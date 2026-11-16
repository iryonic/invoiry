{{-- Hallmark · macrostructure: Multi-Tenant Platform Directory · theme: Tactile Sage & Deep Rail · genre: modern-minimal
 * pre-emit critique: P5 H5 E5 S5 R5 V5
 * slop test: pass
--}}
@extends('layouts.app')

@section('title', 'Registered Businesses · Super Admin')
@section('page-title', 'Business Directory')

@section('content')
<div class="space-y-4 w-full">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-[#111111] tracking-tight" style="overflow-wrap: anywhere; min-width: 0;">Registered Businesses / Stores</h1>
            <p class="text-xs sm:text-sm text-[#666666]">Lifecycle management, subscriptions, store inspection, and tenant isolation</p>
        </div>

        <a href="{{ route('superadmin.tenants.create') }}" class="btn-lime min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            <span class="whitespace-nowrap font-bold">Add New Business</span>
        </a>
    </div>

    <!-- Search & Filter Toolbar -->
    <div class="card-surface p-3.5 shadow-2xs">
        <form method="GET" action="{{ route('superadmin.tenants.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-2">
            <div class="sm:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by store name, phone, email, GSTIN, or city..."
                    class="w-full pl-9 pr-4 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] min-h-[42px]">
                <svg class="w-4 h-4 text-[#929292] absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <div>
                <select name="business_type" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-semibold text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] min-h-[42px]">
                    <option value="all">All Business Types</option>
                    <option value="selling" {{ request('business_type') === 'selling' ? 'selected' : '' }}>Selling &amp; Reselling</option>
                    <option value="repair" {{ request('business_type') === 'repair' ? 'selected' : '' }}>Mobile Repair</option>
                    <option value="both" {{ request('business_type') === 'both' ? 'selected' : '' }}>Both Selling &amp; Repair</option>
                </select>
            </div>

            <div>
                <select name="plan" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-semibold text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] min-h-[42px]">
                    <option value="all">All Plans</option>
                    <option value="trial" {{ request('plan') === 'trial' ? 'selected' : '' }}>Trial (14-30 Days)</option>
                    <option value="starter" {{ request('plan') === 'starter' ? 'selected' : '' }}>Starter Plan</option>
                    <option value="pro" {{ request('plan') === 'pro' ? 'selected' : '' }}>Pro Business</option>
                    <option value="enterprise" {{ request('plan') === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                </select>
            </div>

            <div>
                <select name="status" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-semibold text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] min-h-[42px]">
                    <option value="all">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="disabled" {{ request('status') === 'disabled' ? 'selected' : '' }}>Disabled Only</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2 bg-[#111111] hover:bg-black text-white rounded-xl text-xs sm:text-sm font-bold min-h-[42px] transition">
                    Filter Stores
                </button>
                @if(request()->hasAny(['search', 'plan', 'status', 'business_type']))
                    <a href="{{ route('superadmin.tenants.index') }}" class="btn-secondary min-h-[42px] text-xs">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tenants Container -->
    <div class="card-surface shadow-2xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-[#E6E6E6] flex items-center justify-between">
            <h2 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Business Stores Directory ({{ $tenants->total() }})</h2>
        </div>

        <!-- Desktop Table -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F7F7F6] text-[#666666] uppercase tracking-wider font-bold border-b border-[#E6E6E6]">
                    <tr>
                        <th class="py-3 px-3 w-10 text-center">
                            <input type="checkbox" data-bulk-select-all class="rounded border-[#D0D5DD] text-black focus:ring-black">
                        </th>
                        <th class="py-3 px-4">Business / Shop</th>
                        <th class="py-3 px-4">Contact & Location</th>
                        <th class="py-3 px-4">Plan & Expiry</th>
                        <th class="py-3 px-4">Metrics</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E6E6E6] text-[#344054]">
                    @forelse($tenants as $t)
                        <tr class="hover:bg-[#F7F7F6]/60 transition">
                            <td class="py-3.5 px-3 text-center">
                                <input type="checkbox" data-bulk-item value="{{ $t->id }}" class="rounded border-[#D0D5DD] text-black focus:ring-black">
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <a href="{{ route('superadmin.tenants.show', $t) }}" class="font-bold text-[#111111] hover:underline text-sm block">
                                        {{ $t->name }}
                                    </a>
                                    @php
                                        $typeBadge = match($t->business_type ?? 'selling') {
                                            'repair' => 'bg-[#FEF0C7] text-[#B54708] border-[#FEDF89]',
                                            'both' => 'bg-[#ECFDF3] text-[#027A48] border-[#A6F4C5]',
                                            default => 'bg-[#EFF8FF] text-[#175CD3] border-[#B2DDFF]',
                                        };
                                        $typeShort = match($t->business_type ?? 'selling') {
                                            'repair' => 'Repair',
                                            'both' => 'Sell + Repair',
                                            default => 'Selling',
                                        };
                                    @endphp
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-mono font-bold border {{ $typeBadge }}">
                                        {{ $typeShort }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-[#929292] font-mono">{{ $t->slug }} &bull; Prefix: {{ $t->invoice_prefix }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-[#111111] font-mono">{{ $t->phone }}</div>
                                <div class="text-[11px] text-[#929292]">{{ $t->city ?: 'No city specified' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-1.5">
                                    <span class="uppercase px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#F2F4F7] text-[#344054] font-mono">
                                        {{ $t->plan }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-[#929292] font-mono mt-0.5">
                                    Expires: {{ $t->expires_at ? $t->expires_at->format('d M Y') : 'Lifetime' }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-[#111111]">
                                <div>{{ $t->items_count }} in-stock</div>
                                <div class="text-[11px] text-[#666666] font-normal">{{ $t->invoices_count }} bills &bull; {{ $t->users_count }} staff</div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($t->is_active)
                                    <span class="badge-paid">Active</span>
                                @else
                                    <span class="badge-unpaid">Disabled</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- View Details -->
                                    <a href="{{ route('superadmin.tenants.show', $t) }}" class="p-1.5 text-[#666666] hover:text-[#111111] rounded hover:bg-[#F2F4F7]" title="Inspect Store">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>

                                    <!-- Permissions & Form Config -->
                                    <a href="{{ route('superadmin.tenants.permissions', $t) }}" class="p-1.5 text-[#079455] hover:text-[#027A48] rounded hover:bg-[#ECFDF3]" title="Manage Permissions & Form Fields">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    </a>

                                    <!-- Edit Store -->
                                    <a href="{{ route('superadmin.tenants.edit', $t) }}" class="p-1.5 text-[#666666] hover:text-[#111111] rounded hover:bg-[#F2F4F7]" title="Edit Settings">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>

                                    <!-- Quick Extend Subscription -->
                                    <form action="{{ route('superadmin.tenants.extend', $t) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="days" value="30">
                                        <button type="submit" class="p-1.5 text-[#079455] hover:bg-[#ECFDF3] rounded" title="Extend 30 Days">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        </button>
                                    </form>

                                    <!-- Status Toggle -->
                                    <form action="{{ route('superadmin.tenants.status', $t) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-secondary text-[11px] px-2 py-1 min-h-[30px]" title="Toggle Active">
                                            {{ $t->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>

                                    <!-- Impersonate -->
                                    <form action="{{ route('superadmin.impersonate', $t) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-dark text-[11px] px-2.5 py-1 min-h-[30px]" title="Switch into tenant view">
                                            <span>Switch</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-xs text-[#929292]">
                                No businesses matching the specified query filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Select All Bar -->
        <div class="flex items-center justify-between p-3 bg-white border-b border-[#E6E6E6] text-xs lg:hidden">
            <label class="flex items-center gap-2.5 font-bold text-[#111111] cursor-pointer">
                <input type="checkbox" data-bulk-select-all class="rounded border-[#D0D5DD] text-black focus:ring-black w-4 h-4">
                <span>Select All on Page</span>
            </label>
            <span class="text-[11px] text-[#666666] font-mono">{{ $tenants->total() }} stores</span>
        </div>

        <!-- Mobile Stacked Cards -->
        <div class="lg:hidden divide-y divide-[#E6E6E6]">
            @forelse($tenants as $t)
                <div class="p-4 space-y-3 transition" data-card-item>
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-start gap-2.5">
                            <label class="cursor-pointer p-1 -m-1 inline-flex items-center shrink-0">
                                <input type="checkbox" data-bulk-item value="{{ $t->id }}" class="rounded border-[#D0D5DD] text-black focus:ring-black w-4 h-4 mt-0.5">
                            </label>
                            <div>
                                <a href="{{ route('superadmin.tenants.show', $t) }}" class="font-bold text-[#111111] text-sm hover:underline block">
                                    {{ $t->name }}
                                </a>
                                <div class="text-[11px] text-[#929292] font-mono">{{ $t->phone }} &bull; {{ $t->city ?: 'No city' }}</div>
                            </div>
                        </div>
                        <div>
                            @if($t->is_active)
                                <span class="badge-paid">Active</span>
                            @else
                                <span class="badge-unpaid">Disabled</span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs text-[#666666]">
                        <div>
                            <span class="text-[10px] text-[#929292] block uppercase">Plan & Expiry</span>
                            <span class="font-mono font-bold text-[#111111] capitalize">{{ $t->plan }} &bull; {{ $t->expires_at ? $t->expires_at->format('d/m/y') : 'Life' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-[#929292] block uppercase">Stock & Bills</span>
                            <span class="font-mono font-bold text-[#111111]">{{ $t->items_count }} stock &bull; {{ $t->invoices_count }} bills</span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-[#E6E6E6] flex items-center justify-between gap-2 flex-wrap">
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('superadmin.tenants.show', $t) }}" class="btn-secondary text-xs px-2.5 py-1 min-h-[34px]">
                                Details
                            </a>
                            <a href="{{ route('superadmin.tenants.edit', $t) }}" class="btn-secondary text-xs px-2.5 py-1 min-h-[34px]">
                                Edit
                            </a>
                            <a href="{{ route('superadmin.tenants.permissions', $t) }}" class="btn-secondary text-xs px-2.5 py-1 min-h-[34px] text-[#079455] font-bold">
                                RBAC & Forms
                            </a>
                        </div>

                        <div class="flex items-center gap-1.5">
                            <form action="{{ route('superadmin.tenants.status', $t) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-secondary text-xs px-2.5 py-1 min-h-[34px]">
                                    {{ $t->is_active ? 'Disable' : 'Enable' }}
                                </button>
                            </form>
                            <form action="{{ route('superadmin.impersonate', $t) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-dark text-xs px-3 py-1.5 min-h-[34px]">
                                    <span>Switch Store</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-xs text-[#929292]">
                    No businesses matching the specified query filters.
                </div>
            @endforelse
        </div>

        @if($tenants->hasPages())
            <div class="p-3 border-t border-[#E6E6E6]">
                {{ $tenants->links() }}
            </div>
        @endif
    </div>

    <!-- Reusable Bulk Action Bar for Businesses -->
    <x-bulk-action-bar :action="route('superadmin.tenants.bulk')" id="bulk-tenants-form">
        <button type="button" data-bulk-action="activate" class="px-3.5 py-2 rounded-xl bg-[#079455] hover:bg-[#067644] text-white font-bold text-xs transition shadow-xs active:scale-95 shrink-0">
            Activate
        </button>
        <button type="button" data-bulk-action="suspend" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-black font-bold text-xs transition shadow-xs active:scale-95 shrink-0">
            Suspend
        </button>
        <button type="button" data-bulk-action="delete" data-confirm="Are you sure you want to permanently delete :count selected business store(s) and all their data?" data-confirm-title="Delete Selected Stores" data-confirm-variant="danger" class="px-3.5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs transition shadow-xs active:scale-95 shrink-0">
            Delete
        </button>
    </x-bulk-action-bar>
</div>
@endsection

