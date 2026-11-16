@extends('layouts.app')

@section('title', 'Repair Job #' . $repairJob->job_number . ' · ' . ($tenant->name ?? 'Mobile Repair'))
@section('page-title', 'Repair Job #' . $repairJob->job_number)
@section('page-subtitle', $repairJob->device_brand . ' ' . $repairJob->device_model . ' · Customer: ' . ($repairJob->customer->name ?? 'Walk-in'))

@section('content')
<div class="space-y-6 w-full" style="overflow-x: clip;">

    @php
        $stInfo = $repairJob->status_info;
        $due = $repairJob->due_balance;
    @endphp

    <!-- Top Action & Navigation Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('repairs.index') }}" class="p-2 rounded-lg border border-[#E5E5E5] bg-white hover:bg-neutral-50 text-[#666666] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-xl sm:text-2xl font-bold font-mono text-[#111111]">#{{ $repairJob->job_number }}</span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $stInfo['color'] ?? 'bg-gray-100 text-gray-800' }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                        <span>{{ $stInfo['label'] }}</span>
                    </span>
                    @if($repairJob->priority === 'urgent')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800">URGENT</span>
                    @elseif($repairJob->priority === 'high')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-800">HIGH PRIORITY</span>
                    @endif
                </div>
                <div class="text-xs text-[#666666] mt-0.5">
                    Registered on {{ $repairJob->created_at->format('d M Y, h:i A') }} · Created by {{ $repairJob->creator->name ?? 'Staff' }}
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('repairs.job-sheet', $repairJob) }}" target="_blank" class="btn-secondary px-3.5 py-2 rounded-lg text-xs font-semibold flex items-center gap-1.5 whitespace-nowrap min-h-[40px]">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Print Job Sheet</span>
            </a>

            <a href="{{ route('repairs.delivery-receipt', $repairJob) }}" target="_blank" class="btn-secondary px-3.5 py-2 rounded-lg text-xs font-semibold flex items-center gap-1.5 whitespace-nowrap min-h-[40px]">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Gate Pass / Receipt</span>
            </a>

            @if($repairJob->invoices->count() > 0)
                <a href="{{ route('invoices.show', $repairJob->invoices->first()) }}" class="btn-secondary px-3.5 py-2 rounded-lg text-xs font-semibold flex items-center gap-1.5 whitespace-nowrap min-h-[40px] text-[#079455] border-[#079455]">
                    <svg class="w-4 h-4 text-[#079455]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>View Repair Invoice</span>
                </a>
            @else
                <form method="POST" action="{{ route('repairs.generate-invoice', $repairJob) }}" onsubmit="return confirm('Generate an official tax repair invoice for this job?');">
                    @csrf
                    <button type="submit" class="btn-secondary px-3.5 py-2 rounded-lg text-xs font-semibold flex items-center gap-1.5 whitespace-nowrap min-h-[40px]">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Generate Invoice</span>
                    </button>
                </form>
            @endif

            <a href="{{ route('repairs.edit', $repairJob) }}" class="btn-secondary px-3.5 py-2 rounded-lg text-xs font-semibold flex items-center gap-1.5 whitespace-nowrap min-h-[40px]">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                <span>Edit</span>
            </a>
        </div>
    </div>

    <!-- Main Grid: Left Workshop Details & Right Financial / Status Panel -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Columns -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Customer & Device Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- Customer Details -->
                <div class="card-surface p-5 space-y-3">
                    <div class="flex items-center justify-between border-b border-[#EBEBEB] pb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-[#666666]">Customer Details</span>
                        @if($repairJob->customer)
                            <a href="{{ route('customers.show', $repairJob->customer) }}" class="text-xs font-semibold text-[#0369A1] hover:underline">
                                Full Profile &rarr;
                            </a>
                        @endif
                    </div>
                    @if($repairJob->customer)
                        <div>
                            <h4 class="text-base font-bold text-[#111111]">{{ $repairJob->customer->name }}</h4>
                            <div class="text-xs text-[#666666] font-mono mt-1 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                <span>{{ $repairJob->customer->phone }}</span>
                            </div>
                            @if($repairJob->customer->email)
                                <div class="text-xs text-[#666666] mt-0.5">
                                    {{ $repairJob->customer->email }}
                                </div>
                            @endif
                            @if($repairJob->customer->address)
                                <div class="text-xs text-[#888888] mt-1">
                                    {{ $repairJob->customer->address }}
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-xs text-[#888888] italic">Direct Walk-in customer</p>
                    @endif
                </div>

                <!-- Device Specifications -->
                <div class="card-surface p-5 space-y-3">
                    <div class="flex items-center justify-between border-b border-[#EBEBEB] pb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-[#666666]">Device Intake Spec</span>
                        <span class="text-xs font-mono font-bold text-[#111111]">{{ $repairJob->device_brand }}</span>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-[#111111]">
                            {{ $repairJob->device_brand }} {{ $repairJob->device_model }}
                        </h4>
                        <div class="grid grid-cols-2 gap-2 mt-2 text-xs">
                            @if($repairJob->device_color)
                                <div><span class="text-[#888888]">Colour:</span> <span class="font-medium text-[#111111]">{{ $repairJob->device_color }}</span></div>
                            @endif
                            @if($repairJob->device_password)
                                <div><span class="text-[#888888]">Passcode:</span> <span class="font-mono font-bold text-[#111111]">{{ $repairJob->device_password }}</span></div>
                            @endif
                            @if($repairJob->imei)
                                <div class="col-span-2 font-mono"><span class="text-[#888888]">IMEI:</span> <span class="text-[#111111]">{{ $repairJob->imei }}</span></div>
                            @endif
                            @if($repairJob->serial_number)
                                <div class="col-span-2 font-mono"><span class="text-[#888888]">S/N:</span> <span class="text-[#111111]">{{ $repairJob->serial_number }}</span></div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            <!-- Problem, Condition & Diagnosis Card -->
            <div class="card-surface p-5 space-y-4">
                <h3 class="text-sm font-bold text-[#111111] uppercase tracking-wider border-b border-[#EBEBEB] pb-2">
                    Reported Problem &amp; Physical Intake
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-3 bg-red-50/50 rounded-lg border border-red-100">
                        <span class="text-xs font-bold text-[#DC2626] uppercase">Customer Reported Problem</span>
                        <p class="text-xs sm:text-sm text-[#111111] mt-1 leading-relaxed">
                            {{ $repairJob->reported_problem }}
                        </p>
                    </div>

                    <div class="p-3 bg-neutral-50 rounded-lg border border-neutral-200">
                        <span class="text-xs font-bold text-[#666666] uppercase">Physical Condition at Intake</span>
                        <p class="text-xs sm:text-sm text-[#111111] mt-1 leading-relaxed">
                            {{ $repairJob->device_condition ?: 'No visible physical damage reported at counter.' }}
                        </p>
                    </div>
                </div>

                @if($repairJob->accessories_received)
                    <div class="text-xs">
                        <span class="font-bold text-[#666666]">Accessories Received:</span>
                        <span class="text-[#111111] ml-1">{{ $repairJob->accessories_received }}</span>
                    </div>
                @endif

                @if($repairJob->diagnosis_notes || $repairJob->technician_notes)
                    <div class="pt-2 border-t border-[#EBEBEB] grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($repairJob->diagnosis_notes)
                            <div>
                                <span class="text-xs font-bold text-[#0369A1]">Diagnosis Notes</span>
                                <p class="text-xs text-[#333333] mt-0.5 leading-relaxed">{{ $repairJob->diagnosis_notes }}</p>
                            </div>
                        @endif
                        @if($repairJob->technician_notes)
                            <div>
                                <span class="text-xs font-bold text-[#B54708]">Technician Workbench Notes</span>
                                <p class="text-xs text-[#333333] mt-0.5 leading-relaxed">{{ $repairJob->technician_notes }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Spare Parts Used & Consumption Ledger -->
            <div class="card-surface p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-[#EBEBEB] pb-3">
                    <div>
                        <h3 class="text-base font-bold text-[#111111]">Spare Parts &amp; Hardware Consumed</h3>
                        <p class="text-xs text-[#666666]">Components installed into this device. Deducts live stock from workshop inventory.</p>
                    </div>
                    <button type="button" onclick="document.getElementById('addPartModal').classList.remove('hidden')" class="btn-lime px-3.5 py-2 rounded-lg text-xs font-bold flex items-center gap-1.5 shadow-xs whitespace-nowrap min-h-[38px]">
                        <svg class="w-3.5 h-3.5 text-[#101010]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        <span>+ Add Spare Part</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs sm:text-sm">
                        <thead>
                            <tr class="border-b border-[#EBEBEB] bg-[#FAFAFA] text-[#666666] font-semibold">
                                <th class="py-2.5 px-3">Part Description</th>
                                <th class="py-2.5 px-3 text-center">Qty</th>
                                <th class="py-2.5 px-3 text-right">Unit Price</th>
                                <th class="py-2.5 px-3 text-right">Total</th>
                                <th class="py-2.5 px-3 text-center">Stock Status</th>
                                <th class="py-2.5 px-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EBEBEB] text-[#111111]">
                            @forelse($repairJob->items->where('item_type', 'part') as $item)
                                <tr class="hover:bg-[#F9F9F8] transition">
                                    <td class="py-2.5 px-3">
                                        <div class="font-bold text-[#111111]">{{ $item->item_name }}</div>
                                        @if($item->sparePart)
                                            <div class="text-[11px] text-[#888888] font-mono">
                                                SKU: {{ $item->sparePart->sku ?? 'N/A' }} · Brand: {{ $item->sparePart->brand ?? 'Generic' }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 text-center font-bold">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-mono">
                                        ₹{{ number_format($item->unit_price, 2) }}
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-mono font-bold">
                                        ₹{{ number_format($item->total_price, 2) }}
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        @if($item->is_stock_deducted)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-[#ECFDF3] text-[#027A48]">
                                                Deducted
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700">
                                                Manual
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 text-right">
                                        <form method="POST" action="{{ route('repairs.parts.remove', [$repairJob, $item]) }}" onsubmit="return confirm('Remove this part? Its quantity will be restored to inventory.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-red-600 hover:bg-red-50 rounded transition" title="Remove part & restore stock">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 px-3 text-center text-[#888888] text-xs">
                                        No spare parts installed yet. Click "+ Add Spare Part" to consume components from stock.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($repairJob->parts_total > 0)
                            <tfoot>
                                <tr class="bg-[#FAFAFA] font-bold text-xs border-t border-[#EBEBEB]">
                                    <td colspan="3" class="py-2.5 px-3 text-right">Parts Subtotal:</td>
                                    <td class="py-2.5 px-3 text-right font-mono text-[#111111]">₹{{ number_format($repairJob->parts_total, 2) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Activity & Repair History Timeline -->
            <div class="card-surface p-5 space-y-4">
                <h3 class="text-sm font-bold text-[#111111] uppercase tracking-wider border-b border-[#EBEBEB] pb-2">
                    Repair Job Activity History
                </h3>

                <div class="space-y-3">
                    @forelse($repairJob->activities as $act)
                        <div class="flex items-start gap-3 text-xs">
                            <span class="w-2 h-2 rounded-full bg-[#111111] mt-1.5 flex-shrink-0"></span>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-[#111111]">
                                        {{ ucwords(str_replace('_', ' ', $act->activity_type)) }}
                                        @if($act->from_status && $act->to_status)
                                            <span class="text-[#888888] font-normal">({{ ucfirst($act->from_status) }} &rarr; {{ ucfirst($act->to_status) }})</span>
                                        @endif
                                    </span>
                                    <span class="text-[11px] text-[#888888] font-mono">
                                        {{ $act->created_at->format('d M, h:i A') }}
                                    </span>
                                </div>
                                @if($act->notes)
                                    <p class="text-[#666666] mt-0.5 leading-relaxed">{{ $act->notes }}</p>
                                @endif
                                <div class="text-[10px] text-[#888888] mt-0.5">By: {{ $act->user->name ?? 'System' }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-[#888888] italic">No activity recorded yet.</p>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right 1 Column: Status Management, Technician & Billing Breakdown -->
        <div class="space-y-6">

            <!-- 1. Status Update Card -->
            <div class="card-surface p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-[#EBEBEB] pb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#666666]">Workshop Status</span>
                    <span class="text-xs font-bold font-mono text-[#0369A1]">Live</span>
                </div>

                <form method="POST" action="{{ route('repairs.status', $repairJob) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-[#666666] mb-1">Update Progress</label>
                        <select name="status" class="w-full py-2 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-xs sm:text-sm text-[#111111] font-semibold focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[42px]">
                            @foreach($statuses as $key => $st)
                                <option value="{{ $key }}" {{ $repairJob->status === $key ? 'selected' : '' }}>
                                    {{ $st['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#666666] mb-1">Status Transition Notes</label>
                        <input type="text" name="notes" placeholder="e.g. Screen replaced and undergoing 2hr touch test..." class="w-full py-2 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-xs text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition">
                    </div>

                    <button type="submit" class="w-full btn-secondary py-2 text-xs font-bold rounded-lg flex items-center justify-center gap-1.5 min-h-[38px]">
                        <span>Apply Status Change</span>
                    </button>
                </form>
            </div>

            <!-- 2. Technician Assignment Card -->
            <div class="card-surface p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-[#EBEBEB] pb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#666666]">Assigned Technician</span>
                </div>

                <form method="POST" action="{{ route('repairs.assign-technician', $repairJob) }}" class="space-y-3">
                    @csrf
                    <div>
                        <select name="assigned_to" class="w-full py-2 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-xs sm:text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[42px]">
                            <option value="">-- Unassigned (General Queue) --</option>
                            @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}" {{ $repairJob->assigned_to == $tech->id ? 'selected' : '' }}>
                                    {{ $tech->name }} ({{ ucfirst(str_replace('_', ' ', $tech->role)) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full btn-secondary py-2 text-xs font-bold rounded-lg flex items-center justify-center gap-1.5 min-h-[38px]">
                        <span>Assign Technician</span>
                    </button>
                </form>

                <div class="pt-2 border-t border-[#EBEBEB] space-y-1.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-[#888888]">Target Delivery:</span>
                        <span class="font-bold text-[#111111]">{{ $repairJob->expected_completion_date ? \Carbon\Carbon::parse($repairJob->expected_completion_date)->format('d M Y') : 'Open' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#888888]">Warranty Period:</span>
                        <span class="font-bold text-[#111111]">{{ $repairJob->warranty_days }} days coverage</span>
                    </div>
                </div>
            </div>

            <!-- 3. Billing & Financial Summary -->
            <div class="card-surface p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-[#EBEBEB] pb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#666666]">Repair Charges &amp; Due</span>
                    <button type="button" onclick="document.getElementById('editChargesModal').classList.remove('hidden')" class="text-xs font-bold text-[#0369A1] hover:underline">
                        Edit Charges
                    </button>
                </div>

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-[#666666]">Spare Parts:</span>
                        <span class="font-mono text-[#111111]">₹{{ number_format($repairJob->parts_total, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#666666]">Labour / Service:</span>
                        <span class="font-mono text-[#111111]">₹{{ number_format($repairJob->labour_charge, 2) }}</span>
                    </div>
                    @if($repairJob->additional_charges > 0)
                        <div class="flex justify-between">
                            <span class="text-[#666666]">Diagnostic / Other:</span>
                            <span class="font-mono text-[#111111]">₹{{ number_format($repairJob->additional_charges, 2) }}</span>
                        </div>
                    @endif
                    @if($repairJob->tax_amount > 0)
                        <div class="flex justify-between">
                            <span class="text-[#666666]">Tax / GST:</span>
                            <span class="font-mono text-[#111111]">₹{{ number_format($repairJob->tax_amount, 2) }}</span>
                        </div>
                    @endif
                    @if($repairJob->discount > 0)
                        <div class="flex justify-between text-[#079455]">
                            <span>Discount:</span>
                            <span class="font-mono">-₹{{ number_format($repairJob->discount, 2) }}</span>
                        </div>
                    @endif

                    <div class="pt-2 border-t border-[#EBEBEB] flex justify-between text-sm font-bold text-[#111111]">
                        <span>Total Repair Cost:</span>
                        <span class="font-mono">₹{{ number_format($repairJob->total_amount, 2) }}</span>
                    </div>

                    <div class="flex justify-between text-xs text-[#079455]">
                        <span>Amount Paid:</span>
                        <span class="font-mono font-bold">₹{{ number_format($repairJob->amount_paid, 2) }}</span>
                    </div>

                    <div class="pt-2 border-t border-[#EBEBEB] flex justify-between text-sm font-bold">
                        <span>Balance Due:</span>
                        @if($due > 0)
                            <span class="font-mono text-[#DC2626]">₹{{ number_format($due, 2) }}</span>
                        @else
                            <span class="font-mono text-[#079455]">Paid Full (₹0.00)</span>
                        @endif
                    </div>
                </div>

                <!-- Record Payment CTA -->
                <button type="button" onclick="document.getElementById('recordPaymentModal').classList.remove('hidden')" class="w-full btn-lime py-2.5 text-xs font-bold rounded-lg flex items-center justify-center gap-2 shadow-xs min-h-[42px]">
                    <svg class="w-4 h-4 text-[#101010]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    <span>Record Payment / Advance</span>
                </button>

                <!-- Payments Ledger -->
                @if($repairJob->payments->count() > 0)
                    <div class="pt-2 border-t border-[#EBEBEB] space-y-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-[#888888]">Payment Receipts</span>
                        @foreach($repairJob->payments as $pmt)
                            <div class="p-2 bg-[#FAFAFA] rounded border border-[#EBEBEB] text-xs flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-[#111111]">₹{{ number_format($pmt->amount, 2) }}</div>
                                    <div class="text-[10px] text-[#888888]">{{ ucfirst($pmt->payment_method) }} · {{ \Carbon\Carbon::parse($pmt->payment_date)->format('d M Y') }}</div>
                                </div>
                                <span class="text-[10px] font-bold text-[#079455] bg-green-50 px-2 py-0.5 rounded">Received</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>

<!-- Modal 1: Add Spare Part -->
<div id="addPartModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-xl">
        <div class="flex items-center justify-between border-b border-[#EBEBEB] pb-3">
            <h3 class="text-base font-bold text-[#111111]">Add Spare Part from Inventory</h3>
            <button type="button" onclick="document.getElementById('addPartModal').classList.add('hidden')" class="p-1 text-[#888888] hover:text-[#111111] rounded">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('repairs.parts.add', $repairJob) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                    Select Spare Part <span class="text-red-500">*</span>
                </label>
                <select name="spare_part_id" id="modal_spare_part_id" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                    <option value="">-- Choose part from workshop stock --</option>
                    @foreach($availableParts as $part)
                        <option value="{{ $part->id }}" data-price="{{ $part->selling_price }}" data-stock="{{ $part->stock_quantity }}">
                            {{ $part->name }} [{{ $part->brand }}] (Qty Available: {{ $part->stock_quantity }}) - ₹{{ number_format($part->selling_price, 2) }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-[#888888] mt-1">Or enter manual component below if not in stock catalogue.</p>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                    Custom Component Name (Optional)
                </label>
                <input type="text" name="item_name" placeholder="Leave empty if selected from above" class="w-full py-2 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">Quantity <span class="text-red-500">*</span></label>
                    <input type="number" min="1" name="quantity" value="1" required class="w-full py-2 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[42px]">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">Unit Price Charged (₹) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="unit_price" id="modal_unit_price" required placeholder="0.00" class="w-full py-2 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm font-mono text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[42px]">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('addPartModal').classList.add('hidden')" class="btn-secondary px-4 py-2 text-xs font-semibold rounded-lg min-h-[40px]">
                    Cancel
                </button>
                <button type="submit" class="btn-lime px-5 py-2 text-xs font-bold rounded-lg shadow-xs min-h-[40px]">
                    Install Part &amp; Deduct Stock
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Edit Charges (Labour, Extra, Discount) -->
<div id="editChargesModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-xl">
        <div class="flex items-center justify-between border-b border-[#EBEBEB] pb-3">
            <h3 class="text-base font-bold text-[#111111]">Update Labour &amp; Service Charges</h3>
            <button type="button" onclick="document.getElementById('editChargesModal').classList.add('hidden')" class="p-1 text-[#888888] hover:text-[#111111] rounded">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('repairs.charges', $repairJob) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">Labour / Service Charge (₹)</label>
                <input type="number" step="0.01" min="0" name="labour_charge" value="{{ $repairJob->labour_charge }}" class="w-full py-2 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm font-mono text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">Additional Diagnostic Charges (₹)</label>
                <input type="number" step="0.01" min="0" name="additional_charges" value="{{ $repairJob->additional_charges }}" class="w-full py-2 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm font-mono text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">Discount (₹)</label>
                    <input type="number" step="0.01" min="0" name="discount" value="{{ $repairJob->discount }}" class="w-full py-2 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm font-mono text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">Tax Amount (₹)</label>
                    <input type="number" step="0.01" min="0" name="tax_amount" value="{{ $repairJob->tax_amount }}" class="w-full py-2 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm font-mono text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">Reason / Notes</label>
                <input type="text" name="notes" placeholder="e.g. Revised labour cost for IC reballing" class="w-full py-2 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-xs text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('editChargesModal').classList.add('hidden')" class="btn-secondary px-4 py-2 text-xs font-semibold rounded-lg min-h-[40px]">
                    Cancel
                </button>
                <button type="submit" class="btn-lime px-5 py-2 text-xs font-bold rounded-lg shadow-xs min-h-[40px]">
                    Update Charges
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Record Payment -->
<div id="recordPaymentModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-xl">
        <div class="flex items-center justify-between border-b border-[#EBEBEB] pb-3">
            <h3 class="text-base font-bold text-[#111111]">Record Payment / Deposit</h3>
            <button type="button" onclick="document.getElementById('recordPaymentModal').classList.add('hidden')" class="p-1 text-[#888888] hover:text-[#111111] rounded">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('repairs.payments', $repairJob) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                    Amount Received (₹) <span class="text-red-500">*</span>
                </label>
                <input type="number" step="0.01" min="0.01" name="amount" value="{{ $due > 0 ? $due : '' }}" required placeholder="0.00" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-base font-mono font-bold text-[#079455] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                @if($due > 0)
                    <p class="text-[11px] text-[#888888] mt-1">Pending balance due is ₹{{ number_format($due, 2) }}</p>
                @endif
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">Payment Method</label>
                <select name="payment_method" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                    <option value="cash">Cash</option>
                    <option value="upi">UPI / QR Code</option>
                    <option value="card">Debit / Credit Card</option>
                    <option value="bank_transfer">Bank Transfer / NEFT</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">Payment Notes / Ref</label>
                <input type="text" name="notes" placeholder="e.g. Balance settlement upon device delivery" class="w-full py-2 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-xs text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('recordPaymentModal').classList.add('hidden')" class="btn-secondary px-4 py-2 text-xs font-semibold rounded-lg min-h-[40px]">
                    Cancel
                </button>
                <button type="submit" class="btn-lime px-5 py-2 text-xs font-bold rounded-lg shadow-xs min-h-[40px]">
                    Confirm Receipt
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const partSelect = document.getElementById('modal_spare_part_id');
        const priceInput = document.getElementById('modal_unit_price');

        if (partSelect && priceInput) {
            partSelect.addEventListener('change', function () {
                const opt = partSelect.selectedOptions[0];
                if (opt && opt.dataset.price) {
                    priceInput.value = opt.dataset.price;
                }
            });
        }
    });
</script>
@endsection

