@extends('layouts.app')

@section('title', 'Sales Ledger · Invoices')
@section('page-title', 'Invoices')
@section('page-subtitle', 'Chronological sales registry, payment statuses, and invoice documents')

@section('content')
<div class="space-y-6 w-full" style="overflow-x: clip;">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-[#111111] tracking-tight"> Invoices</h2>
            <p class="text-xs sm:text-sm text-[#666666] mt-0.5">Track all counter transactions, customer settlements, and payment states</p>
        </div>

        <a href="{{ route('invoices.create') }}" class="btn-lime px-4 py-2.5 rounded-lg text-xs sm:text-sm font-bold flex items-center gap-2 shadow-xs whitespace-nowrap min-h-[44px]">
            <svg class="w-4 h-4 text-[#101010]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            <span>New Bill (Sell)</span>
        </a>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="card-surface p-4 shadow-2xs">
            <span class="text-[11px] font-bold text-[#666666] uppercase tracking-wider">Total Invoices</span>
            <div class="text-xl sm:text-2xl font-bold text-[#111111] mt-1">{{ $stats['total_invoices'] }}</div>
        </div>
        <div class="card-surface p-4 shadow-2xs">
            <span class="text-[11px] font-bold text-[#666666] uppercase tracking-wider">Total Sales GMV</span>
            <div class="text-xl sm:text-2xl font-bold text-[#111111] mt-1">₹{{ number_format($stats['total_sales'], 2) }}</div>
        </div>
        <div class="card-surface p-4 shadow-2xs">
            <span class="text-[11px] font-bold text-[#079455] uppercase tracking-wider">Total Collected</span>
            <div class="text-xl sm:text-2xl font-bold text-[#079455] mt-1">₹{{ number_format($stats['total_collected'], 2) }}</div>
        </div>
        <div class="card-surface p-4 shadow-2xs">
            <span class="text-[11px] font-bold text-[#D97706] uppercase tracking-wider">Pending Dues</span>
            <div class="text-xl sm:text-2xl font-bold text-[#D97706] mt-1">₹{{ number_format($stats['total_due'], 2) }}</div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card-surface p-4 shadow-2xs">
        <form method="GET" action="{{ route('invoices.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="sm:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by Invoice #, Customer, Phone, or IMEI..."
                    class="w-full pl-9 pr-4 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black min-h-[40px]">
                <svg class="w-4 h-4 text-[#929292] absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <div>
                <select name="status" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-1 focus:ring-black min-h-[40px]">
                    <option value="all">All Payment Statuses</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid in Full</option>
                    <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial / Due</option>
                    <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                </select>
            </div>

            <div>
                <input type="date" name="from_date" value="{{ request('from_date') }}" placeholder="From Date"
                    class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-1 focus:ring-black min-h-[40px]">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 bg-[#050505] hover:bg-[#1a1a1a] text-white rounded-lg text-xs sm:text-sm font-semibold min-h-[40px] transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'from_date', 'to_date']))
                    <a href="{{ route('invoices.index') }}" class="px-3 py-2 text-[#666666] hover:text-[#111111] text-xs font-semibold min-h-[40px] inline-flex items-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Invoices List / Table -->
    @if($invoices->isEmpty())
        <div class="card-surface p-12 text-center shadow-2xs">
            <div class="w-12 h-12 rounded-full bg-[#F2F4F7] text-[#666666] flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h3 class="text-sm font-bold text-[#111111]">No invoices found</h3>
            <p class="text-xs text-[#666666] mt-1 mb-4">Clear filters or issue a new bill for a customer.</p>
            <a href="{{ route('invoices.create') }}" class="btn-lime px-4 py-2 rounded-lg text-xs font-bold inline-flex items-center gap-1.5 shadow-2xs min-h-[40px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Create New Bill</span>
            </a>
        </div>
    @else
        <!-- Mobile Card List -->
        <div class="block md:hidden space-y-3">
            @foreach($invoices as $inv)
                @php
                    $tenant = \App\Services\TenantContext::get();
                    $whatsAppText = urlencode("Hello {$inv->customer->name}, thank you for your purchase from {$tenant->name}!\n\nInvoice: {$inv->invoice_number}\nAmount: ₹" . number_format($inv->grand_total, 2) . "\nDate: " . $inv->invoice_date->format('d M Y') . "\n\nWe appreciate your business!");
                    $whatsAppLink = "https://wa.me/91" . preg_replace('/[^0-9]/', '', $inv->customer->phone) . "?text={$whatsAppText}";
                @endphp
                <div class="card-surface p-4 space-y-3 shadow-2xs">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
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

                            <h4 class="text-sm font-bold text-[#111111] mt-1">
                                {{ $inv->customer->name }}
                                <span class="text-xs text-[#929292] font-normal font-mono">({{ $inv->customer->phone }})</span>
                            </h4>

                            <div class="text-xs text-[#666666] mt-1">
                                @foreach($inv->items as $it)
                                    <div>{{ $it->item_name }} ({{ $it->storage }}) &bull; <span class="font-mono text-[11px] text-[#929292]">IMEI: {{ $it->imei_1 ?: 'N/A' }}</span></div>
                                @endforeach
                            </div>
                        </div>

                        <div class="text-right flex-shrink-0">
                            <div class="text-base font-bold text-[#111111]">₹{{ number_format($inv->grand_total, 2) }}</div>
                            <span class="text-[10px] text-[#929292] uppercase font-semibold">{{ $inv->payment_method }}</span>
                            <div class="text-[10px] text-[#929292] mt-0.5">{{ $inv->invoice_date->format('d M Y') }}</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2.5 border-t border-[#E6E6E6] text-xs">
                        <a href="{{ route('invoices.show', $inv) }}" class="font-semibold text-[#111111] hover:underline min-h-[36px] inline-flex items-center">
                            View Bill 
                        </a>

                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('invoices.print', $inv) }}" target="_blank" class="px-2.5 py-1.5 btn-lime rounded-lg text-xs font-bold min-h-[36px] inline-flex items-center gap-1" title="Direct Print">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                <span>Print</span>
                            </a>
                            <a href="{{ $whatsAppLink }}" target="_blank" class="px-2.5 py-1.5 bg-[#ECFDF3] text-[#027A48] border border-[#A6F4C5] rounded-lg text-xs font-semibold min-h-[36px] inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                <span>WhatsApp</span>
                            </a>
                            <a href="{{ route('invoices.pdf', $inv) }}" class="px-2.5 py-1.5 btn-secondary rounded-lg text-xs font-semibold min-h-[36px] inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span>PDF</span>
                            </a>
                            <a href="{{ route('invoices.thermal', $inv) }}" target="_blank" class="px-2.5 py-1.5 bg-[#050505] text-white rounded-lg text-xs font-semibold min-h-[36px] inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                <span>POS</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block card-surface overflow-hidden shadow-2xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F7F7F6] border-b border-[#E6E6E6] text-[#666666] uppercase tracking-wider font-semibold">
                        <tr>
                            <th class="py-3 px-4">Invoice #</th>
                            <th class="py-3 px-4">Customer</th>
                            <th class="py-3 px-4">Sold Items & IMEI</th>
                            <th class="py-3 px-4 text-right">Grand Total</th>
                            <th class="py-3 px-4">Payment</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E6E6E6] text-[#111111]">
                        @foreach($invoices as $inv)
                            @php
                                $tenant = \App\Services\TenantContext::get();
                                $whatsAppText = urlencode("Hello {$inv->customer->name}, thank you for your purchase from {$tenant->name}!\n\nInvoice: {$inv->invoice_number}\nAmount: ₹" . number_format($inv->grand_total, 2) . "\nDate: " . $inv->invoice_date->format('d M Y') . "\n\nWe appreciate your business!");
                                $whatsAppLink = "https://wa.me/91" . preg_replace('/[^0-9]/', '', $inv->customer->phone) . "?text={$whatsAppText}";
                            @endphp
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
                                    @foreach($inv->items as $it)
                                        <div class="text-[#333333]">
                                            {{ $it->item_name }}
                                            @if($it->storage)<span class="text-[#666666]">({{ $it->storage }})</span>@endif
                                            @if($it->imei_1)<span class="font-mono text-[11px] text-[#929292]">&bull; {{ $it->imei_1 }}</span>@endif
                                        </div>
                                    @endforeach
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-[#111111]">
                                    ₹{{ number_format($inv->grand_total, 2) }}
                                </td>
                                <td class="py-3 px-4 capitalize text-[#666666]">
                                    {{ $inv->payment_method }}
                                </td>
                                <td class="py-3 px-4 text-[#666666]">
                                    {{ $inv->invoice_date->format('d M Y') }}
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
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('invoices.show', $inv) }}" class="p-1.5 text-[#666666] hover:text-[#111111] rounded hover:bg-[#F2F4F7]" title="View Invoice">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <a href="{{ route('invoices.print', $inv) }}" target="_blank" class="p-1.5 text-[#111111] hover:bg-[#B7FF00]/40 rounded" title="Print Invoice (Direct Prompt)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </a>
                                        <a href="{{ $whatsAppLink }}" target="_blank" class="p-1.5 text-[#079455] hover:bg-[#ECFDF3] rounded" title="WhatsApp Share">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                        </a>
                                        <a href="{{ route('invoices.pdf', $inv) }}" class="p-1.5 text-[#2563EB] hover:bg-[#EFF6FF] rounded" title="Download PDF">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </a>
                                        <a href="{{ route('invoices.thermal', $inv) }}" target="_blank" class="p-1.5 text-[#111111] hover:bg-[#F2F4F7] rounded" title="Thermal POS Receipt">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($invoices->hasPages())
                <div class="p-3.5 border-t border-[#E6E6E6]">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
