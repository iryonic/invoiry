@extends('layouts.app')

@section('title', 'Counter Overview')
@section('page-title', 'Counter Overview')
@section('page-subtitle', ($tenant->name ?? 'Retail Store') . ' · ' . ($tenant->city ? $tenant->city . ' Terminal' : 'Billing Counter'))

@section('content')
<div class="space-y-6 w-full" style="overflow-x: clip;">

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-[#111111] tracking-tight">Counter Overview</h2>
            <p class="text-xs sm:text-sm text-[#666666] mt-0.5">
                {{ $tenant->name }} &bull; GSTIN: {{ $tenant->gstin ?: 'Unregistered' }} &bull; Live Terminal
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('invoices.create') }}" class="btn-lime px-4 py-2.5 rounded-lg text-xs sm:text-sm font-bold flex items-center gap-2 shadow-xs whitespace-nowrap min-h-[44px]">
                <svg class="w-4 h-4 text-[#101010]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Create Bill</span>
            </a>
            <a href="{{ route('items.create') }}" class="btn-secondary px-4 py-2.5 rounded-lg text-xs sm:text-sm font-semibold flex items-center gap-2 whitespace-nowrap min-h-[44px]">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Add Stock</span>
            </a>
        </div>
    </div>

    <!-- 4 Consistent KPI Cards in One Row (Desktop) / 2 Cols (Tablet/Mobile) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        
        <!-- Today's Intake -->
        <div class="card-surface p-4 sm:p-5 flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between text-[#666666]">
                <span class="text-xs font-semibold uppercase tracking-wider">Today's Intake</span>
                <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-[#F2F4F7] text-[#344054]">{{ $todayInvoicesCount }} bills</span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-[26px] font-bold text-[#111111] tracking-tight">₹{{ number_format($todaySales, 2) }}</div>
                <p class="text-[11px] text-[#929292] mt-0.5">All settlements today</p>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="card-surface p-4 sm:p-5 flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between text-[#666666]">
                <span class="text-xs font-semibold uppercase tracking-wider">Monthly Revenue</span>
                <span class="text-[11px] font-mono text-[#929292]">{{ now()->format('M Y') }}</span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-[26px] font-bold text-[#111111] tracking-tight">₹{{ number_format($monthSales, 2) }}</div>
                <p class="text-[11px] text-[#929292] mt-0.5">Gross billing value</p>
            </div>
        </div>

        <!-- In-Stock Phones -->
        <div class="card-surface p-4 sm:p-5 flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between text-[#666666]">
                <span class="text-xs font-semibold uppercase tracking-wider">In-Stock Phones</span>
                <span class="w-2 h-2 rounded-full bg-[#079455]"></span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-[26px] font-bold text-[#111111] tracking-tight">{{ $inStockCount }}</div>
                <p class="text-[11px] text-[#929292] mt-0.5">Asset cost: ₹{{ number_format($inStockValue, 0) }}</p>
            </div>
        </div>

        <!-- Outstanding Dues (Khata) -->
        <div class="card-surface p-4 sm:p-5 flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between text-[#666666]">
                <span class="text-xs font-semibold uppercase tracking-wider text-[#D97706]">Active Credit</span>
                <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-[#FFFBEB] text-[#B45309] font-bold">Khata</span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-[26px] font-bold text-[#D97706] tracking-tight">₹{{ number_format($totalDue, 2) }}</div>
                <p class="text-[11px] text-[#929292] mt-0.5">Pending balance</p>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Two-Column Layout (68% / 32%) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left 8 Cols (~67%): Recent Invoices / Sales Register -->
        <div class="lg:col-span-8 space-y-3">
            <div class="flex items-center justify-between px-1">
                <div>
                    <h3 class="text-sm font-bold text-[#111111] uppercase tracking-wider">Recent Invoices</h3>
                    <p class="text-xs text-[#666666]">Latest transactions at this counter</p>
                </div>
                <a href="{{ route('invoices.index') }}" class="text-xs font-bold text-[#111111] hover:underline flex items-center gap-1">
                    <span>View all</span>
                    
                </a>
            </div>

            <!-- Desktop Invoices Table -->
            <div class="card-surface overflow-hidden shadow-2xs hidden sm:block">
                @if($recentInvoices->isEmpty())
                    <div class="p-8 text-center text-xs text-[#929292]">
                        No invoices recorded today. Start selling with "+ New Bill".
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#F7F7F6] border-b border-[#E6E6E6] text-[#666666] uppercase tracking-wider font-semibold">
                                <tr>
                                    <th class="py-3 px-4">Invoice #</th>
                                    <th class="py-3 px-4">Customer</th>
                                    <th class="py-3 px-4">Device / Items</th>
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
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="font-semibold text-[#111111]">{{ $inv->customer->name }}</div>
                                            <div class="text-[11px] text-[#929292] font-mono">{{ $inv->customer->phone }}</div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="text-[#333333]">
                                                {{ $inv->items->pluck('item_name')->join(', ') ?: 'Custom Item' }}
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

            <!-- Mobile Stacked Invoices Card List -->
            <div class="sm:hidden space-y-2.5">
                @forelse($recentInvoices as $inv)
                    <div class="card-surface p-3.5 space-y-2.5 shadow-2xs">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('invoices.show', $inv) }}" class="font-mono text-xs font-bold text-[#111111] hover:underline">
                                {{ $inv->invoice_number }}
                            </a>
                            @if($inv->payment_status === 'paid')
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold badge-paid">Paid</span>
                            @elseif($inv->payment_status === 'partial')
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold badge-due">Due ₹{{ number_format($inv->amount_due, 0) }}</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold badge-unpaid">Unpaid</span>
                            @endif
                        </div>
                        <div class="flex items-start justify-between text-xs">
                            <div>
                                <p class="font-semibold text-[#111111]">{{ $inv->customer->name }}</p>
                                <p class="text-[11px] text-[#929292]">{{ $inv->items->pluck('item_name')->join(', ') ?: 'Device' }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold text-[#111111]">₹{{ number_format($inv->grand_total, 2) }}</div>
                                <div class="text-[10px] text-[#929292]">{{ $inv->invoice_date->format('d M') }}</div>
                            </div>
                        </div>
                        <div class="pt-2 border-t border-[#E6E6E6] flex items-center justify-between text-xs">
                            <a href="{{ route('invoices.show', $inv) }}" class="font-semibold text-[#111111] hover:underline">
                                View Bill 
                            </a>
                            <a href="{{ route('invoices.thermal', $inv) }}" target="_blank" class="text-[11px] text-[#666666] hover:text-black">
                                Thermal POS
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="card-surface p-6 text-center text-xs text-[#929292]">
                        No invoices today.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right 4 Cols (~33%): In-Stock Devices Snapshot -->
        <div class="lg:col-span-4 space-y-3">
            <div class="flex items-center justify-between px-1">
                <div>
                    <h3 class="text-sm font-bold text-[#111111] uppercase tracking-wider">Stock Snapshot</h3>
                    <p class="text-xs text-[#666666]">Ready-to-sell electronics</p>
                </div>
                <a href="{{ route('items.index') }}" class="text-xs font-bold text-[#111111] hover:underline flex items-center gap-1">
                    <span>View inventory</span>
                    
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
</div>
@endsection
