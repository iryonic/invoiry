{{-- Hallmark · macrostructure: System Audit Logs Registry · theme: Tactile Sage & Deep Rail · genre: modern-minimal
 * pre-emit critique: P5 H5 E5 S5 R5 V5
 * slop test: pass
--}}
@extends('layouts.app')

@section('title', 'Audit Logs · Super Admin')
@section('page-title', 'System Audit')

@section('content')
<div class="space-y-4 w-full">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-[#FFFBEB] text-[#D97706] rounded-md text-xs font-bold mb-1 border border-[#FEF3C7]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#D97706]"></span>
                <span>Security & Governance Trace</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-[#111111] tracking-tight" style="overflow-wrap: anywhere; min-width: 0;">Platform System Audit Logs</h1>
            <p class="text-xs sm:text-sm text-[#666666]">Immutable audit trail of administrator actions, tenant switches, logins, and configurations</p>
        </div>

        <div class="text-xs font-mono text-[#929292] bg-[#F7F7F6] px-3 py-1.5 rounded-lg border border-[#E6E6E6]">
            Total Events: <strong class="text-[#111111]">{{ $auditLogs->total() }}</strong>
        </div>
    </div>

    <!-- Search & Filter Toolbar -->
    <div class="card-surface p-3.5 shadow-2xs">
        <form method="GET" action="{{ route('superadmin.audit-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
            <div class="sm:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search event type, description, or IP address..."
                    class="w-full pl-9 pr-4 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] min-h-[42px]">
                <svg class="w-4 h-4 text-[#929292] absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <div>
                <select name="event" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-semibold text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] min-h-[42px]">
                    <option value="all">All Event Categories</option>
                    <option value="user" {{ request('event') === 'user' ? 'selected' : '' }}>User & Login Events</option>
                    <option value="tenant" {{ request('event') === 'tenant' ? 'selected' : '' }}>Tenant Store Events</option>
                    <option value="settings" {{ request('event') === 'settings' ? 'selected' : '' }}>Settings Events</option>
                    <option value="staff" {{ request('event') === 'staff' ? 'selected' : '' }}>Staff RBAC Events</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2 bg-[#111111] hover:bg-black text-white rounded-xl text-xs sm:text-sm font-bold min-h-[42px] transition">
                    Filter Logs
                </button>
                @if(request()->hasAny(['search', 'event']))
                    <a href="{{ route('superadmin.audit-logs.index') }}" class="btn-secondary min-h-[42px] text-xs">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Logs Table Container -->
    <div class="card-surface shadow-2xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-[#E6E6E6] flex items-center justify-between">
            <h2 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Audit Log Stream</h2>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F7F7F6] text-[#666666] uppercase tracking-wider font-bold border-b border-[#E6E6E6]">
                    <tr>
                        <th class="py-3 px-4">Timestamp</th>
                        <th class="py-3 px-4">Event Type</th>
                        <th class="py-3 px-4">Description</th>
                        <th class="py-3 px-4">Actor</th>
                        <th class="py-3 px-4">Target Store</th>
                        <th class="py-3 px-4 font-mono text-right">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E6E6E6] text-[#344054]">
                    @forelse($auditLogs as $log)
                        <tr class="hover:bg-[#F7F7F6]/60 transition">
                            <td class="py-3.5 px-4 font-mono text-[11px] text-[#929292] whitespace-nowrap">
                                <div>{{ $log->created_at->format('d M Y, H:i') }}</div>
                                <div class="text-[10px] text-[#666666]">{{ $log->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded font-mono font-bold text-[10px] bg-white border border-[#E6E6E6] text-[#111111]">
                                    {{ $log->event }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-medium text-[#111111]">
                                {{ $log->description }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-[#111111]">{{ $log->user?->name ?? 'System' }}</div>
                                @if($log->user)<div class="text-[10px] text-[#929292] font-mono capitalize">{{ str_replace('_', ' ', $log->user->role) }}</div>@endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($log->tenant)
                                    <span class="font-semibold text-[#111111]">{{ $log->tenant->name }}</span>
                                @else
                                    <span class="text-[#929292] italic">Platform</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono text-right text-[11px] text-[#929292]">
                                {{ $log->ip_address ?: '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-xs text-[#929292]">
                                No audit events found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Stacked View -->
        <div class="md:hidden divide-y divide-[#E6E6E6]">
            @forelse($auditLogs as $log)
                <div class="p-4 space-y-2">
                    <div class="flex items-center justify-between text-[11px]">
                        <span class="px-2 py-0.5 rounded font-mono font-bold text-[10px] bg-white border border-[#E6E6E6] text-[#111111]">
                            {{ $log->event }}
                        </span>
                        <span class="text-[#929292] font-mono text-[10px]">{{ $log->created_at->diffForHumans() }}</span>
                    </div>

                    <p class="text-xs text-[#111111] font-medium leading-relaxed">{{ $log->description }}</p>

                    <div class="flex items-center justify-between text-[11px] text-[#666666] pt-1">
                        <span>By: <strong class="text-[#111111]">{{ $log->user?->name ?? 'System' }}</strong></span>
                        <span class="font-mono text-[#929292]">{{ $log->ip_address ?: '127.0.0.1' }}</span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-xs text-[#929292]">
                    No audit events found.
                </div>
            @endforelse
        </div>

        @if($auditLogs->hasPages())
            <div class="p-3 border-t border-[#E6E6E6]">
                {{ $auditLogs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

