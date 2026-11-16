@php
    $user = auth()->user();
    $tenant = \App\Services\TenantContext::get() ?? $user?->tenant;
    $supportsSelling = $tenant ? $tenant->supportsSelling() : true;
    $supportsRepair = $tenant ? $tenant->supportsRepair() : false;
    $isSuperAdmin = $user?->isSuperAdmin() && !session()->has('active_tenant_id');
@endphp

@if($isSuperAdmin)
    <!-- SUPER ADMIN PLATFORM SECTIONS -->
    <div class="space-y-1">
        <div class="px-2.5 pb-1 text-[10px] font-bold text-[#929292] uppercase tracking-wider">Platform Hub</div>
        
        <a href="{{ route('superadmin.index') }}" 
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('superadmin.index') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('superadmin.tenants.index') }}" 
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('superadmin.tenants.index') || request()->routeIs('superadmin.tenants.show') || request()->routeIs('superadmin.tenants.edit') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            <span>Businesses / Stores</span>
        </a>

        <a href="{{ route('superadmin.tenants.create') }}" 
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('superadmin.tenants.create') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Add Business</span>
        </a>
    </div>

    <!-- ACCESS & GOVERNANCE -->
    <div class="space-y-1 pt-2">
        <div class="px-2.5 pb-1 text-[10px] font-bold text-[#929292] uppercase tracking-wider">Access & Governance</div>

        <a href="{{ route('superadmin.users.index') }}" 
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('superadmin.users.*') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <span>Global Users & RBAC</span>
        </a>

        <a href="{{ route('superadmin.audit-logs.index') }}" 
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('superadmin.audit-logs.*') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span>System Audit Logs</span>
        </a>

        <a href="{{ route('superadmin.settings.index') }}" 
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('superadmin.settings.*') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span>Platform Settings</span>
        </a>

        <a href="{{ route('superadmin.brochure.index') }}" 
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('superadmin.brochure.*') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            <span>Features & Pricing PDF</span>
        </a>
    </div>

    <!-- EMAIL MANAGEMENT (SUPER ADMIN) -->
    <div class="space-y-1 pt-2">
        <div class="px-2.5 pb-1 text-[10px] font-bold text-[#929292] uppercase tracking-wider">Platform Mail</div>

        <a href="{{ route('mail.dashboard') }}" 
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('mail.dashboard') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            <span>Mail Overview</span>
        </a>

        <a href="{{ route('mail.settings.index') }}" 
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('mail.settings.*') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            <span>SMTP Settings</span>
        </a>

        <a href="{{ route('mail.templates.index') }}" 
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('mail.templates.*') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path></svg>
            <span>Email Templates</span>
        </a>

        <a href="{{ route('mail.logs.index') }}" 
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('mail.logs.*') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span>Delivery Logs</span>
        </a>
    </div>
