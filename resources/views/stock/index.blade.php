@extends('layouts.app')

@section('title', 'Device Stock & Inventory Management')
@section('page-title', 'Device Stock')
@section('page-subtitle', 'Manage device quantities, adjust physical stock levels, and audit inventory movements')

@section('content')
<div class="space-y-6 w-full" style="overflow-x: clip;">

    <!-- Top Action & Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-[#111111] tracking-tight">Stock Management & Ledger</h2>
            <p class="text-xs sm:text-sm text-[#666666] mt-0.5">Control live quantities, record adjustments with reasons, and track movement audit trail</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('items.create') }}" class="btn-lime px-4 py-2.5 rounded-lg text-xs sm:text-sm font-bold flex items-center gap-2 shadow-xs whitespace-nowrap min-h-[44px]">
                <svg class="w-4 h-4 text-[#101010]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Inward New Stock</span>
            </a>
            <a href="{{ route('items.index') }}" class="btn-secondary px-3.5 py-2.5 rounded-lg text-xs sm:text-sm font-semibold flex items-center gap-2 min-h-[44px]">
                <span>Devices Catalog</span>
            </a>
        </div>
    </div>

    <!-- Quick Stock KPI Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <a href="{{ route('stock.index', ['status' => 'all']) }}" class="card-surface p-4 shadow-2xs transition {{ $status === 'all' ? 'border-[#111111] ring-1 ring-[#111111]' : '' }}">
            <span class="text-[11px] font-bold text-[#666666] uppercase tracking-wider">Registered Models</span>
            <div class="text-xl sm:text-2xl font-bold text-[#111111] mt-1">{{ $stats['total_items'] }}</div>
        </a>

        <a href="{{ route('stock.index', ['status' => 'in_stock']) }}" class="card-surface p-4 shadow-2xs transition {{ $status === 'in_stock' ? 'border-[#079455] ring-1 ring-[#079455]' : '' }}">
            <span class="text-[11px] font-bold text-[#079455] uppercase tracking-wider">Available Units</span>
            <div class="text-xl sm:text-2xl font-bold text-[#079455] mt-1">{{ $stats['total_units_in_stock'] }} in stock</div>
        </a>

        <a href="{{ route('stock.index', ['status' => 'low_stock']) }}" class="card-surface p-4 shadow-2xs transition {{ $status === 'low_stock' ? 'border-[#D97706] ring-1 ring-[#D97706]' : '' }}">
            <span class="text-[11px] font-bold text-[#D97706] uppercase tracking-wider">Low Stock (&le; 2)</span>
            <div class="text-xl sm:text-2xl font-bold text-[#D97706] mt-1">{{ $stats['low_stock_count'] }} devices</div>
        </a>

        <a href="{{ route('stock.index', ['status' => 'out_of_stock']) }}" class="card-surface p-4 shadow-2xs transition {{ $status === 'out_of_stock' ? 'border-[#DC2626] ring-1 ring-[#DC2626]' : '' }}">
            <span class="text-[11px] font-bold text-[#DC2626] uppercase tracking-wider">Out of Stock / Sold</span>
            <div class="text-xl sm:text-2xl font-bold text-[#DC2626] mt-1">{{ $stats['out_of_stock_count'] }}</div>
        </a>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card-surface p-4 shadow-2xs">
        <form method="GET" action="{{ route('stock.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by Device Name, Brand, IMEI 1, IMEI 2, or Serial..."
                    class="w-full pl-9 pr-4 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black min-h-[40px]">
                <svg class="w-4 h-4 text-[#929292] absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                <select name="status" class="px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-1 focus:ring-black min-h-[40px]">
                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Devices</option>
                    <option value="in_stock" {{ request('status') === 'in_stock' ? 'selected' : '' }}>In Stock Only</option>
                    <option value="low_stock" {{ request('status') === 'low_stock' ? 'selected' : '' }}>Low Stock (&le; 2)</option>
                    <option value="out_of_stock" {{ request('status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock / Sold</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-[#050505] hover:bg-[#1a1a1a] text-white rounded-lg text-xs sm:text-sm font-semibold min-h-[40px] transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('stock.index') }}" class="px-3 py-2 text-[#666666] hover:text-[#111111] text-xs font-semibold min-h-[40px] inline-flex items-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Stock Management Table -->
    @if($items->isEmpty())
        <div class="card-surface p-12 text-center shadow-2xs">
            <div class="w-12 h-12 rounded-full bg-[#F2F4F7] text-[#666666] flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <h3 class="text-sm font-bold text-[#111111]">No devices match your filter</h3>
            <p class="text-xs text-[#666666] mt-1 mb-4">Try clearing your search query or inward a new device batch.</p>
            <a href="{{ route('items.create') }}" class="btn-lime px-4 py-2 rounded-lg text-xs font-bold inline-flex items-center gap-1.5 shadow-2xs min-h-[40px]">
                Inward Device
            </a>
        </div>
    @else
        <!-- Mobile Card View -->
        <div class="block md:hidden space-y-3">
            @foreach($items as $item)
                @php
                    $qty = (int) $item->quantity;
                    $badgeClass = $qty > 2 ? 'badge-paid' : ($qty > 0 ? 'badge-due' : 'badge-neutral');
                    $stockText = $qty > 2 ? "In Stock ({$qty})" : ($qty > 0 ? "Low Stock ({$qty})" : "Out of Stock (0)");
                @endphp
                <div class="card-surface p-4 space-y-3 shadow-2xs">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <h4 class="text-sm font-bold text-[#111111]">{{ $item->item_name }}</h4>
                                @if($item->storage)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-[#F2F4F7] text-[#344054] font-semibold font-mono">{{ $item->storage }}</span>
                                @endif
                                @if($item->color)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-[#F2F4F7] text-[#666666]">{{ $item->color }}</span>
                                @endif
                            </div>

                            <div class="text-xs font-mono text-[#666666] mt-1 space-y-0.5">
                                @if($item->imei_1)
                                    <div>IMEI 1: <strong class="text-[#111111]">{{ $item->imei_1 }}</strong></div>
                                @endif
                                @if($item->imei_2)
                                    <div class="text-[11px] text-[#929292]">IMEI 2: {{ $item->imei_2 }}</div>
                                @endif
                                @if($item->serial_number)
                                    <div class="text-[11px] text-[#929292]">SN: {{ $item->serial_number }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="text-right flex-shrink-0">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $badgeClass }}">
                                {{ $stockText }}
                            </span>
                            <div class="text-sm font-bold text-[#111111] mt-1">₹{{ number_format($item->selling_price ?: $item->purchase_price, 2) }}</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-[#E6E6E6] text-xs">
                        <button type="button" onclick="openHistoryModal({{ $item->id }})" class="text-[#666666] hover:text-[#111111] underline text-[11px] font-semibold min-h-[32px] inline-flex items-center">
                            Movement History
                        </button>

                        <button type="button" onclick="openAdjustModal({{ $item->id }}, '{{ addslashes($item->item_name) }}', {{ $item->quantity }})" class="btn-lime px-3 py-1 rounded-md text-xs font-bold shadow-2xs min-h-[32px] inline-flex items-center gap-1">
                            Adjust Stock
                        </button>
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
                            <th class="py-3 px-4">Device / Hardware</th>
                            <th class="py-3 px-4">IMEI & Identifiers</th>
                            <th class="py-3 px-4">Brand / Category</th>
                            <th class="py-3 px-4 text-center">Available Stock</th>
                            <th class="py-3 px-4">Stock Status</th>
                            <th class="py-3 px-4 text-right">Cost Price</th>
                            <th class="py-3 px-4 text-right">Selling Price</th>
                            <th class="py-3 px-4 text-right">Stock Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E6E6E6] text-[#111111]">
                        @foreach($items as $item)
                            @php
                                $qty = (int) $item->quantity;
                                $badgeClass = $qty > 2 ? 'badge-paid' : ($qty > 0 ? 'badge-due' : 'badge-neutral');
                                $stockText = $qty > 2 ? 'In Stock' : ($qty > 0 ? 'Low Stock' : 'Out of Stock');
                            @endphp
                            <tr class="hover:bg-[#F7F7F6]/80 transition">
                                <td class="py-3.5 px-4 font-semibold text-[#111111]">
                                    <div class="flex items-center gap-1.5">
                                        <span>{{ $item->item_name }}</span>
                                        @if($item->storage)
                                            <span class="text-[10px] px-1.5 py-0.2 rounded bg-[#F2F4F7] font-mono text-[#344054]">{{ $item->storage }}</span>
                                        @endif
                                    </div>
                                    @if($item->color)
                                        <span class="text-[11px] text-[#929292]">{{ $item->color }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 font-mono text-[11px]">
                                    @if($item->imei_1)
                                        <div class="text-[#111111] font-semibold">{{ $item->imei_1 }}</div>
                                    @endif
                                    @if($item->imei_2)
                                        <div class="text-[#929292]">IMEI 2: {{ $item->imei_2 }}</div>
                                    @endif
                                    @if($item->serial_number)
                                        <div class="text-[#929292]">SN: {{ $item->serial_number }}</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-[#666666]">
                                    <div>{{ $item->brand ?: 'Standard' }}</div>
                                    <div class="text-[11px] text-[#929292]">{{ $item->category ?: 'Mobile' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[28px] h-7 px-2 rounded-full font-mono font-black text-xs {{ $qty > 0 ? 'bg-[#050505] text-[#B7FF00]' : 'bg-[#F2F4F7] text-[#929292]' }}">
                                        {{ $qty }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $badgeClass }}">
                                        {{ $stockText }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-medium text-[#666666]">
                                    ₹{{ number_format($item->purchase_price, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-[#111111]">
                                    ₹{{ number_format($item->selling_price ?: $item->purchase_price, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" onclick="openHistoryModal({{ $item->id }})" class="p-1.5 text-[#666666] hover:text-[#111111] rounded hover:bg-[#F2F4F7]" title="View Stock Movement Audit Trail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </button>
                                        <button type="button" onclick="openAdjustModal({{ $item->id }}, '{{ addslashes($item->item_name) }}', {{ $item->quantity }})" class="btn-lime px-2.5 py-1 rounded text-xs font-bold shadow-2xs min-h-[30px] inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                            <span>Adjust</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($items->hasPages())
                <div class="p-3.5 border-t border-[#E6E6E6]">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- Recent Store-wide Movements Ledger -->
    @if($recentMovements->isNotEmpty())
        <div class="card-surface p-5 shadow-2xs space-y-3">
            <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-2.5">
                <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <span>Recent Stock Movement Audit Trail</span>
                </h3>
                <span class="text-[11px] text-[#929292]">Real-time traceability</span>
            </div>

            <div class="divide-y divide-[#E6E6E6]">
                @foreach($recentMovements as $mov)
                    <div class="py-2.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                        <div class="flex items-center gap-2.5">
                            <span class="inline-flex items-center justify-center min-w-[28px] h-6 px-1.5 rounded font-mono font-bold text-[11px] {{ $mov->quantity_change > 0 ? 'bg-[#ECFDF3] text-[#027A48]' : 'bg-[#FEF3F2] text-[#B42318]' }}">
                                {{ $mov->quantity_change > 0 ? '+'.$mov->quantity_change : $mov->quantity_change }}
                            </span>
                            <div>
                                <span class="font-bold text-[#111111]">{{ $mov->item?->item_name ?: 'Device' }}</span>
                                @if($mov->item?->imei_1)
                                    <span class="font-mono text-[11px] text-[#929292]">&bull; {{ $mov->item->imei_1 }}</span>
                                @endif
                                <span class="text-[#666666] ml-1">({{ $mov->reason ?: ucwords(str_replace('_', ' ', $mov->adjustment_type)) }})</span>
                            </div>
                        </div>

                        <div class="text-[11px] text-[#929292] flex items-center gap-3">
                            <span>Balance: <strong class="text-[#111111] font-mono">{{ $mov->quantity_after }}</strong></span>
                            <span>&bull;</span>
                            <span>{{ $mov->user?->name ?: 'System' }}</span>
                            <span>&bull;</span>
                            <span>{{ $mov->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- Stock Adjustment Modal -->
<div id="adjustStockModal" class="modal-backdrop hidden" onclick="if(event.target === this) closeAdjustModal()">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl space-y-4 border border-[#E6E6E6] my-auto">
        <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
            <div>
                <h3 class="text-sm font-bold text-[#111111]" id="modalItemTitle">Adjust Device Stock</h3>
                <p class="text-[11px] text-[#666666]" id="modalCurrentStockText">Current available quantity: 0</p>
            </div>
            <button type="button" onclick="closeAdjustModal()" class="text-[#929292] hover:text-[#111111] text-lg">&times;</button>
        </div>

        <form method="POST" id="adjustStockForm" action="" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-[#111111] mb-1">Adjustment Action *</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="adjustment_type" value="add" checked class="peer sr-only" onchange="updateQtyLabel()">
                        <div class="px-3 py-2 text-center rounded-lg border border-[#E6E6E6] text-xs font-bold peer-checked:bg-[#050505] peer-checked:text-[#B7FF00] peer-checked:border-black transition">
                            + Add Stock
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="adjustment_type" value="reduce" class="peer sr-only" onchange="updateQtyLabel()">
                        <div class="px-3 py-2 text-center rounded-lg border border-[#E6E6E6] text-xs font-bold peer-checked:bg-[#050505] peer-checked:text-[#B7FF00] peer-checked:border-black transition">
                            - Reduce Stock
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="adjustment_type" value="set" class="peer sr-only" onchange="updateQtyLabel()">
                        <div class="px-3 py-2 text-center rounded-lg border border-[#E6E6E6] text-xs font-bold peer-checked:bg-[#050505] peer-checked:text-[#B7FF00] peer-checked:border-black transition">
                            = Set Quantity
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label id="quantityInputLabel" class="block text-xs font-semibold text-[#111111] mb-1">Units to Add *</label>
                <input type="number" name="quantity" id="adjustQuantityInput" required min="1" step="1" value="1"
                    class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-sm font-bold font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black min-h-[44px]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#111111] mb-1">Reason for Adjustment *</label>
                <select name="reason_preset" onchange="if(this.value) document.getElementById('adjustReasonInput').value = this.value" class="w-full px-3 py-1.5 mb-1.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-md text-xs focus:outline-none">
                    <option value="">-- Choose common reason or type below --</option>
                    <option value="Physical count audit adjustment">Physical count audit adjustment</option>
                    <option value="New stock batch arrival">New stock batch arrival</option>
                    <option value="Damaged / defective unit write-off">Damaged / defective unit write-off</option>
                    <option value="Customer warranty exchange return">Customer warranty exchange return</option>
                    <option value="Display / demo unit assignment">Display / demo unit assignment</option>
                </select>
                <input type="text" name="reason" id="adjustReasonInput" required placeholder="Describe the reason (e.g. Audit correction)"
                    class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs focus:bg-white focus:outline-none focus:ring-1 focus:ring-black min-h-[44px]">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#E6E6E6]">
                <button type="button" onclick="closeAdjustModal()" class="btn-secondary px-4 py-2 text-xs font-semibold min-h-[40px]">
                    Cancel
                </button>
                <button type="submit" class="btn-lime px-5 py-2 text-xs font-bold min-h-[40px]">
                    Confirm Stock Update
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Stock Movement History Modal -->
<div id="historyModal" class="modal-backdrop hidden" onclick="if(event.target === this) closeHistoryModal()">
    <div class="bg-white rounded-2xl p-6 w-full max-w-xl shadow-2xl space-y-4 border border-[#E6E6E6] my-auto max-h-[85vh] flex flex-col">
        <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
            <div>
                <h3 class="text-sm font-bold text-[#111111]" id="historyModalTitle">Stock Movement Audit History</h3>
                <p class="text-[11px] text-[#666666]" id="historyModalSubtitle">Traceable movement ledger</p>
            </div>
            <button type="button" onclick="closeHistoryModal()" class="text-[#929292] hover:text-[#111111] text-lg">&times;</button>
        </div>

        <div id="historyContent" class="overflow-y-auto flex-1 space-y-2 pr-1 text-xs">
            <div class="text-center py-6 text-[#929292]">Loading audit records...</div>
        </div>

        <div class="pt-2 border-t border-[#E6E6E6] text-right">
            <button type="button" onclick="closeHistoryModal()" class="btn-secondary px-4 py-2 text-xs font-semibold">
                Close
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentItemQty = 0;

    function openAdjustModal(itemId, itemName, currentQty) {
        currentItemQty = currentQty;
        document.getElementById('modalItemTitle').innerText = 'Adjust Stock · ' + itemName;
        document.getElementById('modalCurrentStockText').innerText = 'Current available quantity: ' + currentQty + ' unit(s)';
        document.getElementById('adjustStockForm').action = '/stock/' + itemId + '/adjust';
        document.getElementById('adjustQuantityInput').value = '1';
        document.getElementById('adjustReasonInput').value = '';
        updateQtyLabel();

        const modal = document.getElementById('adjustStockModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeAdjustModal() {
        const modal = document.getElementById('adjustStockModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function updateQtyLabel() {
        const type = document.querySelector('input[name="adjustment_type"]:checked')?.value || 'add';
        const label = document.getElementById('quantityInputLabel');
        const input = document.getElementById('adjustQuantityInput');
        if (type === 'add') {
            label.innerText = 'Units to Add (+) *';
            input.min = '1';
        } else if (type === 'reduce') {
            label.innerText = 'Units to Remove (-) *';
            input.min = '1';
            input.max = currentItemQty.toString();
        } else {
            label.innerText = 'New Absolute Quantity (=) *';
            input.min = '0';
            input.removeAttribute('max');
        }
    }

    function openHistoryModal(itemId) {
        const modal = document.getElementById('historyModal');
        const content = document.getElementById('historyContent');
        content.innerHTML = '<div class="text-center py-6 text-[#929292]">Loading audit records...</div>';
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        fetch('/stock/' + itemId + '/history')
            .then(res => res.json())
            .then(data => {
                document.getElementById('historyModalTitle').innerText = data.item_name;
                document.getElementById('historyModalSubtitle').innerText = 'Current Quantity: ' + data.current_quantity + (data.imei_1 ? ' · IMEI: ' + data.imei_1 : '');

                if (!data.history || data.history.length === 0) {
                    content.innerHTML = '<div class="text-center py-6 text-[#929292]">No past stock adjustments recorded for this device.</div>';
                    return;
                }

                let html = '<div class="divide-y divide-[#E6E6E6]">';
                data.history.forEach(h => {
                    const isPositive = h.change.startsWith('+');
                    const badgeClass = isPositive ? 'bg-[#ECFDF3] text-[#027A48]' : 'bg-[#FEF3F2] text-[#B42318]';
                    html += `
                        <div class="py-2.5 flex items-start justify-between gap-3">
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center justify-center min-w-[24px] px-1.5 py-0.5 rounded text-[11px] font-mono font-bold ${badgeClass}">
                                        ${h.change}
                                    </span>
                                    <span class="font-bold text-[#111111]">${h.reason}</span>
                                </div>
                                <div class="text-[11px] text-[#666666]">
                                    Stock: <span class="font-mono">${h.before}</span> &rarr; <span class="font-mono font-bold text-[#111111]">${h.after}</span>
                                    &bull; Recorded by <span class="font-medium text-[#111111]">${h.user}</span>
                                </div>
                            </div>
                            <span class="text-[11px] text-[#929292] shrink-0 font-mono">${h.date}</span>
                        </div>
                    `;
                });
                html += '</div>';
                content.innerHTML = html;
            })
            .catch(() => {
                content.innerHTML = '<div class="text-center py-6 text-red-600">Failed to load movement history. Please try again.</div>';
            });
    }

    function closeHistoryModal() {
        const modal = document.getElementById('historyModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endpush
@endsection

