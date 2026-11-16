@extends('layouts.app')

@section('title', 'Spare Parts Catalogue · ' . ($tenant->name ?? 'Mobile Repair'))
@section('page-title', 'Spare Parts Catalogue')
@section('page-subtitle', 'Manage phone components, screens, batteries, compatibility, and available stock')

@section('content')
<div class="space-y-6 w-full" style="overflow-x: clip;">

    <!-- Top Action & Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-[#FEF0C7] text-[#B54708] mb-1 border border-[#FEDF89]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#D97706]"></span>
                <span>Repair Hardware Inventory</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-[#111111] tracking-tight">Spare Parts &amp; Components</h2>
            <p class="text-xs sm:text-sm text-[#666666] mt-0.5">Replacement screens, batteries, charging ports, ICs, and workshop materials</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('spare-parts.stock') }}" class="btn-secondary px-4 py-2.5 rounded-lg text-xs sm:text-sm font-semibold flex items-center gap-2 whitespace-nowrap min-h-[44px]">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <span>Stock Adjustments</span>
            </a>
            <a href="{{ route('spare-parts.create') }}" class="btn-lime px-4 py-2.5 rounded-lg text-xs sm:text-sm font-bold flex items-center gap-2 shadow-xs whitespace-nowrap min-h-[44px]">
                <svg class="w-4 h-4 text-[#101010]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Add Spare Part</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ route('spare-parts.index', ['stock_status' => 'all']) }}" class="card-surface p-4 flex flex-col justify-between shadow-2xs transition {{ $stockStatus === 'all' ? 'border-[#111111] ring-1 ring-[#111111]' : '' }}">
            <span class="text-xs font-bold text-[#111111] uppercase tracking-wider">Total Catalogue</span>
            <div class="text-xl sm:text-2xl font-bold text-[#111111] mt-1">{{ $stats['total_items'] }} items</div>
            <span class="text-[11px] text-[#666666] font-mono mt-0.5">{{ $stats['total_quantity'] }} units in stock</span>
        </a>
        <a href="{{ route('spare-parts.index', ['stock_status' => 'in_stock']) }}" class="card-surface p-4 flex flex-col justify-between shadow-2xs transition {{ $stockStatus === 'in_stock' ? 'border-[#079455] ring-1 ring-[#079455]' : '' }}">
            <span class="text-xs font-bold text-[#079455] uppercase tracking-wider">In Stock Available</span>
            <div class="text-xl sm:text-2xl font-bold text-[#111111] mt-1">{{ $stats['in_stock_count'] }} parts</div>
            <span class="text-[11px] text-[#079455] font-mono mt-0.5">Ready for repair jobs</span>
        </a>
        <a href="{{ route('spare-parts.index', ['stock_status' => 'low_stock']) }}" class="card-surface p-4 flex flex-col justify-between shadow-2xs transition {{ $stockStatus === 'low_stock' ? 'border-[#D97706] ring-1 ring-[#D97706]' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#D97706] uppercase tracking-wider">Low Stock Alert</span>
                @if($stats['low_stock_count'] > 0)
                    <span class="w-2 h-2 rounded-full bg-[#D97706] animate-pulse"></span>
                @endif
            </div>
            <div class="text-xl sm:text-2xl font-bold text-[#D97706] mt-1">{{ $stats['low_stock_count'] }} parts</div>
            <span class="text-[11px] text-[#666666] font-mono mt-0.5">&le; Min stock threshold</span>
        </a>
        <div class="card-surface p-4 flex flex-col justify-between shadow-2xs">
            <span class="text-xs font-bold text-[#111111] uppercase tracking-wider">Inventory Asset Cost</span>
            <div class="text-xl sm:text-2xl font-bold text-[#111111] mt-1">₹{{ number_format($stats['inventory_value'], 0) }}</div>
            <span class="text-[11px] text-[#666666] font-mono mt-0.5">Total wholesale valuation</span>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card-surface p-4 shadow-2xs">
        <form method="GET" action="{{ route('spare-parts.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by part name, SKU, barcode, compatible model, or brand..."
                    class="w-full pl-9 pr-4 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black min-h-[40px]">
                <svg class="w-4 h-4 text-[#929292] absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                <select name="category" class="px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-1 focus:ring-black min-h-[40px]">
                    <option value="all">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>

                <select name="stock_status" class="px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-1 focus:ring-black min-h-[40px]">
                    <option value="all" {{ request('stock_status', 'all') === 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Low Stock Only</option>
                    <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-[#111111] text-white rounded-lg text-xs sm:text-sm font-semibold hover:bg-black transition min-h-[40px] whitespace-nowrap">
                    Filter
                </button>

                @if(request()->hasAny(['search', 'category', 'stock_status']))
                    <a href="{{ route('spare-parts.index') }}" class="btn-secondary px-3 py-2 text-xs min-h-[40px] flex items-center justify-center">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Spare Parts Table -->
    <div class="card-surface shadow-2xs overflow-hidden">
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F7F7F6] text-[#666666] uppercase tracking-wider font-bold border-b border-[#E6E6E6]">
                    <tr>
                        <th class="py-3.5 px-4">Part Name &amp; Category</th>
                        <th class="py-3.5 px-4">Compatible Models</th>
                        <th class="py-3.5 px-4">SKU / Barcode</th>
                        <th class="py-3.5 px-4 text-center">Available Stock</th>
                        <th class="py-3.5 px-4 text-right">Cost (₹)</th>
                        <th class="py-3.5 px-4 text-right">Repair Price (₹)</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E6E6E6] text-[#344054]">
                    @forelse($parts as $part)
                        <tr class="hover:bg-[#F7F7F6]/60 transition">
                            <td class="py-3.5 px-4">
                                <a href="{{ route('spare-parts.show', $part) }}" class="font-bold text-[#111111] hover:underline text-sm block">
                                    {{ $part->name }}
                                </a>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="px-2 py-0.2 rounded text-[10px] font-mono font-bold bg-[#F2F4F7] text-[#344054]">
                                        {{ $part->category }}
                                    </span>
                                    @if($part->brand)
                                        <span class="text-[11px] text-[#666666]">&bull; {{ $part->brand }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-[#666666]">
                                <span class="line-clamp-2">{{ $part->compatible_models ?: 'Universal / Multiple' }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-mono">
                                @if($part->sku)
                                    <div class="text-[#111111] font-semibold">{{ $part->sku }}</div>
                                @endif
                                @if($part->barcode)
                                    <div class="text-[11px] text-[#929292]">BC: {{ $part->barcode }}</div>
                                @endif
                                @if(!$part->sku && !$part->barcode)
                                    <span class="text-[#929292]">&mdash;</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($part->stock_quantity <= 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold font-mono bg-[#FEF2F2] text-[#B91C1C] border border-[#FECACA]">
                                        0 Out of Stock
                                    </span>
                                @elseif($part->isLowStock())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold font-mono bg-[#FEF0C7] text-[#B54708] border border-[#FEDF89]" title="Min alert level: {{ $part->min_stock_level }}">
                                        {{ $part->stock_quantity }} Low Stock
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold font-mono bg-[#ECFDF3] text-[#027A48] border border-[#A6F4C5]">
                                        {{ $part->stock_quantity }} In Stock
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono text-[#666666]">
                                ₹{{ number_format($part->purchase_price, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-[#111111]">
                                ₹{{ number_format($part->selling_price, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('spare-parts.show', $part) }}" class="p-1.5 hover:bg-[#E6E6E6] rounded-md transition text-[#111111]" title="View & Adjust Stock">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <a href="{{ route('spare-parts.edit', $part) }}" class="p-1.5 hover:bg-[#E6E6E6] rounded-md transition text-[#111111]" title="Edit Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-xs text-[#929292]">
                                <div class="space-y-2">
                                    <svg class="w-8 h-8 mx-auto text-[#D5D5D5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    <p class="font-bold text-[#111111]">No spare parts found matching the criteria</p>
                                    <p class="text-[11px] text-[#666666]">Inward replacement parts or components with the "+ Add Spare Part" button above.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden divide-y divide-[#E6E6E6]">
            @forelse($parts as $part)
                <div class="p-4 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <a href="{{ route('spare-parts.show', $part) }}" class="font-bold text-sm text-[#111111] hover:underline">
                                {{ $part->name }}
                            </a>
                            <div class="text-[11px] text-[#666666] mt-0.5">{{ $part->category }} &bull; {{ $part->brand ?: 'Generic' }}</div>
                        </div>
                        @if($part->stock_quantity <= 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold font-mono bg-[#FEF2F2] text-[#B91C1C]">
                                Out of Stock
                            </span>
                        @elseif($part->isLowStock())
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold font-mono bg-[#FEF0C7] text-[#B54708]">
                                {{ $part->stock_quantity }} Left
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold font-mono bg-[#ECFDF3] text-[#027A48]">
                                {{ $part->stock_quantity }} Available
                            </span>
                        @endif
                    </div>

                    <div class="text-xs text-[#666666] line-clamp-1">
                        <strong>Fits:</strong> {{ $part->compatible_models ?: 'Universal' }}
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1 border-t border-[#EDEDEC]">
                        <div>
                            <span class="text-[10px] text-[#929292] uppercase font-mono">Repair Price:</span>
                            <span class="font-bold text-[#111111] font-mono ml-1">₹{{ number_format($part->selling_price, 2) }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('spare-parts.show', $part) }}" class="text-xs font-bold text-[#111111] underline">Details</a>
                            <a href="{{ route('spare-parts.edit', $part) }}" class="text-xs text-[#666666]">Edit</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-xs text-[#929292]">
                    No spare parts found. Click "+ Add Spare Part" to inward inventory.
                </div>
            @endforelse
        </div>

        @if($parts->hasPages())
            <div class="p-4 border-t border-[#E6E6E6]">
                {{ $parts->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

