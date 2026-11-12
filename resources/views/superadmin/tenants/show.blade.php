{{-- Hallmark · macrostructure: Tenant Store Detail Deep-Dive · theme: Tactile Sage & Deep Rail · genre: modern-minimal
 * pre-emit critique: P5 H5 E5 S5 R5 V5
 * slop test: pass
--}}
@extends('layouts.app')

@section('title', $tenant->name . ' · Business Inspection')
@section('page-title', 'Store Deep-Dive')

@section('content')
<div class="space-y-4 w-full">

    <!-- Top Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <a href="{{ route('superadmin.tenants.index') }}" class="btn-secondary min-h-[38px] text-xs inline-flex items-center gap-1.5 self-start">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>All Businesses</span>
        </a>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('superadmin.tenants.permissions', $tenant) }}" class="btn-secondary min-h-[40px] text-xs">
                <svg class="w-4 h-4 text-[#079455]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                <span>Permissions & Forms</span>
            </a>

            <a href="{{ route('superadmin.tenants.edit', $tenant) }}" class="btn-secondary min-h-[40px] text-xs">
                <svg class="w-4 h-4 text-[#111111]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                <span>Edit Details</span>
            </a>

            <form action="{{ route('superadmin.impersonate', $tenant) }}" method="POST">
                @csrf
                <button type="submit" class="btn-lime min-h-[40px] text-xs">
                    <span class="font-bold">Switch Into Store</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Store Profile Card -->
    <div class="card-surface p-5 sm:p-6 shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-5">
        <div class="flex items-start sm:items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-[#050505] text-[#B7FF00] flex items-center justify-center font-black text-2xl uppercase shrink-0">
                {{ substr($tenant->name, 0, 1) }}
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl sm:text-2xl font-black text-[#111111] tracking-tight" style="overflow-wrap: anywhere; min-width: 0;">{{ $tenant->name }}</h1>
                    <span class="uppercase px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#F2F4F7] text-[#344054] font-mono">
                        {{ $tenant->plan }} Plan
                    </span>
                    @if($tenant->is_active)
                        <span class="badge-paid">Active</span>
                    @else
                        <span class="badge-unpaid">Disabled</span>
                    @endif
                </div>

                <div class="flex items-center gap-3 text-xs text-[#666666] mt-1 flex-wrap font-mono">
                    <span class="font-semibold text-[#111111]">{{ $tenant->phone }}</span>
                    <span>&bull; {{ $tenant->email }}</span>
                    <span>&bull; Slug: {{ $tenant->slug }}</span>
                    @if($tenant->gstin)<span>&bull; GSTIN: {{ $tenant->gstin }}</span>@endif
                </div>
                @if($tenant->address)<p class="text-xs text-[#929292] mt-1">{{ $tenant->address }}, {{ $tenant->city }} ({{ $tenant->state }})</p>@endif
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 shrink-0">
            <div class="p-3.5 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] text-right">
                <span class="text-[10px] font-bold text-[#929292] uppercase tracking-wider block">Store GMV</span>
                <div class="text-lg font-black text-[#111111] mt-0.5 font-mono">₹{{ number_format($totalSales, 0) }}</div>
                <span class="text-[10px] text-[#666666] font-mono">Collected: ₹{{ number_format($totalCollected, 0) }}</span>
            </div>
            <div class="p-3.5 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] text-right">
                <span class="text-[10px] font-bold text-[#929292] uppercase tracking-wider block">Devices In Stock</span>
                <div class="text-lg font-black text-[#111111] mt-0.5 font-mono">{{ $inStockDevices }}</div>
                <span class="text-[10px] text-[#666666] font-mono">{{ $tenant->invoices_count }} total bills</span>
            </div>
        </div>
    </div>

    <!-- Subscription & Validity Controls -->
    <div class="card-surface p-4 sm:p-5 shadow-2xs space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-[#E6E6E6] pb-3">
            <div>
                <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Subscription Validity & Lifecycle</h3>
                <p class="text-xs text-[#666666] mt-0.5">
                    Plan expires: 
                    <strong class="font-mono text-[#111111]">{{ $tenant->expires_at ? $tenant->expires_at->format('d M Y') : 'Lifetime Access' }}</strong>
                    @if($tenant->expires_at && $tenant->expires_at->isPast())
                        <span class="text-[#DC2626] font-bold">(Expired)</span>
                    @elseif($tenant->expires_at)
                        <span class="text-[#079455]">({{ $tenant->expires_at->diffForHumans() }})</span>
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-2">
                <form action="{{ route('superadmin.tenants.extend', $tenant) }}" method="POST">
                    @csrf
                    <input type="hidden" name="days" value="30">
                    <button type="submit" class="btn-secondary text-xs px-3 py-1.5 min-h-[36px]">
                        +30 Days
                    </button>
                </form>
                <form action="{{ route('superadmin.tenants.extend', $tenant) }}" method="POST">
                    @csrf
                    <input type="hidden" name="days" value="90">
                    <button type="submit" class="btn-secondary text-xs px-3 py-1.5 min-h-[36px]">
                        +90 Days
                    </button>
                </form>
                <form action="{{ route('superadmin.tenants.extend', $tenant) }}" method="POST">
                    @csrf
                    <input type="hidden" name="days" value="365">
                    <button type="submit" class="btn-lime text-xs px-3 py-1.5 min-h-[36px]">
                        +1 Year Extension
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs pt-1">
            <div>
                <span class="text-[10px] text-[#929292] uppercase block font-semibold">Invoice Prefix</span>
                <span class="font-mono font-bold text-[#111111]">{{ $tenant->invoice_prefix ?: 'INV-' }}</span>
            </div>
            <div>
                <span class="text-[10px] text-[#929292] uppercase block font-semibold">Counter Invoice #</span>
                <span class="font-mono font-bold text-[#111111]">{{ $tenant->invoice_counter }}</span>
            </div>
            <div>
                <span class="text-[10px] text-[#929292] uppercase block font-semibold">Counter UPI VPA</span>
                <span class="font-mono font-bold text-[#111111]">{{ $tenant->upi_id ?: 'Not Configured' }}</span>
            </div>
            <div>
                <span class="text-[10px] text-[#929292] uppercase block font-semibold">Bank Settlement</span>
                <span class="font-mono font-bold text-[#111111]">{{ $tenant->bank_name ?: 'None' }}</span>
            </div>
        </div>
    </div>

    <!-- Staff Accounts in this Store -->
    <div class="card-surface shadow-2xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-[#E6E6E6] flex items-center justify-between">
            <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Registered Staff Accounts ({{ $tenantUsers->count() }})</h3>
        </div>

        <div class="divide-y divide-[#E6E6E6]">
            @foreach($tenantUsers as $u)
                <div class="p-3.5 sm:px-5 flex items-center justify-between gap-3 text-xs">
                    <div>
                        <div class="font-bold text-[#111111] flex items-center gap-2">
                            <span>{{ $u->name }}</span>
                            <span class="uppercase text-[10px] px-2 py-0.2 rounded-full font-bold {{ $u->role === 'tenant_admin' ? 'bg-[#111111] text-[#B7FF00]' : 'bg-[#F2F4F7] text-[#344054]' }}">
                                {{ str_replace('_', ' ', $u->role) }}
                            </span>
                        </div>
                        <div class="text-[11px] text-[#929292] font-mono">{{ $u->email }} &bull; {{ $u->phone ?: 'No phone' }}</div>
                    </div>

                    <div>
                        @if($u->is_active)
                            <span class="badge-paid">Active</span>
                        @else
                            <span class="badge-unpaid">Disabled</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Danger Zone: Delete Tenant -->
    <div class="card-surface p-4 sm:p-5 border border-[#FECDCA] shadow-2xs space-y-3 bg-[#FEF2F2]/30">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h4 class="text-xs font-bold text-[#B42318] uppercase tracking-wider">Danger Zone: Delete Business Store</h4>
                <p class="text-xs text-[#666666] mt-0.5">Permanently remove this store, staff users, items, and billing history from the platform.</p>
            </div>

            <form action="{{ route('superadmin.tenants.destroy', $tenant) }}" method="POST" onsubmit="return confirm('WARNING: Are you absolutely sure you want to permanently delete store {{ $tenant->name }}? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-[#DC2626] hover:bg-[#B91C1C] text-white rounded-xl text-xs font-bold min-h-[40px] transition shadow-xs">
                    Delete Business Store
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

