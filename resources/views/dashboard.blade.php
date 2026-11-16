@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', ($tenant->name ?? 'Retail Store') . ' · ' . ($tenant->business_type_name ?? 'Store Terminal'))

@section('content')
<div class="space-y-6 w-full" style="overflow-x: clip;">

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-[#F2F4F7] text-[#344054] mb-1 border border-[#EAECF0]">
                <span>{{ $tenant->business_type_name }}</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-[#111111] tracking-tight">{{ $tenant->name }} Dashboard</h2>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @if($supportsSelling)
                <a href="{{ route('invoices.create') }}" class="btn-lime px-3.5 py-2.5 rounded-lg text-xs sm:text-sm font-bold flex items-center gap-2 shadow-xs whitespace-nowrap min-h-[44px]">
                    <svg class="w-4 h-4 text-[#101010]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    <span>Create Bill</span>
                </a>
            @endif

            @if($supportsRepair)
                <a href="{{ route('repairs.create') }}" class="{{ $supportsSelling ? 'btn-secondary' : 'btn-lime' }} px-3.5 py-2.5 rounded-lg text-xs sm:text-sm font-bold flex items-center gap-2 shadow-xs whitespace-nowrap min-h-[44px]">
                    <svg class="w-4 h-4 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    <span>New Repair Request</span>
                </a>
            @endif

            @if($supportsSelling)
                <a href="{{ route('items.create') }}" class="btn-secondary px-3.5 py-2.5 rounded-lg text-xs sm:text-sm font-semibold flex items-center gap-2 whitespace-nowrap min-h-[44px]">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Add Device</span>
                </a>
            @elseif($supportsRepair)
                <a href="{{ route('spare-parts.create') }}" class="btn-secondary px-3.5 py-2.5 rounded-lg text-xs sm:text-sm font-semibold flex items-center gap-2 whitespace-nowrap min-h-[44px]">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Add Spare Part</span>
                </a>
            @endif
        </div>
    </div>

    <!-- KPI Metric Cards Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        
        <!-- Card 1: Today's Intake / Revenue -->
        <div class="card-surface p-4 sm:p-5 flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between text-[#666666]">
                <span class="text-xs font-semibold uppercase tracking-wider">Today's Intake</span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-[26px] font-bold text-[#111111] tracking-tight">₹{{ number_format($todaySales, 2) }}</div>
                <p class="text-[11px] text-[#929292] mt-0.5">All billing settled today</p>
            </div>
        </div>

        @if($tenant->isSellingOnly())
            <!-- Selling Only: Monthly Revenue, In-Stock Phones, Active Credit -->
            <div class="card-surface p-4 sm:p-5 flex flex-col justify-between shadow-2xs">
                <div class="flex items-center justify-between text-[#666666]">
                    <span class="text-xs font-semibold uppercase tracking-wider">Monthly Revenue</span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl sm:text-[26px] font-bold text-[#111111] tracking-tight">₹{{ number_format($monthSales, 2) }}</div>
                    <p class="text-[11px] text-[#929292] mt-0.5">Gross billing value</p>
                </div>
            </div>

            <div class="card-surface p-4 sm:p-5 flex flex-col justify-between shadow-2xs">
                <div class="flex items-center justify-between text-[#666666]">
                    <span class="text-xs font-semibold uppercase tracking-wider">In-Stock Phones</span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl sm:text-[26px] font-bold text-[#111111] tracking-tight">{{ $inStockCount }}</div>
                    <p class="text-[11px] text-[#929292] mt-0.5">Asset cost: ₹{{ number_format($inStockValue, 0) }}</p>
                </div>
            </div>

            <div class="card-surface p-4 sm:p-5 flex flex-col justify-between shadow-2xs">
                <div class="flex items-center justify-between text-[#666666]">
                    <span class="text-xs font-semibold uppercase tracking-wider text-[#D97706]">Active Credit</span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl sm:text-[26px] font-bold text-[#D97706] tracking-tight">₹{{ number_format($totalDue, 2) }}</div>
                    <p class="text-[11px] text-[#929292] mt-0.5">Pending balance</p>
                </div>
            </div>

        @elseif($tenant->isRepairOnly())
            <!-- Repair Only: Active on Bench, Ready for Pickup, Spare Parts Low Stock -->
            <div class="card-surface p-4 sm:p-5 flex flex-col justify-between shadow-2xs">
                <div class="flex items-center justify-between text-[#666666]">
                    <span class="text-xs font-semibold uppercase tracking-wider text-[#2563EB]">Active in Workshop</span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl sm:text-[26px] font-bold text-[#2563EB] tracking-tight">{{ $activeRepairsCount }}</div>
                    <p class="text-[11px] text-[#929292] mt-0.5">Devices currently in repair</p>
                </div>
            </div>

            <div class="card-surface p-4 sm:p-5 flex flex-col justify-between shadow-2xs">
                <div class="flex items-center justify-between text-[#666666]">
                    <span class="text-xs font-semibold uppercase tracking-wider text-[#079455]">Ready for Delivery</span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl sm:text-[26px] font-bold text-[#079455] tracking-tight">{{ $completedRepairsCount }}</div>
                    <p class="text-[11px] text-[#929292] mt-0.5">Repairs completed</p>
                </div>
            </div>

            <div class="card-surface p-4 sm:p-5 flex flex-col justify-between shadow-2xs">
                <div class="flex items-center justify-between text-[#666666]">
                    <span class="text-xs font-semibold uppercase tracking-wider {{ $sparePartsLowStockCount > 0 ? 'text-[#D97706]' : 'text-[#666666]' }}">Low Stock Parts</span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl sm:text-[26px] font-bold {{ $sparePartsLowStockCount > 0 ? 'text-[#D97706]' : 'text-[#111111]' }} tracking-tight">
                        {{ $sparePartsLowStockCount }}
                    </div>
                    <p class="text-[11px] text-[#929292] mt-0.5">{{ $sparePartsCount }} total part lines</p>
                </div>
            </div>

        @else
            <!-- Both Selling & Repair: In-Stock Phones, Active Repairs, Pending Due -->
            <div class="card-surface p-4 sm:p-5 flex flex-col justify-between shadow-2xs">
                <div class="flex items-center justify-between text-[#666666]">
                    <span class="text-xs font-semibold uppercase tracking-wider">In-Stock Phones</span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl sm:text-[26px] font-bold text-[#111111] tracking-tight">{{ $inStockCount }}</div>
                    <p class="text-[11px] text-[#929292] mt-0.5">Asset cost: ₹{{ number_format($inStockValue, 0) }}</p>
                </div>
            </div>

            <div class="card-surface p-4 sm:p-5 flex flex-col justify-between shadow-2xs">
                <div class="flex items-center justify-between text-[#666666]">
                    <span class="text-xs font-semibold uppercase tracking-wider text-[#2563EB]">Active Repairs</span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl sm:text-[26px] font-bold text-[#2563EB] tracking-tight">{{ $activeRepairsCount }}</div>
                    <p class="text-[11px] text-[#929292] mt-0.5">{{ $completedRepairsCount }} ready for pickup</p>
                </div>
            </div>

            <div class="card-surface p-4 sm:p-5 flex flex-col justify-between shadow-2xs">
                <div class="flex items-center justify-between text-[#666666]">
                    <span class="text-xs font-semibold uppercase tracking-wider text-[#D97706]">Active Credit</span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl sm:text-[26px] font-bold text-[#D97706] tracking-tight">₹{{ number_format($totalDue, 2) }}</div>
                    <p class="text-[11px] text-[#929292] mt-0.5">Pending balance</p>
                </div>
            </div>
        @endif

    </div>

    <!-- Main Dashboard Section Layout -->
    @if($tenant->isRepairOnly())
        <!-- REPAIR ONLY DASHBOARD VIEW: Recent Repair Jobs (Left 8) & Workshop Parts (Right 4) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left 8 Cols: Workshop Jobs -->
            <div class="lg:col-span-8 space-y-3">
                <div class="flex items-center justify-between px-1">
                    <div>
                        <h3 class="text-sm font-bold text-[#111111] uppercase tracking-wider">Recent Repair Jobs</h3>
                        <p class="text-xs text-[#666666]">Active and latest service orders in workshop</p>
                    </div>
                    <a href="{{ route('repairs.index') }}" class="text-xs font-bold text-[#111111] hover:underline flex items-center gap-1">
                        <span>View all jobs &rarr;</span>
                    </a>
                </div>

                <div class="card-surface overflow-hidden shadow-2xs">
                    @if($recentRepairs->isEmpty())
                        <div class="p-8 text-center text-xs text-[#929292]">
                            No repair orders registered yet. Click "New Repair Request" to check in customer devices.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-[#F7F7F6] border-b border-[#E6E6E6] text-[#666666] uppercase tracking-wider font-semibold">
                                    <tr>
                                        <th class="py-3 px-4">Job #</th>
                                        <th class="py-3 px-4">Customer</th>
                                        <th class="py-3 px-4">Device</th>
                                        <th class="py-3 px-4">Technician</th>
                                        <th class="py-3 px-4">Status</th>
                                        <th class="py-3 px-4 text-right">Cost</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#E6E6E6] text-[#111111]">
                                    @foreach($recentRepairs as $job)
                                        <tr class="hover:bg-[#F7F7F6]/80 transition">
                                            <td class="py-3 px-4 font-mono font-bold">
                                                <a href="{{ route('repairs.show', $job) }}" class="hover:underline text-[#111111]">
                                                    #{{ $job->job_number }}
                                                </a>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="font-semibold text-[#111111]">{{ $job->customer->name ?? 'Walk-in' }}</div>
                                                <div class="text-[11px] text-[#929292] font-mono">{{ $job->customer->phone ?? '' }}</div>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="font-bold text-[#111111]">{{ $job->device_brand }} {{ $job->device_model }}</div>
                                                <div class="text-[10px] text-[#666666] line-clamp-1">{{ $job->reported_problem }}</div>
                                            </td>
                                            <td class="py-3 px-4 text-xs">
                                                {{ $job->technician->name ?? 'Unassigned' }}
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $job->status_info['color'] ?? 'bg-gray-100' }}">
                                                    {{ $job->status_info['label'] }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-right font-mono font-bold text-[#111111]">
                                                ₹{{ number_format($job->total_amount, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right 4 Cols: Quick Actions & Invoices Summary -->
            <div class="lg:col-span-4 space-y-4">
                <div class="flex items-center justify-between px-1">
                    <div>
                        <h3 class="text-sm font-bold text-[#111111] uppercase tracking-wider">Parts &amp; Inventory</h3>
                        <p class="text-xs text-[#666666]">Hardware overview</p>
                    </div>
                    <a href="{{ route('spare-parts.index') }}" class="text-xs font-bold text-[#111111] hover:underline">
                        Catalogue &rarr;
                    </a>
                </div>

                <div class="card-surface p-4 space-y-3 shadow-2xs">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-[#666666]">Total Parts Catalogued:</span>
                        <span class="font-bold text-[#111111]">{{ $sparePartsCount }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-[#666666]">Low Stock Items:</span>
                        <span class="font-bold {{ $sparePartsLowStockCount > 0 ? 'text-[#D97706]' : 'text-[#079455]' }}">
                            {{ $sparePartsLowStockCount }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-[#666666]">Parts Valuation:</span>
                        <span class="font-mono font-bold text-[#111111]">₹{{ number_format($sparePartsValue, 0) }}</span>
                    </div>

                    <div class="pt-3 border-t border-[#EBEBEB] space-y-2">
                        <a href="{{ route('spare-parts.stock') }}" class="w-full btn-secondary py-2 text-xs font-semibold rounded-lg flex items-center justify-center gap-1.5">
                            <span>Manage Stock Levels</span>
                        </a>
                        <a href="{{ route('spare-parts.create') }}" class="w-full btn-lime py-2 text-xs font-bold rounded-lg flex items-center justify-center gap-1.5 shadow-xs">
                            <span>+ Add Spare Part</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- SELLING OR BOTH DASHBOARD VIEW -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left 8 Cols (~67%): Recent Invoices / Sales Register -->
            <div class="lg:col-span-8 space-y-3">
                <div class="flex items-center justify-between px-1">
                    <div>
                        <h3 class="text-sm font-bold text-[#111111] uppercase tracking-wider">Recent Invoices</h3>
                        <p class="text-xs text-[#666666]">Latest sales &amp; repair settlements at this counter</p>
                    </div>
                    <a href="{{ route('invoices.index') }}" class="text-xs font-bold text-[#111111] hover:underline flex items-center gap-1">
                        <span>View all &rarr;</span>
                    </a>
                </div>

                <!-- Invoices Table -->
                <div class="card-surface overflow-hidden shadow-2xs">
                    @if($recentInvoices->isEmpty())
                        <div class="p-8 text-center text-xs text-[#929292]">
                            No invoices recorded yet. Start billing with "+ Create Bill".
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-[#F7F7F6] border-b border-[#E6E6E6] text-[#666666] uppercase tracking-wider font-semibold">
                                    <tr>
                                        <th class="py-3 px-4">Invoice #</th>
                                        <th class="py-3 px-4">Customer</th>
                                        <th class="py-3 px-4">Items / Service</th>
                                        <th class="py-3 px-4 text-right">Amount</th>
                                        <th class="py-3 px-4">Status</th>
                                        <th class="py-3 px-4 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#E6E6E6] text-[#111111]">
                                    @foreach($recentInvoices as $inv)
                                        <tr class="hover:bg-[#F7F7F6]/80 transition">
                                            <td class="py-3 px-4 font-mono font-bold">
                                                <a href="{{ route('invoices.show', $inv) }}" class="hover:underline text-[#111111]">
                                                    {{ $inv->invoice_number }}
                                                </a>
                                                @if($inv->isRepair())
                                                    <span class="inline-block px-1.5 py-0.2 rounded text-[9px] font-bold bg-[#E0F2FE] text-[#0369A1] ml-1">REPAIR</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="font-semibold text-[#111111]">{{ $inv->customer->name ?? 'Walk-in' }}</div>
                                                <div class="text-[11px] text-[#929292] font-mono">{{ $inv->customer->phone ?? '' }}</div>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="text-[#333333]">
                                                    {{ $inv->items->pluck('item_name')->join(', ') ?: 'Custom Items' }}
                                                </div>
                                                <div class="text-[10px] text-[#929292]">{{ $inv->invoice_date->format('d M Y') }}</div>
                                            </td>
                                            <td class="py-3 px-4 text-right font-bold text-[#111111]">
                                                ₹{{ number_format($inv->grand_total, 2) }}
                                            </td>
                                            <td class="py-3 px-4">
                                                @if($inv->payment_status === 'paid')
                                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold badge-paid">Paid</span>
                                                @elseif($inv->payment_status === 'partial')
                                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold badge-due">Due ₹{{ number_format($inv->amount_due, 0) }}</span>
                                                @else
                                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold badge-unpaid">Unpaid</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route('invoices.show', $inv) }}" class="p-1 text-[#666666] hover:text-[#111111] rounded hover:bg-[#F2F4F7]" title="View Bill">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                    </a>
                                                    <a href="{{ route('invoices.thermal', $inv) }}" target="_blank" class="p-1 text-[#666666] hover:text-[#111111] rounded hover:bg-[#F2F4F7]" title="Print Receipt">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                @if($supportsRepair && $recentRepairs->isNotEmpty())
                    <!-- Additional Workshop Bench Widget for 'Both' businesses -->
                    <div class="pt-4 space-y-3">
                        <div class="flex items-center justify-between px-1">
                            <div>
                                <h3 class="text-sm font-bold text-[#111111] uppercase tracking-wider">Active Workshop Bench</h3>
                                <p class="text-xs text-[#666666]">Repair orders awaiting work or customer collection</p>
                            </div>
                            <a href="{{ route('repairs.index') }}" class="text-xs font-bold text-[#111111] hover:underline">
                                Workshop Board &rarr;
                            </a>
                        </div>

                        <div class="card-surface overflow-hidden shadow-2xs">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-[#F7F7F6] border-b border-[#E6E6E6] text-[#666666] uppercase tracking-wider font-semibold">
                                        <tr>
                                            <th class="py-2.5 px-4">Job #</th>
                                            <th class="py-2.5 px-4">Device &amp; Issue</th>
                                            <th class="py-2.5 px-4">Customer</th>
                                            <th class="py-2.5 px-4">Status</th>
                                            <th class="py-2.5 px-4 text-right">Due</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#E6E6E6] text-[#111111]">
                                        @foreach($recentRepairs->take(5) as $rjob)
                                            <tr class="hover:bg-[#F7F7F6]/80 transition">
                                                <td class="py-2.5 px-4 font-mono font-bold">
                                                    <a href="{{ route('repairs.show', $rjob) }}" class="hover:underline">
                                                        #{{ $rjob->job_number }}
                                                    </a>
                                                </td>
                                                <td class="py-2.5 px-4">
                                                    <span class="font-bold">{{ $rjob->device_brand }} {{ $rjob->device_model }}</span>
                                                    <span class="text-[#888888] ml-1">({{ $rjob->reported_problem }})</span>
                                                </td>
                                                <td class="py-2.5 px-4">{{ $rjob->customer->name ?? 'Walk-in' }}</td>
                                                <td class="py-2.5 px-4">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $rjob->status_info['color'] ?? 'bg-gray-100' }}">
                                                        {{ $rjob->status_info['label'] }}
                                                    </span>
                                                </td>
                                                <td class="py-2.5 px-4 text-right font-mono font-bold {{ $rjob->due_balance > 0 ? 'text-[#DC2626]' : 'text-[#079455]' }}">
                                                    ₹{{ number_format($rjob->due_balance, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right 4 Cols (~33%): Stock Snapshot -->
            <div class="lg:col-span-4 space-y-3">
                <div class="flex items-center justify-between px-1">
                    <div>
                        <h3 class="text-sm font-bold text-[#111111] uppercase tracking-wider">Device Inventory</h3>
                        <p class="text-xs text-[#666666]">Ready-to-sell electronics</p>
                    </div>
                    <a href="{{ route('items.index') }}" class="text-xs font-bold text-[#111111] hover:underline flex items-center gap-1">
                        <span>View catalog &rarr;</span>
                    </a>
                </div>

                <div class="card-surface p-3.5 space-y-2.5 shadow-2xs">
                    @forelse($recentStock as $item)
                        <div class="p-2.5 rounded-lg border border-[#E6E6E6] bg-[#FFFFFF] hover:border-[#D6D6D6] transition flex items-center justify-between gap-2.5">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <h4 class="text-xs font-bold text-[#111111] truncate">{{ $item->item_name }}</h4>
                                    @if($item->storage)
                                        <span class="text-[10px] px-1 py-0.2 bg-[#F2F4F7] text-[#344054] font-mono rounded">{{ $item->storage }}</span>
                                    @endif
                                </div>
                                <div class="text-[10px] font-mono text-[#929292] truncate mt-0.5">
                                    IMEI: {{ $item->imei_1 ?: 'N/A' }}
                                </div>
                                <div class="flex items-center gap-2 text-[10px] text-[#666666] mt-0.5">
                                    <span class="capitalize px-1 rounded bg-[#F2F4F7] text-[#344054]">{{ $item->condition }}</span>
                                    <span class="font-semibold text-[#111111]">₹{{ number_format($item->selling_price ?: $item->purchase_price, 0) }}</span>
                                </div>
                            </div>

                            <div class="flex-shrink-0">
                                @if($item->status === 'in_stock')
                                    <a href="{{ route('invoices.create', ['preselect_item' => $item->id]) }}" class="btn-lime px-2.5 py-1 rounded text-[11px] font-bold shadow-2xs flex items-center gap-1 min-h-[30px]">
                                        <span>Sell</span>
                                    </a>
                                @else
                                    <span class="text-[10px] font-mono text-[#929292]">Sold</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-[#929292]">
                            No stock records found.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
