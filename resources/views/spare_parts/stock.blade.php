@extends('layouts.app')

@section('title', 'Spare Parts Stock Management · ' . ($tenant->name ?? 'Mobile Repair'))
@section('page-title', 'Spare Parts Stock')
@section('page-subtitle', 'Workshop inventory ledger, low-stock alerts, and material movements')

@section('content')
<div class="space-y-6 w-full" style="overflow-x: clip;">

    <!-- Top Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-[#FEF0C7] text-[#B54708] mb-1 border border-[#FEDF89]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#D97706]"></span>
                <span>Stock Control &amp; Alerts</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-[#111111] tracking-tight">Spare Parts Stock Management</h1>
            <p class="text-xs sm:text-sm text-[#666666] mt-0.5">Audit quantities, restock low-inventory components, and reconcile warehouse bins</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('spare-parts.create') }}" class="btn-lime px-4 py-2.5 rounded-lg text-xs sm:text-sm font-bold flex items-center gap-2 shadow-xs whitespace-nowrap min-h-[44px]">
                <svg class="w-4 h-4 text-[#101010]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Inward New Part</span>
            </a>
            <a href="{{ route('spare-parts.index') }}" class="btn-secondary px-4 py-2.5 rounded-lg text-xs sm:text-sm font-semibold flex items-center gap-2 whitespace-nowrap min-h-[44px]">
                <span>Catalogue Directory</span>
            </a>
        </div>
    </div>

    <!-- Low Stock Alert Banner (if any) -->
    @if($lowStockCount > 0 || $outOfStockCount > 0)
        <div class="p-4 rounded-xl bg-[#FEF0C7] border border-[#FEDF89] text-[#B54708] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full bg-[#D97706] animate-pulse shrink-0"></span>
                <div class="text-xs">
                    <strong class="font-bold">Inventory Alert:</strong>
                    @if($outOfStockCount > 0)
                        <span class="font-bold text-[#B91C1C]">{{ $outOfStockCount }} components are completely OUT OF STOCK</span>
                    @endif
                    @if($outOfStockCount > 0 && $lowStockCount > 0) &bull; @endif
                    @if($lowStockCount > 0)
                        <span>{{ $lowStockCount }} components have reached or dropped below their minimum alert threshold</span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('spare-parts.stock', ['filter' => 'out_of_stock']) }}" class="px-2.5 py-1 rounded bg-[#FEF2F2] text-[#B91C1C] border border-[#FECACA] text-[11px] font-bold">
                    View Out of Stock
                </a>
                <a href="{{ route('spare-parts.stock', ['filter' => 'low_stock']) }}" class="px-2.5 py-1 rounded bg-white text-[#B54708] border border-[#FEDF89] text-[11px] font-bold">
                    View Low Stock
                </a>
            </div>
        </div>
    @endif

    <!-- Stock Ledger Table Card -->
    <div class="card-surface shadow-2xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-[#E6E6E6] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Parts Inventory Ledger ({{ $parts->total() }})</h3>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('spare-parts.stock') }}" class="px-2.5 py-1 rounded-md text-[11px] font-bold {{ !request('filter') ? 'bg-[#111111] text-[#B7FF00]' : 'bg-[#F7F7F6] text-[#666666]' }}">All</a>
                <a href="{{ route('spare-parts.stock', ['filter' => 'low_stock']) }}" class="px-2.5 py-1 rounded-md text-[11px] font-bold {{ request('filter') === 'low_stock' ? 'bg-[#111111] text-[#B7FF00]' : 'bg-[#F7F7F6] text-[#666666]' }}">Low Stock ({{ $lowStockCount }})</a>
                <a href="{{ route('spare-parts.stock', ['filter' => 'out_of_stock']) }}" class="px-2.5 py-1 rounded-md text-[11px] font-bold {{ request('filter') === 'out_of_stock' ? 'bg-[#111111] text-[#B7FF00]' : 'bg-[#F7F7F6] text-[#666666]' }}">Out of Stock ({{ $outOfStockCount }})</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F7F7F6] text-[#666666] uppercase tracking-wider font-bold border-b border-[#E6E6E6]">
                    <tr>
                        <th class="py-3 px-4">Part / Component</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">SKU / Location</th>
                        <th class="py-3 px-4 text-center">In Stock</th>
                        <th class="py-3 px-4 text-center">Min Alert</th>
                        <th class="py-3 px-4 text-right">Quick Stock Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E6E6E6] text-[#344054]">
                    @forelse($parts as $part)
                        <tr class="hover:bg-[#F7F7F6]/60 transition">
                            <td class="py-3.5 px-4">
                                <a href="{{ route('spare-parts.show', $part) }}" class="font-bold text-sm text-[#111111] hover:underline block">
                                    {{ $part->name }}
                                </a>
                                <div class="text-[11px] text-[#666666]">{{ $part->compatible_models ?: 'Universal' }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-[#666666]">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-[#F2F4F7] text-[#344054]">
                                    {{ $part->category }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-[#666666]">
                                <div>{{ $part->sku ?: 'No SKU' }}</div>
                                <div class="text-[10px] text-[#929292]">Loc: {{ $part->location ?: 'Not set' }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($part->stock_quantity <= 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold font-mono bg-[#FEF2F2] text-[#B91C1C]">
                                        0 Out of Stock
                                    </span>
                                @elseif($part->isLowStock())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold font-mono bg-[#FEF0C7] text-[#B54708]">
                                        {{ $part->stock_quantity }} Low
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold font-mono bg-[#ECFDF3] text-[#027A48]">
                                        {{ $part->stock_quantity }} Available
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono text-[#666666]">
                                {{ $part->min_stock_level }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Inward 5 units quick form -->
                                    <form action="{{ route('spare-parts.adjust', $part) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="adjustment_type" value="manual_add">
                                        <input type="hidden" name="quantity" value="5">
                                        <input type="hidden" name="reason" value="Quick restock (+5)">
                                        <button type="submit" class="px-2 py-1 rounded bg-[#F7F7F6] text-[#079455] hover:bg-[#ECFDF3] border border-[#E6E6E6] text-[10px] font-bold font-mono transition" title="Quick Restock +5">
                                            +5 Inward
                                        </button>
                                    </form>

                                    <!-- Inward 10 units quick form -->
                                    <form action="{{ route('spare-parts.adjust', $part) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="adjustment_type" value="manual_add">
                                        <input type="hidden" name="quantity" value="10">
                                        <input type="hidden" name="reason" value="Quick restock (+10)">
                                        <button type="submit" class="px-2 py-1 rounded bg-[#F7F7F6] text-[#079455] hover:bg-[#ECFDF3] border border-[#E6E6E6] text-[10px] font-bold font-mono transition" title="Quick Restock +10">
                                            +10 Inward
                                        </button>
                                    </form>

                                    <a href="{{ route('spare-parts.show', $part) }}" class="btn-secondary px-2.5 py-1 text-[11px] font-bold">
                                        Adjust / Audit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-xs text-[#929292]">
                                No parts found matching current ledger filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($parts->hasPages())
            <div class="p-4 border-t border-[#E6E6E6]">
                {{ $parts->links() }}
            </div>
        @endif
    </div>

    <!-- Recent Stock Movements Across All Parts -->
    @if($recentMovements->isNotEmpty())
        <div class="card-surface p-4 sm:p-5 shadow-2xs space-y-3">
            <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Recent Workshop Material Movements</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F7F7F6] text-[#666666] uppercase tracking-wider font-bold border-b border-[#E6E6E6]">
                        <tr>
                            <th class="py-2.5 px-3">Date</th>
                            <th class="py-2.5 px-3">Spare Part</th>
                            <th class="py-2.5 px-3">Type</th>
                            <th class="py-2.5 px-3 text-center">Change</th>
                            <th class="py-2.5 px-3">Before &rarr; After</th>
                            <th class="py-2.5 px-3">Repair Job</th>
                            <th class="py-2.5 px-3">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E6E6E6] text-[#344054]">
                        @foreach($recentMovements as $rm)
                            <tr class="hover:bg-[#F7F7F6]/60">
                                <td class="py-2.5 px-3 font-mono text-[#666666]">{{ $rm->created_at->format('d M, h:i A') }}</td>
                                <td class="py-2.5 px-3 font-bold text-[#111111]">
                                    <a href="{{ route('spare-parts.show', $rm->sparePart) }}" class="hover:underline">
                                        {{ $rm->sparePart?->name ?? 'Spare Part' }}
                                    </a>
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold bg-[#F2F4F7] text-[#344054]">
                                        {{ ucfirst(str_replace('_', ' ', $rm->type)) }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-center font-mono font-bold {{ $rm->quantity_change > 0 ? 'text-[#079455]' : 'text-[#DC2626]' }}">
                                    {{ $rm->quantity_change > 0 ? '+'.$rm->quantity_change : $rm->quantity_change }}
                                </td>
                                <td class="py-2.5 px-3 text-center font-mono">{{ $rm->quantity_before }} &rarr; {{ $rm->quantity_after }}</td>
                                <td class="py-2.5 px-3">
                                    @if($rm->repairJob)
                                        <a href="{{ route('repairs.show', $rm->repairJob) }}" class="font-mono text-[#111111] font-bold hover:underline">
                                            #{{ $rm->repairJob->job_number }}
                                        </a>
                                    @else
                                        <span class="text-[#929292]">&mdash;</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 text-[#666666]">{{ $rm->notes ?: 'Adjustment' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection

