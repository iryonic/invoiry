{{-- Hallmark · macrostructure: Multi-Tenant Platform Console · theme: Tactile Sage & Deep Rail · genre: modern-minimal
 * pre-emit critique: P5 H5 E5 S5 R5 V5
 * slop test: pass
--}}
@extends('layouts.app')

@section('title', 'Super Admin Console · Platform Overview')
@section('page-title', 'Platform Overview')
@section('page-subtitle', 'Multi-Tenant Master Control')

@section('content')
<div class="space-y-6 w-full" style="overflow-x: clip;">

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-[#F2F4F7] text-[#344054] mb-1.5 border border-[#EAECF0]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#079455]"></span>
                <span>Platform Master Console</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#111111] tracking-tight">Multi-Tenant Platform Overview</h1>
            <p class="text-xs sm:text-sm text-[#666666] mt-0.5">
                Centralized oversight of all subscribed business stores, platform GMV, and system access
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('superadmin.users.index') }}" class="btn-secondary text-xs min-h-[40px] px-3.5 py-2">
                <svg class="w-4 h-4 text-[#111111]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span>Global Users</span>
            </a>
            <a href="{{ route('superadmin.settings.index') }}" class="btn-secondary text-xs min-h-[40px] px-3.5 py-2">
                <svg class="w-4 h-4 text-[#111111]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>Platform Settings</span>
            </a>
            <a href="{{ route('superadmin.tenants.create') }}" class="btn-lime text-xs sm:text-sm font-bold min-h-[44px] px-4 py-2.5">
                <svg class="w-4 h-4 text-[#101010]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span class="whitespace-nowrap font-bold">Add Business</span>
            </a>
        </div>
    </div>

    <!-- 4 Consistent Platform KPI Cards in One Row (Desktop) / 2 Cols (Tablet/Mobile) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- 1. Registered Businesses -->
        <div class="card-surface p-4 sm:p-5 flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between text-[#666666]">
                <span class="text-xs font-semibold uppercase tracking-wider">Subscribed Stores</span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-[26px] font-bold text-[#111111] tracking-tight">{{ $totalTenants }}</div>
                <p class="text-[11px] text-[#929292] mt-0.5">Isolated retail tenants</p>
            </div>
        </div>

        <!-- 2. Platform GMV -->
        <div class="card-surface p-4 sm:p-5 flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between text-[#666666]">
                <span class="text-xs font-semibold uppercase tracking-wider">Platform GMV</span>
               
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-[26px] font-bold text-[#111111] tracking-tight font-mono">₹{{ number_format($totalGmv, 0) }}</div>
                <p class="text-[11px] text-[#929292] mt-0.5">Recorded across all stores</p>
            </div>
        </div>

        <!-- 3. Total Invoices -->
        <div class="card-surface p-4 sm:p-5 flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between text-[#666666]">
                <span class="text-xs font-semibold uppercase tracking-wider">Total Invoices</span>
               
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-[26px] font-bold text-[#111111] tracking-tight font-mono">{{ number_format($totalInvoices) }}</div>
                <p class="text-[11px] text-[#929292] mt-0.5">Customer bills generated</p>
            </div>
        </div>

        <!-- 4. Tracked Hardware Items -->
        <div class="card-surface p-4 sm:p-5 flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between text-[#666666]">
                <span class="text-xs font-semibold uppercase tracking-wider">Tracked Devices</span>
                
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-[26px] font-bold text-[#111111] tracking-tight font-mono">{{ number_format($totalItemsTracked) }}</div>
                <p class="text-[11px] text-[#929292] mt-0.5">Hardware assets in stock</p>
            </div>
        </div>
    </div>

   

    <!-- Main Content Two-Column Grid (67% / 33%) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left 8 Cols: Registered Businesses Snapshot Table -->
        <div class="lg:col-span-8 space-y-3">
            <div class="flex items-center justify-between px-1">
                <div>
                    <h3 class="text-sm font-bold text-[#111111] uppercase tracking-wider">Registered Businesses Snapshot</h3>
                    <p class="text-xs text-[#666666]">Latest Added stores and subscription validity</p>
                </div>
                <a href="{{ route('superadmin.tenants.index') }}" class="text-xs font-bold text-[#111111] hover:underline flex items-center gap-1">
                    <span>View all ({{ $totalTenants }})</span>
                    <span></span>
                </a>
            </div>

            <!-- Desktop Table View -->
            <div class="card-surface overflow-hidden shadow-2xs hidden sm:block">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F7F7F6] text-[#666666] uppercase tracking-wider font-semibold border-b border-[#E6E6E6]">
                        <tr>
                            <th class="py-3 px-4">Business Store</th>
                            <th class="py-3 px-4">Plan & Expiry</th>
                            <th class="py-3 px-4">Activity</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E6E6E6] text-[#344054]">
                        @forelse($recentTenants as $t)
                            <tr class="hover:bg-[#F7F7F6]/60 transition">
                                <td class="py-3 px-4">
                                    <a href="{{ route('superadmin.tenants.show', $t) }}" class="font-bold text-[#111111] hover:underline text-sm block">
                                        {{ $t->name }}
                                    </a>
                                    <div class="text-[11px] text-[#929292] font-mono mt-0.5">
                                        {{ $t->phone }} &bull; {{ $t->city ?: 'Retail Counter' }}
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="uppercase px-2 py-0.5 rounded text-[10px] font-bold bg-[#F2F4F7] text-[#344054] font-mono">
                                        {{ $t->plan }}
                                    </span>
                                    <div class="text-[11px] text-[#929292] font-mono mt-1">
                                        {{ $t->expires_at ? $t->expires_at->format('d M Y') : 'Lifetime' }}
                                    </div>
                                </td>
                                <td class="py-3 px-4 font-mono font-semibold text-[#111111]">
                                    <div>{{ $t->items_count }} stock</div>
                                    <div class="text-[11px] text-[#666666] font-normal font-sans">{{ $t->invoices_count }} bills &bull; {{ $t->users_count }} staff</div>
                                </td>
                                <td class="py-3 px-4">
                                    @if($t->is_active)
                                        <span class="badge-paid">Active</span>
                                    @else
                                        <span class="badge-unpaid">Disabled</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('superadmin.tenants.show', $t) }}" class="p-1.5 text-[#666666] hover:text-[#111111] rounded-md hover:bg-[#F2F4F7] transition" title="Inspect Store">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <form action="{{ route('superadmin.impersonate', $t) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn-dark text-[11px] px-2.5 py-1 min-h-[30px]" title="Switch session into store view">
                                                <span>Switch</span>
                                                <span></span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-xs text-[#929292]">
                                    No registered businesses found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Stacked Cards -->
            <div class="sm:hidden space-y-2.5">
                @foreach($recentTenants as $t)
                    <div class="card-surface p-4 shadow-2xs space-y-2.5">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <a href="{{ route('superadmin.tenants.show', $t) }}" class="font-bold text-[#111111] text-sm hover:underline block">
                                    {{ $t->name }}
                                </a>
                                <p class="text-[11px] text-[#929292] font-mono">{{ $t->phone }} &bull; {{ $t->city ?: 'No city' }}</p>
                            </div>
                            @if($t->is_active)
                                <span class="badge-paid">Active</span>
                            @else
                                <span class="badge-unpaid">Disabled</span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between text-xs text-[#666666] pt-1">
                            <span class="uppercase font-mono font-bold text-[#111111]">{{ $t->plan }}</span>
                            <span class="font-mono text-[#111111]">{{ $t->items_count }} stock &bull; {{ $t->invoices_count }} bills</span>
                        </div>

                        <div class="pt-2 border-t border-[#E6E6E6] flex items-center justify-end gap-2">
                            <a href="{{ route('superadmin.tenants.show', $t) }}" class="btn-secondary text-xs px-3 py-1.5 min-h-[34px]">
                                Details
                            </a>
                            <form action="{{ route('superadmin.impersonate', $t) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-dark text-xs px-3 py-1.5 min-h-[34px]">
                                    <span>Switch Store </span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right 4 Cols: Live Platform Audit Feed -->
        <div class="lg:col-span-4 space-y-3">
            <div class="flex items-center justify-between px-1">
                <div>
                    <h3 class="text-sm font-bold text-[#111111] uppercase tracking-wider">Live Audit Stream</h3>
                    <p class="text-xs text-[#666666]">Real-time administrative ledger</p>
                </div>
                <a href="{{ route('superadmin.audit-logs.index') }}" class="text-xs font-bold text-[#111111] hover:underline flex items-center gap-1">
                    <span>All logs</span>
                    <span></span>
                </a>
            </div>

            <div class="card-surface p-3.5 sm:p-4 shadow-2xs space-y-3">
                @forelse($recentAuditLogs as $log)
                    <div class="p-3 rounded-xl bg-[#F7F7F6] border border-[#E6E6E6] space-y-1.5 text-xs">
                        <div class="flex items-center justify-between gap-1 text-[10px]">
                            <span class="font-mono font-bold text-[#111111] px-2 py-0.5 rounded bg-white border border-[#E6E6E6]">
                                {{ $log->event }}
                            </span>
                            <span class="text-[#929292] font-mono">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-[#344054] text-[11px] leading-relaxed font-medium">{{ $log->description }}</p>
                        <div class="flex items-center justify-between text-[10px] text-[#929292] font-mono pt-0.5">
                            <span>Actor: <strong class="text-[#111111]">{{ $log->user?->name ?? 'System' }}</strong></span>
                            <span>{{ $log->ip_address ?: 'Local' }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-[#929292] text-center py-6">No recent audit events recorded.</p>
                @endforelse

                <div class="pt-2 border-t border-[#E6E6E6]">
                    <a href="{{ route('superadmin.audit-logs.index') }}" class="w-full btn-secondary text-xs min-h-[36px] flex items-center justify-center">
                        <span>View Full Platform Audit History </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
