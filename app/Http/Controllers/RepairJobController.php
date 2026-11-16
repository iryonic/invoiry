<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\RepairJob;
use App\Models\RepairJobItem;
use App\Models\SparePart;
use App\Models\SparePartStockMovement;
use App\Models\User;
use App\Services\TenantContext;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class RepairJobController extends Controller
{
    /**
     * Display a listing of repair jobs with filtering and stats.
     */
    public function index(Request $request): View
    {
        $tenant = TenantContext::get();
        abort_unless($tenant, 403, 'Tenant context not found.');

        $query = RepairJob::where('tenant_id', $tenant->id)
            ->with(['customer', 'technician']);

        // Status filter
        $status = $request->get('status', 'all');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Technician filter
        if ($request->filled('technician_id') && $request->technician_id !== 'all') {
            $query->where('assigned_to', $request->technician_id);
        }

        // Search query (Job number, Customer Name/Phone, Device Brand/Model, IMEI)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('job_number', 'like', "%{$search}%")
                    ->orWhere('device_brand', 'like', "%{$search}%")
                    ->orWhere('device_model', 'like', "%{$search}%")
                    ->orWhere('imei', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $jobs = $query->latest()->paginate(15)->withQueryString();

        $technicians = User::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $stats = [
            'total_jobs' => RepairJob::where('tenant_id', $tenant->id)->count(),
            'active_jobs' => RepairJob::where('tenant_id', $tenant->id)->whereNotIn('status', ['delivered', 'cancelled'])->count(),
            'in_progress' => RepairJob::where('tenant_id', $tenant->id)->where('status', 'in_progress')->count(),
            'completed' => RepairJob::where('tenant_id', $tenant->id)->where('status', 'completed')->count(),
            'delivered' => RepairJob::where('tenant_id', $tenant->id)->where('status', 'delivered')->count(),
            'pending_due' => (float) RepairJob::where('tenant_id', $tenant->id)->whereNotIn('status', ['cancelled'])->selectRaw('SUM(total_amount - amount_paid) as pending')->value('pending'),
        ];

        $statuses = RepairJob::getStatuses();

        return view('repairs.index', compact('tenant', 'jobs', 'technicians', 'stats', 'status', 'statuses'));
    }

    /**
     * Show form to register a new repair request.
     */
    public function create(Request $request): View
    {
        $tenant = TenantContext::get();
        abort_unless($tenant, 403, 'Tenant context not found.');

        $customers = Customer::where('tenant_id', $tenant->id)->orderBy('name')->get();
        $technicians = User::where('tenant_id', $tenant->id)->where('is_active', true)->orderBy('name')->get();
        $selectedCustomerId = $request->get('customer_id');

        return view('repairs.create', compact('tenant', 'customers', 'technicians', 'selectedCustomerId'));
    }

    /**
     * Store new customer repair request.
     */
    public function store(Request $request): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_unless($tenant, 403, 'Tenant context not found.');

        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'new_customer_name' => ['nullable', 'string', 'max:255'],
            'new_customer_phone' => ['nullable', 'string', 'max:20'],
            'new_customer_email' => ['nullable', 'email', 'max:255'],
            'device_brand' => ['required', 'string', 'max:100'],
            'device_model' => ['required', 'string', 'max:100'],
            'imei' => ['nullable', 'string', 'max:50'],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'device_color' => ['nullable', 'string', 'max:50'],
            'device_password' => ['nullable', 'string', 'max:100'],
            'device_condition' => ['nullable', 'string'],
            'accessories_received' => ['nullable', 'string', 'max:255'],
            'reported_problem' => ['required', 'string'],
            'diagnosis_notes' => ['nullable', 'string'],
            'technician_notes' => ['nullable', 'string'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'priority' => ['nullable', 'in:low,normal,high,urgent'],
            'estimated_cost' => ['nullable', 'numeric', 'min:0'],
            'expected_completion_date' => ['nullable', 'date'],
            'advance_payment' => ['nullable', 'numeric', 'min:0'],
            'advance_paid' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:50'],
        ]);

        try {
            $repairJob = DB::transaction(function () use ($tenant, $validated) {
                // 1. Resolve or create customer
                $customerId = $validated['customer_id'] ?? null;
                if (! $customerId) {
                    if (empty($validated['new_customer_name'])) {
                        throw new DomainException('Please either select an existing customer or provide a customer name.');
                    }
                    $customer = Customer::create([
                        'tenant_id' => $tenant->id,
                        'name' => trim($validated['new_customer_name']),
                        'phone' => ! empty($validated['new_customer_phone']) ? trim($validated['new_customer_phone']) : null,
                        'email' => ! empty($validated['new_customer_email']) ? trim($validated['new_customer_email']) : null,
                    ]);
                    $customerId = $customer->id;
                }

                // 2. Generate unique job number
                $jobNumber = $tenant->generateNextRepairJobNumber();

                $estimatedCost = (float) ($validated['estimated_cost'] ?? 0.00);
                $advancePaid = (float) ($validated['advance_paid'] ?? $validated['advance_payment'] ?? 0.00);

                // 3. Create Repair Job
                $job = RepairJob::create([
                    'tenant_id' => $tenant->id,
                    'customer_id' => $customerId,
                    'assigned_to' => $validated['assigned_to'] ?? null,
                    'created_by' => auth()->id(),
                    'job_number' => $jobNumber,
                    'device_brand' => $validated['device_brand'],
                    'device_model' => $validated['device_model'],
                    'imei' => ! empty($validated['imei']) ? trim($validated['imei']) : null,
                    'serial_number' => ! empty($validated['serial_number']) ? trim($validated['serial_number']) : null,
                    'device_color' => $validated['device_color'] ?? null,
                    'device_password' => $validated['device_password'] ?? null,
                    'device_condition' => $validated['device_condition'] ?? null,
                    'accessories_received' => $validated['accessories_received'] ?? null,
                    'reported_problem' => $validated['reported_problem'],
                    'diagnosis_notes' => $validated['diagnosis_notes'] ?? null,
                    'technician_notes' => $validated['technician_notes'] ?? null,
                    'status' => RepairJob::STATUS_RECEIVED,
                    'priority' => $validated['priority'] ?? 'normal',
                    'estimated_cost' => $estimatedCost,
                    'labour_charge' => 0.00,
                    'parts_total' => 0.00,
                    'additional_charges' => 0.00,
                    'discount' => 0.00,
                    'tax_amount' => 0.00,
                    'total_amount' => $estimatedCost > 0 ? $estimatedCost : 0.00,
                    'amount_paid' => $advancePaid,
                    'expected_completion_date' => $validated['expected_completion_date'] ?? null,
                ]);

                // 4. Log creation activity
                $job->addActivity(
                    type: 'created',
                    notes: "Device intake recorded. Reported issue: {$job->reported_problem}",
                    fromStatus: null,
                    toStatus: RepairJob::STATUS_RECEIVED
                );

                // 5. Record advance payment if provided
                if ($advancePaid > 0) {
                    Payment::create([
                        'tenant_id' => $tenant->id,
                        'invoice_id' => null,
                        'repair_job_id' => $job->id,
                        'customer_id' => $customerId,
                        'amount' => $advancePaid,
                        'payment_method' => $validated['payment_method'] ?? 'cash',
                        'payment_date' => now()->toDateString(),
                        'notes' => "Advance deposit received for Repair Job #{$jobNumber}",
                    ]);

                    $job->addActivity(
                        type: 'payment_recorded',
                        notes: 'Advance deposit received: ₹'.number_format($advancePaid, 2).' via '.($validated['payment_method'] ?? 'cash')
                    );
                }

                return $job;
            });

            AuditLog::record('repair_job.created', "Registered new Repair Job #{$repairJob->job_number} for {$repairJob->device_brand} {$repairJob->device_model}", $tenant->id);

            return redirect()->route('repairs.show', $repairJob)->with('success', "Repair Job #{$repairJob->job_number} created successfully.");
        } catch (DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('RepairJob store failed: '.$e->getMessage());

            return back()->withInput()->with('error', 'Unable to create repair job. Please check inputs and try again.');
        }
    }

    /**
     * Display comprehensive details, parts used, billing, and timeline for a repair job.
     */
    public function show(RepairJob $repairJob): View
    {
        $tenant = TenantContext::get();
        abort_unless($tenant && $repairJob->tenant_id === $tenant->id, 403);

        $repairJob->load([
            'customer',
            'technician',
            'creator',
            'items.sparePart',
            'activities.user',
            'payments',
            'invoices',
        ]);

        $availableParts = SparePart::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->inStock()
            ->orderBy('name')
            ->get();

        $technicians = User::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $statuses = RepairJob::getStatuses();

        return view('repairs.show', compact('tenant', 'repairJob', 'availableParts', 'technicians', 'statuses'));
    }

    /**
     * Show form to edit repair job details.
     */
    public function edit(RepairJob $repairJob): View
    {
        $tenant = TenantContext::get();
        abort_unless($tenant && $repairJob->tenant_id === $tenant->id, 403);

        $customers = Customer::where('tenant_id', $tenant->id)->orderBy('name')->get();
        $technicians = User::where('tenant_id', $tenant->id)->where('is_active', true)->orderBy('name')->get();

        return view('repairs.edit', compact('tenant', 'repairJob', 'customers', 'technicians'));
    }

    /**
     * Update basic repair job information.
     */
    public function update(Request $request, RepairJob $repairJob): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_unless($tenant && $repairJob->tenant_id === $tenant->id, 403);

        $validated = $request->validate([
            'device_brand' => ['required', 'string', 'max:100'],
            'device_model' => ['required', 'string', 'max:100'],
            'imei' => ['nullable', 'string', 'max:50'],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'device_color' => ['nullable', 'string', 'max:50'],
            'device_password' => ['nullable', 'string', 'max:100'],
            'device_condition' => ['nullable', 'string'],
            'accessories_received' => ['nullable', 'string', 'max:255'],
            'reported_problem' => ['required', 'string'],
            'diagnosis_notes' => ['nullable', 'string'],
            'technician_notes' => ['nullable', 'string'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'priority' => ['nullable', 'in:low,normal,high,urgent'],
            'estimated_cost' => ['nullable', 'numeric', 'min:0'],
            'expected_completion_date' => ['nullable', 'date'],
            'warranty_days' => ['nullable', 'integer', 'min:0'],
        ]);

        $repairJob->update([
            'device_brand' => $validated['device_brand'],
            'device_model' => $validated['device_model'],
            'imei' => ! empty($validated['imei']) ? trim($validated['imei']) : null,
            'serial_number' => ! empty($validated['serial_number']) ? trim($validated['serial_number']) : null,
            'device_color' => $validated['device_color'] ?? null,
            'device_password' => $validated['device_password'] ?? null,
            'device_condition' => $validated['device_condition'] ?? null,
            'accessories_received' => $validated['accessories_received'] ?? null,
            'reported_problem' => $validated['reported_problem'],
            'diagnosis_notes' => $validated['diagnosis_notes'] ?? null,
            'technician_notes' => $validated['technician_notes'] ?? null,
            'assigned_to' => $validated['assigned_to'] ?? null,
            'priority' => $validated['priority'] ?? 'normal',
            'estimated_cost' => (float) ($validated['estimated_cost'] ?? 0.00),
            'expected_completion_date' => $validated['expected_completion_date'] ?? null,
            'warranty_days' => (int) ($validated['warranty_days'] ?? 0),
        ]);

        $repairJob->addActivity(
            type: 'details_updated',
            notes: 'Repair job details, problem description, or technician notes updated'
        );

        AuditLog::record('repair_job.updated', "Updated details for Repair Job #{$repairJob->job_number}", $tenant->id);

        return redirect()->route('repairs.show', $repairJob)->with('success', "Repair Job #{$repairJob->job_number} updated successfully.");
    }

    /**
     * Update repair job workflow status.
     */
    public function updateStatus(Request $request, RepairJob $repairJob): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_unless($tenant && $repairJob->tenant_id === $tenant->id, 403);

        $validated = $request->validate([
            'status' => ['required', 'in:received,under_diagnosis,awaiting_approval,waiting_for_parts,in_progress,completed,delivered,cancelled'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $fromStatus = $repairJob->status;
        $toStatus = $validated['status'];

        if ($fromStatus === $toStatus) {
            return back()->with('info', "Repair job is already in status '{$repairJob->status_label}'.");
        }

        DB::transaction(function () use ($repairJob, $fromStatus, $toStatus, $validated) {
            $updateData = ['status' => $toStatus];

            if ($toStatus === RepairJob::STATUS_COMPLETED && ! $repairJob->completed_at) {
                $updateData['completed_at'] = now();
            }

            if ($toStatus === RepairJob::STATUS_DELIVERED && ! $repairJob->delivered_at) {
                $updateData['delivered_at'] = now();
                if (! $repairJob->completed_at) {
                    $updateData['completed_at'] = now();
                }
            }

            // If cancelled, restore any consumed parts to inventory
            if ($toStatus === RepairJob::STATUS_CANCELLED) {
                foreach ($repairJob->items as $item) {
                    if ($item->spare_part_id && $item->is_stock_deducted) {
                        $part = SparePart::lockForUpdate()->find($item->spare_part_id);
                        if ($part) {
                            $before = $part->stock_quantity;
                            $after = $before + $item->quantity;
                            $part->stock_quantity = $after;
                            $part->save();

                            SparePartStockMovement::create([
                                'tenant_id' => $repairJob->tenant_id,
                                'spare_part_id' => $part->id,
                                'user_id' => auth()->id(),
                                'repair_job_id' => $repairJob->id,
                                'type' => 'repair_restored',
                                'quantity_change' => $item->quantity,
                                'quantity_before' => $before,
                                'quantity_after' => $after,
                                'notes' => "Stock restored due to cancellation of Repair Job #{$repairJob->job_number}",
                            ]);

                            $item->update(['is_stock_deducted' => false]);
                        }
                    }
                }
            }

            $repairJob->update($updateData);

            $repairJob->addActivity(
                type: 'status_changed',
                notes: $validated['notes'] ?: "Status advanced to '".ucfirst(str_replace('_', ' ', $toStatus))."'",
                fromStatus: $fromStatus,
                toStatus: $toStatus
            );
        });

        AuditLog::record('repair_job.status_updated', "Repair Job #{$repairJob->job_number} status updated: {$fromStatus} -> {$toStatus}", $tenant->id);

        return back()->with('success', "Repair job status updated to '{$repairJob->fresh()->status_label}'.");
    }

    /**
     * Assign or reassign technician.
     */
    public function assignTechnician(Request $request, RepairJob $repairJob): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_unless($tenant && $repairJob->tenant_id === $tenant->id, 403);

        $validated = $request->validate([
            'assigned_to' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $tech = $validated['assigned_to'] ? User::find($validated['assigned_to']) : null;
        $repairJob->update(['assigned_to' => $validated['assigned_to']]);

        $repairJob->addActivity(
            type: 'technician_assigned',
            notes: $tech ? "Assigned to technician {$tech->name}. {$validated['notes']}" : 'Technician unassigned.'
        );

        return back()->with('success', $tech ? "Assigned to {$tech->name} successfully." : 'Technician unassigned.');
    }

    /**
     * Add spare part or custom component to the repair job and confirm consumption with stock deduction.
     */
    public function addPart(Request $request, RepairJob $repairJob): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_unless($tenant && $repairJob->tenant_id === $tenant->id, 403);

        $validated = $request->validate([
            'spare_part_id' => ['nullable', 'exists:spare_parts,id'],
            'custom_item_name' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($tenant, $repairJob, $validated) {
                $qty = (int) $validated['quantity'];
                $unitPrice = (float) $validated['unit_price'];
                $totalPrice = round($qty * $unitPrice, 2);

                $sparePartId = $validated['spare_part_id'] ?? null;
                $itemName = $validated['custom_item_name'] ?? 'Spare Part';
                $unitCost = 0.00;
                $isStockDeducted = false;

                if ($sparePartId) {
                    $part = SparePart::where('tenant_id', $tenant->id)->lockForUpdate()->findOrFail($sparePartId);
                    $itemName = $part->name;
                    $unitCost = (float) $part->purchase_price;

                    // Concurrency safe & prevent negative stock
                    if ($part->stock_quantity < $qty) {
                        throw new DomainException("Insufficient stock for '{$part->name}'. Available: {$part->stock_quantity} units, Requested: {$qty} units.");
                    }

                    // Deduct confirmed stock
                    $before = $part->stock_quantity;
                    $after = $before - $qty;
                    $part->stock_quantity = $after;
                    $part->save();

                    SparePartStockMovement::create([
                        'tenant_id' => $tenant->id,
                        'spare_part_id' => $part->id,
                        'user_id' => auth()->id(),
                        'repair_job_id' => $repairJob->id,
                        'type' => 'repair_consumed',
                        'quantity_change' => -$qty,
                        'quantity_before' => $before,
                        'quantity_after' => $after,
                        'unit_cost' => $unitCost,
                        'notes' => "Consumed in Repair Job #{$repairJob->job_number}",
                    ]);

                    $isStockDeducted = true;
                }

                RepairJobItem::create([
                    'repair_job_id' => $repairJob->id,
                    'spare_part_id' => $sparePartId,
                    'item_type' => 'part',
                    'item_name' => $itemName,
                    'quantity' => $qty,
                    'unit_cost' => $unitCost,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'is_stock_deducted' => $isStockDeducted,
                ]);

                $repairJob->recalculateTotals();

                $repairJob->addActivity(
                    type: 'part_added',
                    notes: "Added component: '{$itemName}' ({$qty} pcs @ ₹".number_format($unitPrice, 2).')'
                );
            });

            return back()->with('success', 'Component added and stock deducted successfully.');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('RepairJob addPart failed: '.$e->getMessage());

            return back()->with('error', 'Unable to add component to repair job.');
        }
    }

    /**
     * Remove spare part from repair job and restore stock.
     */
    public function removePart(Request $request, RepairJob $repairJob, RepairJobItem $item): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_unless($tenant && $repairJob->tenant_id === $tenant->id && $item->repair_job_id === $repairJob->id, 403);

        try {
            DB::transaction(function () use ($tenant, $repairJob, $item) {
                $itemName = $item->item_name;

                // Restore stock if it was deducted
                if ($item->spare_part_id && $item->is_stock_deducted) {
                    $part = SparePart::where('tenant_id', $tenant->id)->lockForUpdate()->find($item->spare_part_id);
                    if ($part) {
                        $before = $part->stock_quantity;
                        $after = $before + $item->quantity;
                        $part->stock_quantity = $after;
                        $part->save();

                        SparePartStockMovement::create([
                            'tenant_id' => $tenant->id,
                            'spare_part_id' => $part->id,
                            'user_id' => auth()->id(),
                            'repair_job_id' => $repairJob->id,
                            'type' => 'repair_restored',
                            'quantity_change' => $item->quantity,
                            'quantity_before' => $before,
                            'quantity_after' => $after,
                            'unit_cost' => $part->purchase_price,
                            'notes' => "Restored upon removal from Repair Job #{$repairJob->job_number}",
                        ]);
                    }
                }

                $item->delete();
                $repairJob->recalculateTotals();

                $repairJob->addActivity(
                    type: 'part_removed',
                    notes: "Removed component: '{$itemName}' and restored stock."
                );
            });

            return back()->with('success', "Component '{$item->item_name}' removed and stock restored.");
        } catch (\Throwable $e) {
            Log::error('RepairJob removePart failed: '.$e->getMessage());

            return back()->with('error', 'Unable to remove component.');
        }
    }

    /**
     * Update labour charges, additional expenses, discounts, and taxes.
     */
    public function updateCharges(Request $request, RepairJob $repairJob): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_unless($tenant && $repairJob->tenant_id === $tenant->id, 403);

        $validated = $request->validate([
            'labour_charge' => ['required', 'numeric', 'min:0'],
            'additional_charges' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $repairJob->update([
            'labour_charge' => (float) $validated['labour_charge'],
            'additional_charges' => (float) ($validated['additional_charges'] ?? 0.00),
            'discount' => (float) ($validated['discount'] ?? 0.00),
            'tax_amount' => (float) ($validated['tax_amount'] ?? 0.00),
        ]);

        $repairJob->recalculateTotals();

        $repairJob->addActivity(
            type: 'charges_updated',
            notes: 'Labour & service charges updated. Final Payable Amount: ₹'.number_format($repairJob->total_amount, 2)
        );

        return back()->with('success', 'Repair charges and totals recalculated successfully.');
    }

    /**
     * Record a payment (advance, partial, or final) on the repair job.
     */
    public function recordPayment(Request $request, RepairJob $repairJob): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_unless($tenant && $repairJob->tenant_id === $tenant->id, 403);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string', 'in:cash,upi,card,bank_transfer'],
            'payment_date' => ['nullable', 'date'],
            'reference_id' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $amount = (float) $validated['amount'];
        $due = $repairJob->due_amount;

        if ($due > 0 && $amount > $due) {
            return back()->with('error', 'Payment of ₹'.number_format($amount, 2).' exceeds remaining balance of ₹'.number_format($due, 2).'.');
        }

        DB::transaction(function () use ($tenant, $repairJob, $validated, $amount) {
            Payment::create([
                'tenant_id' => $tenant->id,
                'invoice_id' => $repairJob->invoices()->first()?->id,
                'repair_job_id' => $repairJob->id,
                'customer_id' => $repairJob->customer_id,
                'amount' => $amount,
                'payment_method' => $validated['payment_method'],
                'reference_id' => $validated['reference_id'] ?? null,
                'payment_date' => $validated['payment_date'] ?? now()->toDateString(),
                'notes' => $validated['notes'] ?: "Payment for Repair Job #{$repairJob->job_number}",
            ]);

            $newPaid = round((float) $repairJob->amount_paid + $amount, 2);
            $repairJob->amount_paid = $newPaid;
            $repairJob->save();

            // If an invoice exists for this repair, sync payment onto invoice as well
            if ($inv = $repairJob->invoices()->first()) {
                $invAmountPaid = round((float) $inv->amount_paid + $amount, 2);
                $invStatus = ($invAmountPaid >= (float) $inv->grand_total) ? 'paid' : 'partial';
                $inv->update([
                    'amount_paid' => $invAmountPaid,
                    'payment_status' => $invStatus,
                ]);
            }

            $repairJob->addActivity(
                type: 'payment_recorded',
                notes: 'Recorded payment of ₹'.number_format($amount, 2).' via '.strtoupper($validated['payment_method']).'. Balance Due: ₹'.number_format($repairJob->fresh()->due_amount, 2)
            );
        });

        AuditLog::record('repair_job.payment_recorded', "Recorded payment of ₹{$amount} for Repair Job #{$repairJob->job_number}", $tenant->id);

        return back()->with('success', 'Payment of ₹'.number_format($amount, 2).' recorded successfully.');
    }

    /**
     * Generate an official Repair Invoice linking to this job.
     */
    public function generateInvoice(Request $request, RepairJob $repairJob): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_unless($tenant && $repairJob->tenant_id === $tenant->id, 403);

        if ($repairJob->invoices()->exists()) {
            return redirect()->route('invoices.show', $repairJob->invoices()->first())->with('info', "Repair invoice #{$repairJob->invoices()->first()->invoice_number} already generated for this job.");
        }

        try {
            $invoice = DB::transaction(function () use ($tenant, $repairJob) {
                $invoiceNumber = $tenant->generateNextInvoiceNumber();
                $subtotal = (float) $repairJob->parts_total + (float) $repairJob->labour_charge + (float) $repairJob->additional_charges;
                $grandTotal = (float) $repairJob->total_amount;
                $amountPaid = (float) $repairJob->amount_paid;
                $paymentStatus = ($amountPaid >= $grandTotal && $grandTotal > 0) ? 'paid' : ($amountPaid > 0 ? 'partial' : 'unpaid');

                $invoice = Invoice::create([
                    'tenant_id' => $tenant->id,
                    'customer_id' => $repairJob->customer_id,
                    'repair_job_id' => $repairJob->id,
                    'user_id' => auth()->id(),
                    'invoice_number' => $invoiceNumber,
                    'invoice_type' => 'repair',
                    'invoice_date' => now()->toDateString(),
                    'due_date' => now()->toDateString(),
                    'subtotal' => $subtotal,
                    'discount' => (float) $repairJob->discount,
                    'tax_amount' => (float) $repairJob->tax_amount,
                    'grand_total' => $grandTotal,
                    'amount_paid' => $amountPaid,
                    'payment_method' => 'cash',
                    'payment_status' => $paymentStatus,
                    'status' => 'finalized',
                    'is_gst' => (float) $repairJob->tax_amount > 0,
                    'notes' => "Repair Service Invoice for {$repairJob->device_brand} {$repairJob->device_model} (Job #{$repairJob->job_number}). Issue: {$repairJob->reported_problem}",
                ]);

                // Create invoice items for parts used
                foreach ($repairJob->items as $item) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'item_id' => null,
                        'item_name' => '[Repair Part] '.$item->item_name,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'purchase_cost' => $item->unit_cost,
                        'total_price' => $item->total_price,
                        'notes' => "Part used in Repair Job #{$repairJob->job_number}",
                    ]);
                }

                // Add Labour Charge as line item if > 0
                if ((float) $repairJob->labour_charge > 0) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'item_id' => null,
                        'item_name' => 'Technician Labour & Service Charges',
                        'quantity' => 1,
                        'unit_price' => (float) $repairJob->labour_charge,
                        'total_price' => (float) $repairJob->labour_charge,
                        'notes' => 'Service and diagnostic labour',
                    ]);
                }

                // Add Additional Charges as line item if > 0
                if ((float) $repairJob->additional_charges > 0) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'item_id' => null,
                        'item_name' => 'Additional Diagnostic & Workshop Charges',
                        'quantity' => 1,
                        'unit_price' => (float) $repairJob->additional_charges,
                        'total_price' => (float) $repairJob->additional_charges,
                        'notes' => 'Specialist testing and consumables',
                    ]);
                }

                // Link existing payments to this invoice
                Payment::where('repair_job_id', $repairJob->id)->whereNull('invoice_id')->update([
                    'invoice_id' => $invoice->id,
                ]);

                $repairJob->addActivity(
                    type: 'invoice_generated',
                    notes: "Generated official Repair Invoice #{$invoiceNumber}"
                );

                return $invoice;
            });

            AuditLog::record('repair_job.invoice_generated', "Generated invoice #{$invoice->invoice_number} for Repair Job #{$repairJob->job_number}", $tenant->id);

            return redirect()->route('invoices.show', $invoice)->with('success', "Repair Invoice #{$invoice->invoice_number} generated successfully.");
        } catch (\Throwable $e) {
            Log::error('RepairJob generateInvoice failed: '.$e->getMessage());

            return back()->with('error', 'Unable to generate repair invoice: '.$e->getMessage());
        }
    }

    /**
     * Printable Job Sheet / Intake Receipt.
     */
    public function printJobSheet(RepairJob $repairJob): View
    {
        $tenant = TenantContext::get();
        abort_unless($tenant && $repairJob->tenant_id === $tenant->id, 403);

        $repairJob->load(['customer', 'technician', 'items', 'payments']);

        return view('repairs.job-sheet', compact('tenant', 'repairJob'));
    }

    /**
     * Printable Gate Pass / Delivery Receipt.
     */
    public function printDeliveryReceipt(RepairJob $repairJob): View
    {
        $tenant = TenantContext::get();
        abort_unless($tenant && $repairJob->tenant_id === $tenant->id, 403);

        $repairJob->load(['customer', 'technician', 'items', 'payments']);

        return view('repairs.delivery-receipt', compact('tenant', 'repairJob'));
    }

    /**
     * Cancel or delete a repair job.
     */
    public function destroy(RepairJob $repairJob): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_unless($tenant && $repairJob->tenant_id === $tenant->id, 403);

        $jobNum = $repairJob->job_number;

        DB::transaction(function () use ($repairJob) {
            // Restore any consumed spare parts to stock before deleting
            foreach ($repairJob->items as $item) {
                if ($item->spare_part_id && $item->is_stock_deducted) {
                    $part = SparePart::lockForUpdate()->find($item->spare_part_id);
                    if ($part) {
                        $part->stock_quantity += $item->quantity;
                        $part->save();

                        SparePartStockMovement::create([
                            'tenant_id' => $repairJob->tenant_id,
                            'spare_part_id' => $part->id,
                            'user_id' => auth()->id(),
                            'repair_job_id' => null,
                            'type' => 'repair_restored',
                            'quantity_change' => $item->quantity,
                            'quantity_before' => $part->stock_quantity - $item->quantity,
                            'quantity_after' => $part->stock_quantity,
                            'notes' => "Stock restored upon deletion of Repair Job #{$repairJob->job_number}",
                        ]);
                    }
                }
            }

            $repairJob->delete();
        });

        AuditLog::record('repair_job.deleted', "Deleted Repair Job #{$jobNum}", $tenant->id);

        return redirect()->route('repairs.index')->with('success', "Repair Job #{$jobNum} removed and all reserved parts restored.");
    }
}
