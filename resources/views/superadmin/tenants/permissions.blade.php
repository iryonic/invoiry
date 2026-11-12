{{-- Hallmark · macrostructure: Tenant RBAC & Form Configuration Hub · theme: Tactile Sage & Deep Rail · genre: modern-minimal
 * pre-emit critique: P5 H5 E5 S5 R5 V5
 * slop test: pass
--}}
@extends('layouts.app')

@section('title', 'Manage Permissions · ' . $tenant->name)
@section('page-title', 'RBAC and Forms')
@section('page-subtitle', $tenant->name . ' RBAC Permissions')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-[#ECFDF3] text-[#027A48] mb-1 border border-[#A6F4C5]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#079455]"></span>
                <span>Active Business Store</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-[#111111] tracking-tight">{{ $tenant->name }}</h1>
            <p class="text-xs sm:text-sm text-[#666666]">Configure granular feature access and customize transaction form input fields</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="btn-secondary min-h-[38px] text-xs inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Store </span>
            </a>
            <a href="{{ route('superadmin.impersonate', $tenant) }}" class="btn-secondary min-h-[38px] text-xs inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-[#D97706]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>Test as Store Admin</span>
            </a>
        </div>
    </div>

    <!-- Governance Form -->
    <form method="POST" action="{{ route('superadmin.tenants.permissions.update', $tenant) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Primary Mode Navigation Tabs: Permissions vs Form Fields -->
        <div class="flex justify-between items-center gap-2 border-b border-[#E6E6E6] pb-2 overflow-x-auto scrollbar-none">
            <button type="button" onclick="switchGovMode('perms')" id="gov_btn_perms" class="gov-mode-btn px-3.5 sm:px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap flex-shrink-0 bg-[#111111] text-[#B7FF00]">
                1. RBAC Permissions
            </button>
            <button type="button" onclick="switchGovMode('forms')" id="gov_btn_forms" class="gov-mode-btn px-3.5 sm:px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap flex-shrink-0 bg-white text-[#666666] border border-[#E6E6E6] hover:text-[#111111]">
                2. Form Fields
            </button>
        </div>

        <!-- Section 1: Granular Permissions -->
        <div id="gov_section_perms" class="gov-section-pane space-y-4">
            @include('superadmin.partials.permissions-form', ['permissions' => $permissions])
        </div>

        <!-- Section 2: Dynamic Form Fields -->
        <div id="gov_section_forms" class="gov-section-pane hidden space-y-4">
            @include('superadmin.partials.form-fields-config', ['formConfig' => $formConfig])
        </div>

        <!-- Sticky Floating Save Bar -->
        <div class="card-surface p-3.5 sm:p-4 shadow-lg border border-[#E6E6E6] rounded-xl bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-3 sticky bottom-16 lg:bottom-4 z-30">
            <div class="text-xs text-[#666666] text-center sm:text-left">
                Changes take effect <strong class="text-[#111111]">immediately</strong> for all users of this store.
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
</script>
@endsection

