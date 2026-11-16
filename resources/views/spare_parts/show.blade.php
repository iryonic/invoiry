@extends('layouts.app')

@section('title', $sparePart->name . ' · Spare Parts')
@section('page-title', 'Spare Part Details')
@section('page-subtitle', $sparePart->name . ' · Workshop Component Inspection')

@section('content')
<div class="space-y-6 w-full" style="overflow-x: clip;">

    <!-- Top Action & Navigation Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-[#FEF0C7] text-[#B54708] mb-1 border border-[#FEDF89]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#D97706]"></span>
                <span>Category: {{ $sparePart->category }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-[#111111] tracking-tight">{{ $sparePart->name }}</h1>
            <p class="text-xs sm:text-sm text-[#666666] mt-0.5">Compatible with: <strong class="text-[#111111]">{{ $sparePart->compatible_models ?: 'Universal / Multiple Models' }}</strong></p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" onclick="openAdjustModal()" class="btn-lime min-h-[40px] px-4 text-xs font-bold shadow-xs flex items-center gap-2">
                <svg class="w-4 h-4 text-[#101010]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Adjust Stock</span>
            </button>
            <a href="{{ route('spare-parts.edit', $sparePart) }}" class="btn-secondary min-h-[40px] text-xs inline-flex items-center gap-1.5 font-bold">
                <svg class="w-4 h-4 text-[#111111]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                <span>Edit Details</span>
            </a>
            <a href="{{ route('spare-parts.index') }}" class="btn-secondary min-h-[40px] text-xs inline-flex items-center gap-1.5">
                &larr; All Parts
            </a>
        </div>
    </div>

    <!-- 4 Key Metrics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Stock Available -->
        <div class="card-surface p-4 sm:p-5 flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between text-[#666666]">
                <span class="text-xs font-semibold uppercase tracking-wider">Current Stock</span>
                @if($sparePart->stock_quantity <= 0)
                    <span class="w-2 h-2 rounded-full bg-[#DC2626]"></span>
                @elseif($sparePart->isLowStock())
                    <span class="w-2 h-2 rounded-full bg-[#D97706] animate-pulse"></span>
                @else
                    <span class="w-2 h-2 rounded-full bg-[#079455]"></span>
                @endif
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-[28px] font-bold text-[#111111] tracking-tight font-mono">
                    {{ $sparePart->stock_quantity }} <span class="text-xs font-normal text-[#666666]">units</span>
                </div>
                <p class="text-[11px] text-[#929292] mt-0.5">Min threshold: {{ $sparePart->min_stock_level }} units</p>
            </div>
        </div>

        <!-- Repair Billing Price -->
        <div class="card-surface p-4 sm:p-5 flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between text-[#666666]">
                <span class="text-xs font-semibold uppercase tracking-wider">Repair Price</span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-[28px] font-bold text-[#111111] tracking-tight font-mono">
                    ₹{{ number_format($sparePart->selling_price, 2) }}
                </div>
                <p class="text-[11px] text-[#929292] mt-0.5">Base charge to customer</p>
            </div>
        </div>

        <!-- Inward Purchase Cost -->
        <div class="card-surface p-4 sm:p-5 flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between text-[#666666]">
                <span class="text-xs font-semibold uppercase tracking-wider">Procurement Cost</span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-[28px] font-bold text-[#111111] tracking-tight font-mono">
                    ₹{{ number_format($sparePart->purchase_price, 2) }}
                </div>
                <p class="text-[11px] text-[#929292] mt-0.5">Estimated margin: ₹{{ number_format(max(0, $sparePart->selling_price - $sparePart->purchase_price), 2) }}</p>
            </div>
        </div>

        <!-- Identification Codes -->
        <div class="card-surface p-4 sm:p-5 flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between text-[#666666]">
                <span class="text-xs font-semibold uppercase tracking-wider">SKU / Location</span>
            </div>
            <div class="mt-3">
                <div class="text-sm font-bold text-[#111111] font-mono truncate">
                    {{ $sparePart->sku ?: 'No SKU' }}
                </div>
                <p class="text-[11px] text-[#929292] mt-0.5 font-mono truncate">
                    Loc: {{ $sparePart->location ?: 'Not specified' }} &bull; BC: {{ $sparePart->barcode ?: 'None' }}
                </p>
            </div>
        </div>
    </div>

    <!-- 2-Column Info & Specifications Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Specifications & Details -->
        <div class="lg:col-span-2 card-surface p-5 sm:p-6 shadow-2xs space-y-4">
            <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Component Specifications &amp; Compatibility</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-[#666666] block text-[11px]">Brand / Manufacturer:</span>
                    <strong class="text-sm text-[#111111]">{{ $sparePart->brand ?: 'Generic / OEM' }}</strong>
                </div>
                <div>
                    <span class="text-[#666666] block text-[11px]">Category:</span>
                    <strong class="text-sm text-[#111111]">{{ $sparePart->category }}</strong>
                </div>
                <div class="sm:col-span-2">
                    <span class="text-[#666666] block text-[11px]">Compatible Phone Models:</span>
                    <div class="p-3 bg-[#F7F7F6] rounded-lg border border-[#EDEDEC] text-xs font-medium text-[#111111] mt-1">
                        {{ $sparePart->compatible_models ?: 'No specific models listed; universal component' }}
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <span class="text-[#666666] block text-[11px]">Technical Notes &amp; Specifications:</span>
                    <div class="p-3 bg-[#F7F7F6] rounded-lg border border-[#EDEDEC] text-xs text-[#333333] mt-1 leading-relaxed whitespace-pre-line">
                        {{ $sparePart->specifications ?: 'No technical specifications recorded.' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Supplier & Inventory Status -->
        <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-4 flex flex-col justify-between">
            <div>
                <div class="border-b border-[#E6E6E6] pb-3 mb-3">
                    <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Vendor &amp; Stock Controls</h3>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-[#666666] text-[11px] block">Supplier Information:</span>
                        @if($sparePart->supplier)
                            <div class="mt-1 font-bold text-[#111111]">{{ $sparePart->supplier->name }}</div>
                            @if($sparePart->supplier->company_name)<div class="text-[11px] text-[#666666]">{{ $sparePart->supplier->company_name }}</div>@endif
                            <div class="text-[11px] font-mono text-[#929292] mt-0.5">{{ $sparePart->supplier->phone }}</div>
                        @else
                            <div class="text-xs text-[#929292] mt-1 italic">Direct / No supplier linked</div>
                        @endif
                    </div>

                    <div class="pt-2 border-t border-[#EDEDEC]">
                        <span class="text-[#666666] text-[11px] block">Barcode String:</span>
                        <div class="font-mono text-xs font-bold text-[#111111] mt-0.5">{{ $sparePart->barcode ?: 'None' }}</div>
                    </div>

                    <div class="pt-2 border-t border-[#EDEDEC]">
                        <span class="text-[#666666] text-[11px] block">Catalogue Created:</span>
                        <div class="font-mono text-[11px] text-[#666666] mt-0.5">{{ $sparePart->created_at->format('d M Y, h:i A') }}</div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone: Delete Part -->
            <div class="pt-4 border-t border-[#EDEDEC]">
                <form action="{{ route('spare-parts.destroy', $sparePart) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this spare part from catalogue?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-[#DC2626] hover:text-[#B91C1C] font-bold flex items-center gap-1.5 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        <span>Delete From Catalogue</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Stock Movement History Table -->
    <div class="card-surface shadow-2xs overflow-hidden space-y-3 p-4 sm:p-5">
        <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
            <div>
                <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Inventory Movement &amp; Audit Trail</h3>
                <p class="text-xs text-[#666666]">Complete record of inward deliveries, manual adjustments, and repair consumptions</p>
            </div>
            <button type="button" onclick="openAdjustModal()" class="btn-secondary text-xs min-h-[34px] px-3 font-bold">
                + New Adjustment
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F7F7F6] text-[#666666] uppercase tracking-wider font-bold border-b border-[#E6E6E6]">
                    <tr>
                        <th class="py-3 px-3">Date &amp; Time</th>
                        <th class="py-3 px-3">Action Type</th>
                        <th class="py-3 px-3 text-center">Change</th>
                        <th class="py-3 px-3 text-center">Before &rarr; After</th>
                        <th class="py-3 px-3">Associated Repair Job</th>
                        <th class="py-3 px-3">Logged By</th>
                        <th class="py-3 px-3">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E6E6E6] text-[#344054]">
                    @forelse($sparePart->stockMovements as $mov)
                        <tr class="hover:bg-[#F7F7F6]/60 transition">
                            <td class="py-3 px-3 font-mono text-[#666666]">
                                {{ $mov->created_at->format('d M Y, h:i A') }}
                            </td>
                            <td class="py-3 px-3">
                                @php
                                    $movBadge = match($mov->type) {
                                        'inward', 'manual_add', 'returned', 'repair_restored' => 'bg-[#ECFDF3] text-[#027A48] border-[#A6F4C5]',
                                        'damaged', 'manual_reduce' => 'bg-[#FEF2F2] text-[#B91C1C] border-[#FECACA]',
                                        'repair_consumed' => 'bg-[#EFF8FF] text-[#175CD3] border-[#B2DDFF]',
                                        default => 'bg-[#F2F4F7] text-[#344054] border-[#EAECF0]',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold border {{ $movBadge }}">
                                    {{ ucfirst(str_replace('_', ' ', $mov->type)) }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center font-mono font-bold {{ $mov->quantity_change > 0 ? 'text-[#079455]' : ($mov->quantity_change < 0 ? 'text-[#DC2626]' : 'text-[#666666]') }}">
                                {{ $mov->quantity_change > 0 ? '+' . $mov->quantity_change : $mov->quantity_change }}
                            </td>
                            <td class="py-3 px-3 text-center font-mono text-xs">
                                {{ $mov->quantity_before }} &rarr; <strong class="text-[#111111]">{{ $mov->quantity_after }}</strong>
                            </td>
                            <td class="py-3 px-3">
                                @if($mov->repairJob)
                                    <a href="{{ route('repairs.show', $mov->repairJob) }}" class="font-bold text-[#111111] hover:underline font-mono">
                                        #{{ $mov->repairJob->job_number }}
                                    </a>
                                    @if($mov->repairJob->customer)
                                        <div class="text-[10px] text-[#666666]">{{ $mov->repairJob->customer->name }}</div>
                                    @endif
                                @else
                                    <span class="text-[#929292]">&mdash;</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-[#666666]">
                                {{ $mov->user?->name ?? 'System' }}
                            </td>
                            <td class="py-3 px-3 text-[#666666]">
                                {{ $mov->notes ?: 'Stock adjustment' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-xs text-[#929292]">
                                No stock movement records yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Stock Adjustment Modal -->
<div id="adjustStockModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 hidden p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl space-y-4 relative border border-[#E6E6E6]">
        <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
            <h3 class="text-sm font-bold text-[#111111] uppercase tracking-wider">Adjust Stock: {{ $sparePart->name }}</h3>
            <button type="button" onclick="closeAdjustModal()" class="text-[#666666] hover:text-[#111111]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-3 rounded-lg bg-[#F7F7F6] border border-[#EDEDEC] text-xs">
            Current Stock Available: <strong class="font-mono text-sm text-[#111111] ml-1">{{ $sparePart->stock_quantity }} units</strong>
        </div>

        <form method="POST" action="{{ route('spare-parts.adjust', $sparePart) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-[#344054] mb-1">Adjustment Type *</label>
                <select name="adjustment_type" required class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-bold text-[#111111]">
                    <option value="manual_add">Add Stock (Inward / Restock)</option>
                    <option value="manual_reduce">Reduce Stock (Manual Correction)</option>
                    <option value="manual_set">Set Exact Count (Physical Inventory Count)</option>
                    <option value="damaged">Mark as Damaged / Defective</option>
                    <option value="returned">Customer / Warranty Return</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#344054] mb-1">Quantity *</label>
                <input type="number" name="quantity" min="0" required placeholder="Quantity to adjust or set"
                    class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-bold font-mono text-[#111111]">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#344054] mb-1">Reason / Notes</label>
                <input type="text" name="reason" placeholder="e.g. Received new shipment, physical count discrepancy..."
                    class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111]">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#EDEDEC]">
                <button type="button" onclick="closeAdjustModal()" class="btn-secondary min-h-[38px] text-xs">Cancel</button>
                <button type="submit" class="btn-lime min-h-[38px] text-xs font-bold px-5">Confirm Adjustment</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAdjustModal() {
        document.getElementById('adjustStockModal').classList.remove('hidden');
    }
    function closeAdjustModal() {
        document.getElementById('adjustStockModal').classList.add('hidden');
    }
</script>
@endsection

