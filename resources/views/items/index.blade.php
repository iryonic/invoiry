@extends('layouts.app')

@section('title', 'Devices & Inventory')
@section('page-title', 'Devices / Stock')
@section('page-subtitle', 'Manage phone hardware, IMEIs, inward purchasing costs, and available stock')

@section('content')
<div class="space-y-6 w-full" style="overflow-x: clip;">

    <!-- Top Action & Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-[#111111] tracking-tight">Devices & Stock Inventory</h2>
            <p class="text-xs sm:text-sm text-[#666666] mt-0.5">Track all purchased, inward, and sold electronic hardware items</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('items.create') }}" class="btn-lime px-4 py-2.5 rounded-lg text-xs sm:text-sm font-bold flex items-center gap-2 shadow-xs whitespace-nowrap min-h-[44px]">
                <svg class="w-4 h-4 text-[#101010]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Add Device</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class=" p-2 grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ route('items.index', ['status' => 'all']) }}" class="card-surface p-4 flex flex-col justify-between shadow-2xs transition {{ $status === 'all' ? 'border-[#111111] ring-1 ring-[#111111]' : '' }}">
            <span class="text-xs font-bold text-[#111111] uppercase tracking-wider">All Devices</span>
            <div class="text-xl sm:text-2xl font-bold text-[#111111] mt-1">{{ $stats['in_stock'] + $stats['sold'] }} devices</div>
        </a>
        <a href="{{ route('items.index', ['status' => 'in_stock']) }}" class="card-surface p-4 flex flex-col justify-between shadow-2xs transition {{ $status === 'in_stock' ? 'border-[#079455] ring-1 ring-[#079455]' : '' }}">
            <span class="text-xs font-bold text-[#079455] uppercase tracking-wider">Available Stock</span>
            <div class="text-xl sm:text-2xl font-bold text-[#111111] mt-1">{{ $stats['in_stock'] }} devices</div>
        </a>
        <a href="{{ route('items.index', ['status' => 'sold']) }}" class="card-surface p-4 flex flex-col justify-between shadow-2xs transition {{ $status === 'sold' ? 'border-[#666666] ring-1 ring-[#666666]' : '' }}">
            <span class="text-xs font-bold text-[#666666] uppercase tracking-wider">Sold Devices</span>
            <div class="text-xl sm:text-2xl font-bold text-[#111111] mt-1">{{ $stats['sold'] }} devices</div>
        </a>
        <div class="card-surface p-4 flex flex-col justify-between shadow-2xs">
            <span class="text-xs font-bold text-[#111111] uppercase tracking-wider">Total Stock Asset</span>
            <div class="text-xl sm:text-2xl font-bold text-[#111111] mt-1">₹{{ number_format($stats['total_value'], 0) }}</div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card-surface p-4 shadow-2xs">
        <form method="GET" action="{{ route('items.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by IMEI 1, IMEI 2, Serial, Brand, or Model..."
                    class="w-full pl-9 pr-4 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black min-h-[40px]">
                <svg class="w-4 h-4 text-[#929292] absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                <select name="status" class="px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-1 focus:ring-black min-h-[40px]">
                    <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Status (Complete Catalogue)</option>
                    <option value="in_stock" {{ request('status') === 'in_stock' ? 'selected' : '' }}>In Stock Only</option>
                    <option value="sold" {{ request('status') === 'sold' ? 'selected' : '' }}>Sold Only</option>
                </select>

                <select name="condition" class="px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-1 focus:ring-black min-h-[40px]">
                    <option value="all">All Conditions</option>
                    <option value="new" {{ request('condition') === 'new' ? 'selected' : '' }}>New</option>
                    <option value="used" {{ request('condition') === 'used' ? 'selected' : '' }}>Used</option>
                    <option value="refurbished" {{ request('condition') === 'refurbished' ? 'selected' : '' }}>Refurbished</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-[#050505] hover:bg-[#1a1a1a] text-white rounded-lg text-xs sm:text-sm font-semibold min-h-[40px] transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'condition']) || (request('status') && request('status') !== 'all'))
                    <a href="{{ route('items.index') }}" class="px-3 py-2 text-[#666666] hover:text-[#111111] text-xs font-semibold min-h-[40px] inline-flex items-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Items List / Table -->
    @if($items->isEmpty())
        <div class="card-surface p-12 text-center shadow-2xs">
            <div class="w-12 h-12 rounded-full bg-[#F2F4F7] text-[#666666] flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            </div>
            <h3 class="text-sm font-bold text-[#111111]">No devices found</h3>
            <p class="text-xs text-[#666666] mt-1 mb-4">Try clearing filters or add a new inward device to stock.</p>
            <a href="{{ route('items.create') }}" class="btn-lime px-4 py-2 rounded-lg text-xs font-bold inline-flex items-center gap-1.5 shadow-2xs min-h-[40px]">
                Add Device
            </a>
        </div>
    @else
        <!-- Mobile Select All Bar -->
        <div class="flex items-center justify-between p-3 bg-white border border-[#E6E6E6] rounded-xl text-xs md:hidden shadow-2xs">
            <label class="flex items-center gap-2.5 font-bold text-[#111111] cursor-pointer">
                <input type="checkbox" data-bulk-select-all class="rounded border-[#D0D5DD] text-black focus:ring-black w-4 h-4">
                <span>Select All on Page</span>
            </label>
            <span class="text-[11px] text-[#666666] font-mono">{{ $items->total() }} devices</span>
        </div>

        <!-- Mobile Card View -->
        <div class="block md:hidden space-y-3 items-mobile-cards">
            @foreach($items as $item)
                <div class="card-surface p-4 space-y-3 shadow-2xs transition" data-card-item>
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-2.5 min-w-0 flex-1">
                            <label class="mt-0.5 cursor-pointer p-1 -m-1 inline-flex items-center shrink-0">
                                <input type="checkbox" data-bulk-item value="{{ $item->id }}" class="rounded border-[#D0D5DD] text-black focus:ring-black w-4 h-4">
                            </label>
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
                    </div>

                        <div class="text-right flex-shrink-0">
                            @if($item->status === 'in_stock')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold badge-paid">
                                    <span>In Stock</span>
                                    <span class="font-mono">({{ $item->quantity }})</span>
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold badge-neutral">Sold</span>
                            @endif
                            <div class="text-sm font-bold text-[#111111] mt-1">₹{{ number_format($item->selling_price > 0 ? $item->selling_price : ($item->purchase_price ?: 0), 2) }}</div>
                            @if($item->purchase_price > 0 && $item->selling_price > 0)
                                <div class="text-[10px] text-[#929292]">Cost: ₹{{ number_format($item->purchase_price, 2) }}</div>
                            @endif
                            <span class="text-[10px] text-[#929292] capitalize">{{ $item->condition }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-[#E6E6E6] text-xs">
                        <span class="text-[11px] text-[#929292]">
                            {{ $item->supplier ? $item->supplier->name : 'Walk-in' }} &bull; {{ $item->purchase_date?->format('d M Y') ?? 'N/A' }}
                        </span>

                        <div class="flex items-center gap-2">
                            @if($item->status === 'in_stock')
                                <a href="{{ route('invoices.create', ['preselect_item' => $item->id]) }}" class="btn-lime px-3 py-1 rounded-md text-xs font-bold shadow-2xs min-h-[32px] inline-flex items-center">
                                    Sell
                                </a>
                            @endif
                            <a href="{{ route('items.edit', $item) }}" class="p-1.5 text-[#666666] hover:text-[#111111] rounded hover:bg-[#F2F4F7] min-h-[32px] min-w-[32px] flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block card-surface overflow-hidden shadow-2xs items-desktop-table">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F7F7F6] border-b border-[#E6E6E6] text-[#666666] uppercase tracking-wider font-semibold">
                        <tr>
                            <th class="py-3 px-3 w-10 text-center">
                                <input type="checkbox" data-bulk-select-all class="rounded border-[#D0D5DD] text-black focus:ring-black">
                            </th>
                            <th class="py-3 px-4">Device / Model</th>
                            <th class="py-3 px-4">IMEI / Identifiers</th>
                            <th class="py-3 px-4">Condition</th>
                            <th class="py-3 px-4">Supplier</th>
                            <th class="py-3 px-4">Cost Price</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E6E6E6] text-[#111111]">
                        @foreach($items as $item)
                            <tr class="hover:bg-[#F7F7F6]/80 transition">
                                <td class="py-3.5 px-3 text-center">
                                    <input type="checkbox" data-bulk-item value="{{ $item->id }}" class="rounded border-[#D0D5DD] text-black focus:ring-black">
                                </td>
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
                                <td class="py-3.5 px-4 capitalize">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ $item->condition === 'new' ? 'badge-paid' : 'badge-neutral' }}">
                                        {{ $item->condition }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-[#666666]">
                                    {{ $item->supplier ? $item->supplier->name : 'Walk-in' }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-[#111111]">
                                    <div>₹{{ number_format($item->selling_price > 0 ? $item->selling_price : ($item->purchase_price ?: 0), 2) }}</div>
                                    @if($item->purchase_price > 0 && $item->selling_price > 0)
                                        <div class="text-[10px] text-[#929292] font-normal">Cost: ₹{{ number_format($item->purchase_price, 2) }}</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-[#666666]">
                                    {{ $item->purchase_date?->format('d M Y') ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($item->status === 'in_stock')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold badge-paid">
                                            <span>In Stock</span>
                                            <span class="font-mono">({{ $item->quantity }})</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold badge-neutral">Sold</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($item->status === 'in_stock')
                                            <a href="{{ route('invoices.create', ['preselect_item' => $item->id]) }}" class="btn-lime px-2.5 py-1 rounded text-xs font-bold shadow-2xs min-h-[30px] inline-flex items-center">
                                                Sell
                                            </a>
                                        @endif
                                        <a href="{{ route('items.edit', $item) }}" class="p-1.5 text-[#666666] hover:text-[#111111] rounded hover:bg-[#F2F4F7]" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        @if($item->status !== 'sold')
                                            <form action="{{ route('items.destroy', $item) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" data-confirm="Delete this device from inventory?" data-confirm-title="Delete Stock Item" class="p-1.5 text-[#DC2626] hover:bg-[#FEF2F2] rounded" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        @endif
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

    <!-- Reusable Bulk Action Bar for Inventory Items -->
    <x-bulk-action-bar :action="route('items.bulk')" id="bulk-items-form">
        @if(auth()->user()->hasPermission('items.edit'))
            <button type="button" data-bulk-action="mark_sold" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition active:scale-95 shrink-0">
                Mark Sold
            </button>
            <button type="button" data-bulk-action="mark_in_stock" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition active:scale-95 shrink-0">
                In Stock
            </button>
        @endif
        @if(auth()->user()->hasPermission('items.delete'))
            <button type="button" data-bulk-action="delete" data-confirm="Are you sure you want to delete :count selected device(s)?" data-confirm-title="Delete Selected Devices" data-confirm-variant="danger" class="px-3.5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs transition shadow-xs active:scale-95 shrink-0">
                Delete
            </button>
        @endif
    </x-bulk-action-bar>
</div>
@endsection
