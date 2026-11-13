<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Payment;
use App\Services\DynamicFormConfig;
use App\Services\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers with Khata / credit ledger.
     */
    public function index(Request $request): View
    {
        $tenant = TenantContext::get();
        $query = Customer::where('tenant_id', $tenant->id)
            ->withCount('invoices')
            ->withSum('invoices', 'grand_total')
            ->withSum('invoices', 'amount_paid');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(15)->withQueryString();

        return view('customers.index', compact('customers'));
    }

    /**
     * Display customer profile, invoice history, and credit balance.
     */
    public function show(Customer $customer): View
    {
        $tenant = TenantContext::get();
        abort_if($customer->tenant_id !== $tenant->id, 403);

        $customer->load([
            'invoices' => fn ($q) => $q->orderByDesc('invoice_date')->orderByDesc('id')->with('items'),
            'payments' => fn ($q) => $q->orderByDesc('payment_date')->orderByDesc('id')->with('invoice'),
        ]);

        return view('customers.show', compact('customer', 'tenant'));
    }

    /**
     * Quick store new customer.
     */
    public function store(Request $request): RedirectResponse
    {
        $tenant = TenantContext::get();
        $isPhoneRequired = DynamicFormConfig::isFieldRequired($tenant, 'customer', 'phone');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => [$isPhoneRequired ? 'required' : 'nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'gstin' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['tenant_id'] = $tenant->id;
        $customer = Customer::create($validated);

        AuditLog::record('customer.created', "Customer {$customer->name} created", $tenant->id);

        return redirect()->route('customers.index')->with('success', 'Customer profile created successfully.');
    }

    /**
     * Update customer profile.
     */
    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_if($customer->tenant_id !== $tenant->id, 403);

        $isPhoneRequired = DynamicFormConfig::isFieldRequired($tenant, 'customer', 'phone');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => [$isPhoneRequired ? 'required' : 'nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'gstin' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string'],
        ]);

        $customer->update($validated);
        AuditLog::record('customer.updated', "Updated profile for customer {$customer->name}", $tenant->id);

        return back()->with('success', 'Customer profile updated successfully.');
    }

    /**
     * Record Khata balance payment / credit towards customer's dues.
     */
    public function recordKhataPayment(Request $request, Customer $customer): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_if($customer->tenant_id !== $tenant->id, 403);

        $pending = $customer->pendingBalance();
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,upi,bank'],
            'payment_date' => ['required', 'date'],
            'reference_id' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $remainingAmount = (float) $validated['amount'];

        DB::transaction(function () use ($tenant, $customer, $validated, &$remainingAmount) {
            // Settle unpaid / partial invoices starting from oldest
            $dueInvoices = $customer->invoices()
                ->whereIn('payment_status', ['unpaid', 'partial'])
                ->orderBy('invoice_date')
                ->orderBy('id')
                ->get();

            foreach ($dueInvoices as $inv) {
                if ($remainingAmount <= 0) {
                    break;
                }

                $due = max(0, (float) $inv->grand_total - (float) $inv->amount_paid);
                if ($due <= 0) {
                    continue;
                }

                $payForThis = min($remainingAmount, $due);

                Payment::create([
                    'tenant_id' => $tenant->id,
                    'invoice_id' => $inv->id,
                    'customer_id' => $customer->id,
                    'amount' => $payForThis,
                    'payment_method' => $validated['payment_method'],
                    'reference_id' => $validated['reference_id'] ?? null,
                    'payment_date' => $validated['payment_date'],
                    'notes' => $validated['notes'] ? $validated['notes'].' (Khata Settlement)' : 'Khata Settlement',
                ]);

                $newPaid = (float) $inv->amount_paid + $payForThis;
                $inv->update([
                    'amount_paid' => $newPaid,
                    'payment_status' => ($newPaid >= (float) $inv->grand_total) ? 'paid' : 'partial',
                ]);

                $remainingAmount -= $payForThis;
            }

            // If there's an overpayment or no due invoice, record customer unallocated credit
            if ($remainingAmount > 0) {
                Payment::create([
                    'tenant_id' => $tenant->id,
                    'invoice_id' => null,
                    'customer_id' => $customer->id,
                    'amount' => $remainingAmount,
                    'payment_method' => $validated['payment_method'],
                    'reference_id' => $validated['reference_id'] ?? null,
                    'payment_date' => $validated['payment_date'],
                    'notes' => $validated['notes'] ?? 'Khata Advance Deposit',
                ]);
            }
        });

        AuditLog::record('customer.payment_recorded', "Recorded Khata payment of ₹{$validated['amount']} for {$customer->name}", $tenant->id);

        return back()->with('success', 'Payment of ₹'.number_format($validated['amount'], 2).' successfully credited to customer Khata.');
    }

    /**
     * Reusable Bulk Action on Customers.
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $tenant = TenantContext::get() ?? auth()->user()->tenant;

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:customers,id'],
            'action' => ['required', 'string', 'in:delete'],
        ]);

        if ($validated['action'] === 'delete' && ! auth()->user()->hasPermission('customers.delete')) {
            abort(403, 'You do not have permission to delete customer records.');
        }

        $customers = Customer::where('tenant_id', $tenant->id)
            ->whereIn('id', $validated['ids'])
            ->withCount('invoices')
            ->get();

        if ($customers->isEmpty()) {
            return back()->with('error', 'No matching customer records found for this action.');
        }

        $deletedCount = 0;
        $skippedCount = 0;

        foreach ($customers as $customer) {
            if ($customer->invoices_count > 0) {
                $skippedCount++;

                continue;
            }
            $customer->delete();
            $deletedCount++;
        }

        AuditLog::record(
            'customer.bulk_action',
            "Performed bulk {$validated['action']} on {$deletedCount} customer(s). Skipped: {$skippedCount}.",
            $tenant->id
        );

        $msg = "Successfully deleted {$deletedCount} customer(s).";
        if ($skippedCount > 0) {
            $msg .= " ({$skippedCount} customer(s) with billing history were protected from deletion).";
        }

        return back()->with('success', $msg);
    }
}
