{{-- Hallmark · macrostructure: Tenant RBAC & Form Configuration Hub · theme: Tactile Sage & Deep Rail · genre: modern-minimal
 * pre-emit critique: P5 H5 E5 S5 R5 V5
 * slop test: pass
--}}
@extends('layouts.app')

@section('title', 'Manage Permissions · ' . $tenant->name)
@section('page-title', 'RBAC and Forms')
@section('page-subtitle', $tenant->name . ' RBAC Governance')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-[#ECFDF3] text-[#027A48] mb-1 border border-[#A6F4C5]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#079455]"></span>
                <span>Active Store &bull; ID: #{{ $tenant->id }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-[#111111] tracking-tight">{{ $tenant->name }}</h1>
            <p class="text-xs sm:text-sm text-[#666666]">Govern module access, communications suite, hardware inventory, and dynamic transaction forms</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="btn-secondary min-h-[38px] text-xs inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Store Details</span>
            </a>
            <a href="{{ route('superadmin.impersonate', $tenant) }}" class="btn-secondary min-h-[38px] text-xs inline-flex items-center gap-1.5 text-[#111111] font-bold border-[#D5D5D5]">
                <svg class="w-3.5 h-3.5 text-[#D97706]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>Test as Store Admin</span>
            </a>
            <a href="{{ route('superadmin.tenants.index') }}" class="btn-secondary min-h-[38px] text-xs inline-flex items-center gap-1.5">
                <span>All Stores</span>
            </a>
        </div>
    </div>

    <!-- Feature Architecture & System Capability Banner -->
    <div class="card-surface p-4 sm:p-5 shadow-2xs border border-[#E6E6E6] rounded-xl bg-white space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-[#E6E6E6] pb-3">
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-[#111111] uppercase tracking-wider">Enterprise Feature Architecture</h3>
                <p class="text-[11px] text-[#666666]">Integrated operational capabilities available to this business tenant</p>
            </div>
            <!-- Quick Presets -->
            <div class="flex items-center gap-1.5 flex-wrap">
                <span class="text-[11px] font-bold text-[#666666] mr-1 hidden lg:inline">Quick Presets:</span>
                <button type="button" onclick="applyPreset('enterprise')" class="px-2.5 py-1 rounded-md text-[11px] font-bold transition bg-[#111111] text-[#B7FF00] hover:bg-[#222]">
                    Full Enterprise
                </button>
                <button type="button" onclick="applyPreset('standard_pos')" class="px-2.5 py-1 rounded-md text-[11px] font-bold transition bg-[#F7F7F6] text-[#111111] border border-[#E6E6E6] hover:bg-[#EDEDEC]">
                    Retail POS (No Mail)
                </button>
                <button type="button" onclick="applyPreset('repair_center')" class="px-2.5 py-1 rounded-md text-[11px] font-bold transition bg-[#FEF0C7] text-[#B54708] border border-[#FEDF89] hover:bg-[#FDE68A]">
                    Repair Workshop
                </button>
                <button type="button" onclick="applyPreset('cashier')" class="px-2.5 py-1 rounded-md text-[11px] font-bold transition bg-[#F7F7F6] text-[#111111] border border-[#E6E6E6] hover:bg-[#EDEDEC]">
                    Strict Cashier
                </button>
                <button type="button" onclick="applyPreset('toggle_mail')" class="px-2.5 py-1 rounded-md text-[11px] font-bold transition bg-[#ECFDF3] text-[#027A48] border border-[#A6F4C5] hover:bg-[#D1FADF] flex items-center gap-1">
                    <svg class="w-3 h-3 text-[#079455]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span>Toggle Email Suite</span>
                </button>
            </div>
        </div>

        <!-- Capability Pillars Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5">
            <div class="p-2.5 rounded-lg bg-[#F7F7F6] border border-[#EDEDEC] space-y-1">
                <div class="text-[10px] font-mono font-bold text-[#666666] uppercase">Billing & POS</div>
                <div class="text-xs font-bold text-[#111111]">Counter Invoicing</div>
                <div class="text-[10px] text-[#079455] font-medium flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#079455]"></span>
                    <span>PDF & Receipts</span>
                </div>
            </div>

            <div class="p-2.5 rounded-lg bg-[#F7F7F6] border border-[#EDEDEC] space-y-1">
                <div class="text-[10px] font-mono font-bold text-[#666666] uppercase">Stock Control</div>
                <div class="text-xs font-bold text-[#111111]">Devices & IMEIs</div>
                <div class="text-[10px] text-[#079455] font-medium flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#079455]"></span>
                    <span>Stock Adjustments</span>
                </div>
            </div>

            <div class="p-2.5 rounded-lg bg-[#F7F7F6] border border-[#EDEDEC] space-y-1">
                <div class="text-[10px] font-mono font-bold text-[#666666] uppercase">Ledger & Khata</div>
                <div class="text-xs font-bold text-[#111111]">Customer Credit</div>
                <div class="text-[10px] text-[#079455] font-medium flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#079455]"></span>
                    <span>Settlements</span>
                </div>
            </div>

            <div class="p-2.5 rounded-lg bg-[#111111] text-white border border-[#222] space-y-1 relative overflow-hidden">
                <div class="text-[10px] font-mono font-bold text-[#B7FF00] uppercase flex items-center justify-between">
                    <span>Mailing Suite</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-[#B7FF00]"></span>
                </div>
                <div class="text-xs font-bold text-white">SMTP & Templates</div>
                <div class="text-[10px] text-[#D5DDC8] font-medium">Auto Billing Triggers</div>
            </div>

            <div class="p-2.5 rounded-lg bg-[#F7F7F6] border border-[#EDEDEC] space-y-1">
                <div class="text-[10px] font-mono font-bold text-[#666666] uppercase">Staff & RBAC</div>
                <div class="text-xs font-bold text-[#111111]">Cashier Roles</div>
                <div class="text-[10px] text-[#079455] font-medium flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#079455]"></span>
                    <span>Role Enforcement</span>
                </div>
            </div>

            <div class="p-2.5 rounded-lg bg-[#F7F7F6] border border-[#EDEDEC] space-y-1">
                <div class="text-[10px] font-mono font-bold text-[#666666] uppercase">Branding & UPI</div>
                <div class="text-xs font-bold text-[#111111]">Counter QR & Tax</div>
                <div class="text-[10px] text-[#079455] font-medium flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#079455]"></span>
                    <span>Store Identity</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Governance Form -->
    <form method="POST" action="{{ route('superadmin.tenants.permissions.update', $tenant) }}" class="space-y-6" id="tenant_governance_form">
        @csrf
        @method('PUT')

        <!-- Primary Mode Navigation Tabs: Permissions vs Form Fields -->
        <div class="flex items-center gap-2 border-b border-[#E6E6E6] pb-2 overflow-x-auto scrollbar-none">
            <button type="button" onclick="switchGovMode('perms')" id="gov_btn_perms" class="gov-mode-btn px-4 py-2.5 rounded-xl text-xs font-bold transition whitespace-nowrap flex items-center gap-2 bg-[#111111] text-[#B7FF00]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                <span>1. Module & Action RBAC Permissions</span>
                <span class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold bg-[#B7FF00] text-[#111111]">{{ count(\App\Services\RbacPermissionManager::getModules()) }} Modules</span>
            </button>
            <button type="button" onclick="switchGovMode('forms')" id="gov_btn_forms" class="gov-mode-btn px-4 py-2.5 rounded-xl text-xs font-bold transition whitespace-nowrap flex items-center gap-2 bg-white text-[#666666] border border-[#E6E6E6] hover:text-[#111111]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                <span>2. Transaction Form Customization</span>
                <span class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold bg-[#F2F4F7] text-[#475569]">{{ count(\App\Services\DynamicFormConfig::getForms()) }} Forms</span>
            </button>
        </div>

        <!-- Section 1: Granular Permissions -->
        <div id="gov_section_perms" class="gov-section-pane space-y-4">
            @include('superadmin.partials.permissions-form', ['permissions' => $permissions, 'businessType' => $tenant->business_type])
        </div>

        <!-- Section 2: Dynamic Form Fields -->
        <div id="gov_section_forms" class="gov-section-pane hidden space-y-4">
            @include('superadmin.partials.form-fields-config', ['formConfig' => $formConfig, 'businessType' => $tenant->business_type])
        </div>

        <!-- Sticky Floating Save Bar -->
        <div class="card-surface p-4 shadow-lg border border-[#E6E6E6] rounded-xl bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-3 sticky bottom-3 z-30">
            <div class="text-xs text-[#666666] flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#079455]"></span>
                <span>Governance changes take effect <strong class="text-[#111111]">immediately</strong> across all terminals and cashier logins of this store.</span>
            </div>
            <div class="flex items-center justify-end gap-2 w-full sm:w-auto">
                <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="btn-secondary min-h-[40px] text-xs flex-1 sm:flex-initial text-center justify-center">
                    Cancel
                </a>
                <button type="submit" class="btn-lime min-h-[40px] px-6 text-xs font-bold shadow-xs flex-1 sm:flex-initial justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    <span>Save All Changes</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function switchGovMode(mode) {
        document.querySelectorAll('.gov-section-pane').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.gov-mode-btn').forEach(btn => {
            btn.classList.remove('bg-[#111111]', 'text-[#B7FF00]');
            btn.classList.add('bg-white', 'text-[#666666]', 'border', 'border-[#E6E6E6]');
        });

        const activePane = document.getElementById('gov_section_' + mode);
        const activeBtn = document.getElementById('gov_btn_' + mode);
        if (activePane) activePane.classList.remove('hidden');
        if (activeBtn) {
            activeBtn.classList.remove('bg-white', 'text-[#666666]', 'border', 'border-[#E6E6E6]');
            activeBtn.classList.add('bg-[#111111]', 'text-[#B7FF00]');
        }
    }

    function applyPreset(preset) {
        switchGovMode('perms');
        
        if (preset === 'enterprise') {
            document.querySelectorAll('input[name^="permissions["]').forEach(cb => {
                cb.checked = true;
            });
        } else if (preset === 'standard_pos') {
            document.querySelectorAll('input[name^="permissions["]').forEach(cb => {
                cb.checked = true;
            });
            // Disable repair and spare parts modules for selling-only preset
            document.querySelectorAll('input[name^="permissions[repairs]"], input[name^="permissions[spare_parts]"]').forEach(cb => {
                cb.checked = false;
            });
        } else if (preset === 'repair_center') {
            document.querySelectorAll('input[name^="permissions["]').forEach(cb => {
                cb.checked = true;
            });
            // Disable devices/item inventory modules for repair-only preset
            document.querySelectorAll('input[name^="permissions[items]"]').forEach(cb => {
                cb.checked = false;
            });
        } else if (preset === 'cashier') {
            document.querySelectorAll('input[name^="permissions["]').forEach(cb => {
                cb.checked = false;
            });
            const allowed = [
                'perm_dashboard_view',
                'perm_invoices_view',
                'perm_invoices_create',
                'perm_invoices_print',
                'perm_invoices_record_payments',
                'perm_items_view',
                'perm_customers_view',
                'perm_customers_create'
            ];
            allowed.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.checked = true;
            });
        } else if (preset === 'toggle_mail') {
            const mailBoxes = document.querySelectorAll('input[name^="permissions[mail]"]');
            const allChecked = Array.from(mailBoxes).every(cb => cb.checked);
            mailBoxes.forEach(cb => {
                cb.checked = !allChecked;
            });
            const invEmail = document.getElementById('perm_invoices_email');
            if (invEmail) invEmail.checked = !allChecked;
        }

        if (typeof updateCounters === 'function') {
            updateCounters();
        }
    }
</script>
@endsection
