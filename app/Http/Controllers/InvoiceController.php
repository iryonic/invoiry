<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Item;
use App\Models\Payment;
use App\Services\DynamicFormConfig;
use App\Services\TenantContext;
use Barryvdh\DomPDF\Facade\Pdf;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices / sold items.
     */
    public function index(Request $request): View
    {
        $tenant = TenantContext::get();
        $query = Invoice::where('tenant_id', $tenant->id)->with(['customer', 'items']);

        // Payment status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('payment_status', $request->status);
        }

        // Date range
        if ($request->filled('from_date')) {
            $query->whereDate('invoice_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('invoice_date', '<=', $request->to_date);
        }

        // Search (invoice number, customer name, customer phone, IMEI)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('items', function ($iq) use ($search) {
                        $iq->where('item_name', 'like', "%{$search}%")
                            ->orWhere('imei_1', 'like', "%{$search}%")
                            ->orWhere('imei_2', 'like', "%{$search}%");
                    });
            });
        }

        $invoices = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total_invoices' => Invoice::where('tenant_id', $tenant->id)->count(),
            'total_sales' => Invoice::where('tenant_id', $tenant->id)->sum('grand_total'),
            'total_collected' => Invoice::where('tenant_id', $tenant->id)->sum('amount_paid'),
            'total_due' => Invoice::where('tenant_id', $tenant->id)->whereIn('payment_status', ['partial', 'unpaid'])->get()->sum->amount_due,
        ];

        return view('invoices.index', compact('invoices', 'stats'));
    }

    /**
     * Show the form for creating a new invoice (Sell Items).
     */
    public function create(): View
    {
        $tenant = TenantContext::get();

        // In-stock devices for the quick-picker dropdown
        $inStockItems = Item::where('tenant_id', $tenant->id)
            ->inStock()
            ->orderBy('item_name')
            ->get();

        $customers = Customer::where('tenant_id', $tenant->id)
            ->orderBy('name')
            ->take(50)
            ->get();

        $nextInvoiceNumber = $tenant->invoice_prefix.str_pad((string) $tenant->invoice_counter, 5, '0', STR_PAD_LEFT);

        return view('invoices.create', compact('tenant', 'inStockItems', 'customers', 'nextInvoiceNumber'));
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $tenant = TenantContext::get();

        $isPhoneRequired = DynamicFormConfig::isFieldRequired($tenant, 'invoice', 'customer_phone');
        $isAddressRequired = DynamicFormConfig::isFieldRequired($tenant, 'invoice', 'customer_address');
        $isDateRequired = DynamicFormConfig::isFieldRequired($tenant, 'invoice', 'invoice_date');

        $validated = $request->validate([
            'invoice_date' => [$isDateRequired ? 'required' : 'nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'payment_method' => ['nullable', 'in:cash,upi,bank,mixed'],
            'is_gst' => ['nullable', 'boolean'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_tax_inclusive' => ['nullable', 'boolean'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],

            // Customer Details
            'customer_id' => ['nullable', 'exists:customers,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => [$isPhoneRequired ? 'required' : 'nullable', 'string', 'max:20'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_adhar' => ['nullable', 'string', 'max:30'],
            'customer_address' => [$isAddressRequired ? 'required' : 'nullable', 'string'],
            'customer_state' => ['nullable', 'string', 'max:100'],
            'customer_state_code' => ['nullable', 'string', 'max:10'],

            // Line Items
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['nullable', 'exists:items,id'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.storage' => ['nullable', 'string', 'max:50'],
            'items.*.color' => ['nullable', 'string', 'max:50'],
            'items.*.specs' => ['nullable', 'string'],
            'items.*.imei_1' => ['nullable', 'string', 'max:50'],
            'items.*.imei_2' => ['nullable', 'string', 'max:50'],
            'items.*.serial_number' => ['nullable', 'string', 'max:100'],
            'items.*.selling_price' => ['required', 'numeric', 'min:0'],
            'items.*.warranty_status' => ['nullable', 'string', 'in:no_warranty,store_warranty,company_warranty'],
            'items.*.warranty_expiry_date' => ['nullable', 'date'],
            'items.*.notes' => ['nullable', 'string'],
        ]);

        $validated['invoice_date'] = $validated['invoice_date'] ?? now()->toDateString();
        $validated['payment_method'] = $validated['payment_method'] ?? 'cash';

        try {
            return DB::transaction(function () use ($tenant, $validated, $request) {
                // 1. Resolve or create Customer
                $customer = null;
                if (! empty($validated['customer_id'])) {
                    $customer = Customer::where('tenant_id', $tenant->id)->find($validated['customer_id']);
                }

                if (! $customer) {
                    if (! empty($validated['customer_phone'])) {
                        $customer = Customer::firstOrCreate(
                            [
                                'tenant_id' => $tenant->id,
                                'phone' => $validated['customer_phone'],
                            ],
                            [
                                'name' => $validated['customer_name'],
                                'email' => $validated['customer_email'] ?? null,
                                'id_proof_number' => $validated['customer_adhar'] ?? null,
                                'address' => $validated['customer_address'] ?? null,
                                'state' => $validated['customer_state'] ?? $tenant->state,
                                'state_code' => $validated['customer_state_code'] ?? $tenant->state_code,
                            ]
                        );
                    } else {
                        $customer = Customer::create([
                            'tenant_id' => $tenant->id,
                            'name' => $validated['customer_name'],
                            'phone' => null,
                            'email' => $validated['customer_email'] ?? null,
                            'id_proof_number' => $validated['customer_adhar'] ?? null,
                            'address' => $validated['customer_address'] ?? null,
                            'state' => $validated['customer_state'] ?? $tenant->state,
                            'state_code' => $validated['customer_state_code'] ?? $tenant->state_code,
                        ]);
                    }
                } elseif (! empty($validated['customer_state_code']) && $customer->state_code !== $validated['customer_state_code']) {
                    $customer->update([
                        'state' => $validated['customer_state'] ?? $customer->state,
                        'state_code' => $validated['customer_state_code'],
                    ]);
                }

                // 2. GST Configuration & Rates
                $isGst = ! empty($validated['is_gst']);
                $taxRate = $isGst ? (float) ($validated['tax_rate'] ?? $tenant->default_gst_rate ?? 18.00) : 0.00;
                $isTaxInclusive = $isGst && ($request->has('is_tax_inclusive') ? $request->boolean('is_tax_inclusive') : (bool) ($tenant->is_tax_inclusive ?? true));
                $taxRateFactor = $taxRate / 100.0;

                // Raw sum of item selling prices
                $rawItemsTotal = 0;
                foreach ($validated['items'] as $itemInput) {
                    $rawItemsTotal += (float) $itemInput['selling_price'];
                }

                $discount = (float) ($validated['discount'] ?? 0);
                $effectiveGross = max(0, $rawItemsTotal - $discount);

                if ($isGst && $taxRate > 0) {
                    if ($isTaxInclusive) {
                        // Tax Inclusive (MRP): Selling price includes tax
                        $subtotal = round($effectiveGross / (1 + $taxRateFactor), 2);
                        $taxAmount = round($effectiveGross - $subtotal, 2);
                        $grandTotal = $effectiveGross;
                    } else {
                        // Tax Exclusive: GST is added on top of base price
                        $subtotal = $effectiveGross;
                        $taxAmount = round($subtotal * $taxRateFactor, 2);
                        $grandTotal = round($subtotal + $taxAmount, 2);
                    }
                } else {
                    $subtotal = $effectiveGross;
                    $taxAmount = 0.00;
                    $grandTotal = $effectiveGross;
                }

                // Check Inter-State vs Intra-State (CGST + SGST vs IGST)
                $storeStateCode = trim((string) ($tenant->state_code ?? ''));
                $custStateCode = trim((string) ($customer->state_code ?? ''));
                $isInterState = $isGst && ($storeStateCode !== '' && $custStateCode !== '' && $storeStateCode !== $custStateCode);

                if ($isGst && $taxAmount > 0) {
                    if ($isInterState) {
                        $igst = $taxAmount;
                        $cgst = 0.00;
                        $sgst = 0.00;
                    } else {
                        $cgst = round($taxAmount / 2, 2);
                        $sgst = round($taxAmount - $cgst, 2);
                        $igst = 0.00;
                    }
                } else {
                    $cgst = 0.00;
                    $sgst = 0.00;
                    $igst = 0.00;
                }

                // Line items data preparation
                $itemsData = [];
                foreach ($validated['items'] as $itemInput) {
                    $unitPrice = (float) $itemInput['selling_price'];

                    // Check purchase cost if linked to inventory Item
                    $purchaseCost = 0.00;
                    $stockItem = null;
                    if (! empty($itemInput['item_id'])) {
                        $stockItem = Item::where('tenant_id', $tenant->id)->find($itemInput['item_id']);
                        if ($stockItem) {
                            if ($stockItem->status === 'sold') {
                                throw new DomainException("Device '{$stockItem->item_name}' (IMEI: {$stockItem->imei_1}) has already been sold on another invoice.");
                            }
                            $purchaseCost = (float) $stockItem->purchase_price;
                        }
                    }

                    // Line item tax allocation
                    $itemTax = 0.00;
                    if ($isGst && $taxRate > 0) {
                        if ($isTaxInclusive) {
                            $itemBase = round($unitPrice / (1 + $taxRateFactor), 2);
                            $itemTax = round($unitPrice - $itemBase, 2);
                        } else {
                            $itemTax = round($unitPrice * $taxRateFactor, 2);
                        }
                    }

                    $itemsData[] = [
                        'item_id' => $stockItem?->id,
                        'item_name' => $itemInput['item_name'],
                        'storage' => $itemInput['storage'] ?? $stockItem?->storage,
                        'color' => $itemInput['color'] ?? $stockItem?->color,
                        'specs' => $itemInput['specs'] ?? $stockItem?->specs,
                        'imei_1' => $itemInput['imei_1'] ?? $stockItem?->imei_1,
                        'imei_2' => $itemInput['imei_2'] ?? $stockItem?->imei_2,
                        'serial_number' => $itemInput['serial_number'] ?? $stockItem?->serial_number,
                        'hsn_sac' => '8517',
                        'quantity' => 1,
                        'unit_price' => $unitPrice,
                        'purchase_cost' => $purchaseCost,
                        'tax_rate' => $taxRate,
                        'tax_amount' => $itemTax,
                        'total_price' => $isTaxInclusive ? $unitPrice : round($unitPrice + $itemTax, 2),
                        'warranty_status' => $itemInput['warranty_status'] ?? 'no_warranty',
                        'warranty_expiry_date' => $itemInput['warranty_expiry_date'] ?? null,
                        'notes' => $itemInput['notes'] ?? null,
                    ];
                }

                $amountPaid = isset($validated['amount_paid']) && $validated['amount_paid'] !== '' ? (float) $validated['amount_paid'] : $grandTotal;

                $paymentStatus = 'paid';
                if ($amountPaid <= 0) {
                    $paymentStatus = 'unpaid';
                } elseif ($amountPaid < $grandTotal) {
                    $paymentStatus = 'partial';
                }

                $invoiceNumber = $tenant->generateNextInvoiceNumber();

                // 3. Create Invoice
                $invoice = Invoice::create([
                    'tenant_id' => $tenant->id,
                    'customer_id' => $customer->id,
                    'user_id' => auth()->id(),
                    'invoice_number' => $invoiceNumber,
                    'invoice_date' => $validated['invoice_date'],
                    'due_date' => $validated['due_date'] ?? null,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'tax_amount' => $taxAmount,
                    'cgst_amount' => $cgst,
                    'sgst_amount' => $sgst,
                    'igst_amount' => $igst,
                    'grand_total' => $grandTotal,
                    'amount_paid' => $amountPaid,
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => $paymentStatus,
                    'is_gst' => $isGst,
                    'tax_rate' => $taxRate,
                    'is_tax_inclusive' => $isTaxInclusive,
                    'notes' => $validated['notes'] ?? null,
                ]);

                // 4. Create Invoice Items and mark inventory items as SOLD
                foreach ($itemsData as $data) {
                    $data['invoice_id'] = $invoice->id;
                    $invoiceItem = InvoiceItem::create($data);

                    if (! empty($data['item_id'])) {
                        Item::where('id', $data['item_id'])->update([
                            'status' => 'sold',
                        ]);
                    }
                }

                // 5. Create Payment record if amount was paid
                if ($amountPaid > 0) {
                    Payment::create([
                        'tenant_id' => $tenant->id,
                        'invoice_id' => $invoice->id,
                        'customer_id' => $customer->id,
                        'amount' => $amountPaid,
                        'payment_method' => $validated['payment_method'],
                        'payment_date' => $validated['invoice_date'],
                        'notes' => 'Payment at checkout',
                    ]);
                }

                return redirect()->route('invoices.show', $invoice)->with('success', "Invoice {$invoiceNumber} created successfully!");
            });
        } catch (DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (Throwable $e) {
            Log::error('Invoice creation failed: '.$e->getMessage(), [
                'tenant_id' => $tenant->id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->with('error', 'Unable to complete invoice billing. Please verify item inventory details and try again.');
        }
    }

    /**
     * Display the digital interactive bill view.
     */
    public function show(Invoice $invoice): View
    {
        $tenant = TenantContext::get();
        abort_if($invoice->tenant_id !== $tenant->id, 403);

        $invoice->load(['customer', 'items', 'payments', 'tenant']);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Download or stream the official GST Tax Invoice PDF.
     */
    public function pdf(Invoice $invoice): Response|RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_if($invoice->tenant_id !== $tenant->id, 403);

        $invoice->load(['customer', 'items', 'tenant']);

        try {
            $pdf = Pdf::loadView('invoices.pdf', compact('invoice'))
                ->setPaper('a4', 'portrait')
                ->setOption([
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'defaultFont' => 'DejaVu Sans',
                ]);

            return $pdf->download("{$invoice->invoice_number}.pdf");
        } catch (Throwable $e) {
            Log::error("PDF Generation failed for invoice {$invoice->id}: ".$e->getMessage());

            return back()->with('error', 'Unable to render downloadable PDF at this time. Please use the Print View button to view or print the bill.');
        }
    }

    /**
     * Direct print-ready browser view with automated window.print() prompt.
     */
    public function print(Invoice $invoice): View
    {
        $tenant = TenantContext::get();
        abort_if($invoice->tenant_id !== $tenant->id, 403);

        $invoice->load(['customer', 'items', 'tenant']);

        return view('invoices.print', compact('invoice'));
    }

    /**
     * Mobile Bluetooth POS 58mm / 80mm thermal receipt format.
     */
    public function thermal(Invoice $invoice): View
    {
        $tenant = TenantContext::get();
        abort_if($invoice->tenant_id !== $tenant->id, 403);

        $invoice->load(['customer', 'items', 'tenant']);

        return view('invoices.thermal', compact('invoice'));
    }

    /**
     * Record an additional payment for an invoice with outstanding balance.
     */
    public function recordPayment(Request $request, Invoice $invoice): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_if($invoice->tenant_id !== $tenant->id, 403);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:'.$invoice->amount_due],
            'payment_method' => ['required', 'in:cash,upi,bank'],
            'reference_id' => ['nullable', 'string', 'max:100'],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($tenant, $invoice, $validated) {
            Payment::create([
                'tenant_id' => $tenant->id,
                'invoice_id' => $invoice->id,
                'customer_id' => $invoice->customer_id,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_id' => $validated['reference_id'] ?? null,
                'payment_date' => $validated['payment_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $newPaid = (float) $invoice->amount_paid + (float) $validated['amount'];
            $newStatus = ($newPaid >= (float) $invoice->grand_total) ? 'paid' : 'partial';

            $invoice->update([
                'amount_paid' => $newPaid,
                'payment_status' => $newStatus,
            ]);
        });

        return back()->with('success', 'Payment recorded successfully!');
    }

    /**
     * Remove the specified invoice from storage and return sold items to stock.
     */
    public function destroy(Invoice $invoice): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_if($invoice->tenant_id !== $tenant->id, 403);

        DB::transaction(function () use ($invoice) {
            // Restore associated stock items to in_stock status
            foreach ($invoice->items as $lineItem) {
                if ($lineItem->item_id) {
                    Item::where('id', $lineItem->item_id)->update(['status' => 'in_stock']);
                }
            }

            $invoice->delete();
        });

        return redirect()->route('invoices.index')->with('success', "Invoice {$invoice->invoice_number} deleted and stock restored.");
    }
}