@else
    <!-- OVERVIEW -->
    @if($user?->hasPermission('dashboard.view'))
        <div class="space-y-1">
            <div class="px-2.5 pb-1 text-[10px] font-bold text-[#929292] uppercase tracking-wider">Overview</div>
            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('dashboard') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span>Dashboard</span>
            </a>
        </div>
    @endif

    <!-- BILLING & SALES (SELLING / BOTH) -->
    @if($supportsSelling && ($user?->hasPermission('invoices.create') || $user?->hasPermission('invoices.view')))
        <div class="space-y-1 pt-1">
            <div class="px-2.5 pb-1 text-[10px] font-bold text-[#929292] uppercase tracking-wider">
                {{ $supportsRepair ? 'Device Sales' : 'Billing & Sales' }}
            </div>
            
            @if($user?->hasPermission('invoices.create'))
                <a href="{{ route('invoices.create') }}" 
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('invoices.create') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Create New Bill</span>
                </a>
            @endif

            @if($user?->hasPermission('invoices.view'))
                <a href="{{ route('invoices.index') }}" 
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('invoices.index*') && !request()->routeIs('invoices.create') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>All Invoices</span>
                </a>
            @endif
        </div>
    @endif

    <!-- DEVICE INVENTORY (SELLING / BOTH) -->
    @if($supportsSelling && ($user?->hasPermission('items.view') || $user?->hasPermission('items.create')))
        <div class="space-y-1 pt-1">
            <div class="px-2.5 pb-1 text-[10px] font-bold text-[#929292] uppercase tracking-wider">
                {{ $supportsRepair ? 'Device Inventory' : 'Inventory' }}
            </div>
            
            @if($user?->hasPermission('items.view'))
                <a href="{{ route('items.index') }}" 
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('items.index') || request()->routeIs('items.show') || request()->routeIs('items.edit') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <span>Devices Catalog</span>
                </a>

                <a href="{{ route('stock.index') }}" 
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('stock.*') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    <span>Stock Management</span>
                </a>
            @endif

            @if($user?->hasPermission('items.create'))
                <a href="{{ route('items.create') }}" 
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('items.create') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Inward Purchase</span>
                </a>
            @endif
        </div>
    @endif

    <!-- WORKSHOP & REPAIRS (REPAIR / BOTH) -->
    @if($supportsRepair && ($user?->hasPermission('repairs.view') || $user?->hasPermission('repairs.create')))
        <div class="space-y-1 pt-1">
            <div class="px-2.5 pb-1 text-[10px] font-bold text-[#929292] uppercase tracking-wider flex items-center justify-between">
                <span>Workshop &amp; Repair</span>
                <span class="text-[9px] px-1.5 py-0.5 rounded bg-[#111111] text-[#0369A1] font-mono font-bold border border-[#1e293b]">BENCH</span>
            </div>

            @if($user?->hasPermission('repairs.create'))
                <a href="{{ route('repairs.create') }}" 
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('repairs.create') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>New Repair Request</span>
                </a>
            @endif

            @if($user?->hasPermission('repairs.view'))
                <a href="{{ route('repairs.index') }}" 
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('repairs.index') || request()->routeIs('repairs.show') || request()->routeIs('repairs.edit') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>Repair Jobs</span>
                </a>
            @endif
        </div>
    @endif

    <!-- SPARE PARTS & HARDWARE (REPAIR / BOTH) -->
    @if($supportsRepair && ($user?->hasPermission('spare_parts.view') || $user?->hasPermission('spare_parts.create')))
        <div class="space-y-1 pt-1">
            <div class="px-2.5 pb-1 text-[10px] font-bold text-[#929292] uppercase tracking-wider">
                Spare Parts &amp; Stock
            </div>

            @if($user?->hasPermission('spare_parts.view'))
                <a href="{{ route('spare-parts.index') }}" 
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('spare-parts.index') || request()->routeIs('spare-parts.show') || request()->routeIs('spare-parts.edit') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span>Parts Catalogue</span>
                </a>

                <a href="{{ route('spare-parts.stock') }}" 
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('spare-parts.stock') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    <span>Parts Stock Management</span>
                </a>
            @endif

            @if($user?->hasPermission('spare_parts.create'))
                <a href="{{ route('spare-parts.create') }}" 
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('spare-parts.create') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Add Spare Part</span>
                </a>
            @endif
        </div>
    @endif

    <!-- REPAIR INVOICES (FOR REPAIR-ONLY BUSINESSES) -->
    @if(!$supportsSelling && $supportsRepair && $user?->hasPermission('invoices.view'))
        <div class="space-y-1 pt-1">
            <div class="px-2.5 pb-1 text-[10px] font-bold text-[#929292] uppercase tracking-wider">
                Billing &amp; Invoices
            </div>

            <a href="{{ route('invoices.index') }}" 
               class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('invoices.index*') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Repair Invoices</span>
            </a>
        </div>
    @endif

    <!-- CONTACTS & LEDGER -->
    @if($user?->hasPermission('customers.view') || $user?->hasPermission('suppliers.view'))
        <div class="space-y-1 pt-1">
            <div class="px-2.5 pb-1 text-[10px] font-bold text-[#929292] uppercase tracking-wider">Contacts & Ledger</div>
            
            @if($user?->hasPermission('customers.view'))
                <a href="{{ route('customers.index') }}" 
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('customers.*') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span>Customers</span>
                </a>
            @endif

            @if($user?->hasPermission('suppliers.view'))
                <a href="{{ route('suppliers.index') }}" 
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('suppliers.*') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span>Suppliers</span>
                </a>
            @endif
        </div>
    @endif

    <!-- COMMUNICATIONS & EMAIL -->
    @if($user?->hasPermission('mail.view'))
        <div class="space-y-1 pt-1">
            <div class="px-2.5 pb-1 text-[10px] font-bold text-[#929292] uppercase tracking-wider flex items-center justify-between">
                <span>Communications</span>
                <span class="text-[9px] px-1.5 py-0.5 rounded bg-[#111111] text-[#B7FF00] font-mono font-bold border border-[#222]">MAIL</span>
            </div>
            
            <a href="{{ route('mail.dashboard') }}" 
               class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('mail.dashboard') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                <span>Email Center</span>
            </a>

            @if($user?->hasPermission('mail.compose'))
                <a href="{{ route('mail.compose') }}" 
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('mail.compose') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    <span>Compose Email</span>
                </a>
            @endif

            @if($user?->hasPermission('mail.templates'))
                <a href="{{ route('mail.templates.index') }}" 
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('mail.templates.*') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path></svg>
                    <span>Email Templates</span>
                </a>
            @endif

            <a href="{{ route('mail.logs.index') }}" 
               class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('mail.logs.*') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Delivery Logs</span>
            </a>

            @if($user?->hasPermission('mail.settings'))
                <a href="{{ route('mail.settings.index') }}" 
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('mail.settings.*') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                    <span>SMTP Settings</span>
                </a>
            @endif

            @if($user?->hasPermission('mail.automations'))
                <a href="{{ route('mail.automations.index') }}" 
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('mail.automations.*') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <span>Automations</span>
                </a>
            @endif
        </div>
    @endif

    <!-- STORE ADMINISTRATION & RBAC -->
    @if($user?->canManageStaff() || $user?->canAccessSettings())
        <div class="space-y-1 pt-1">
            <div class="px-2.5 pb-1 text-[10px] font-bold text-[#929292] uppercase tracking-wider">Store Admin</div>
            
            @if($user?->canManageStaff())
                <a href="{{ route('staff.index') }}" 
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('staff.*') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span>Staff & Roles</span>
                </a>
            @endif

            @if($user?->canAccessSettings())
                <a href="{{ route('settings.index') }}" 
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition whitespace-nowrap {{ request()->routeIs('settings.*') ? 'bg-[#B7FF00] text-[#101010] font-bold' : 'text-[#E7E7E7] hover:bg-[#1a1a1a] font-medium' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>Platform Settings</span>
                </a>
            @endif
        </div>
    @endif
@endif
