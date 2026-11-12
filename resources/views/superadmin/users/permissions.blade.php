{{-- Hallmark · macrostructure: User RBAC Permissions Editor · theme: Tactile Sage & Deep Rail · genre: modern-minimal
 * pre-emit critique: P5 H5 E5 S5 R5 V5
 * slop test: pass
--}}
@extends('layouts.app')

@section('title', 'User Permissions · ' . $user->name)
@section('page-title', 'User Access & Permissions')
@section('page-subtitle', $user->name . ' (' . str_replace('_', ' ', $user->role) . ')')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-[#F2F4F7] text-[#344054] mb-1 border border-[#EAECF0]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#111111]"></span>
                <span class="capitalize">{{ str_replace('_', ' ', $user->role) }}</span>
                @if($user->tenant)
                    <span class="text-[#929292]">&bull;</span>
                    <span class="font-normal">{{ $user->tenant->name }}</span>
                @endif
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-[#111111] tracking-tight">Permissions: {{ $user->name }}</h1>
            <p class="text-xs sm:text-sm text-[#666666]">Configure granular module capabilities, action privileges, and view permissions for this user</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @if($user->tenant)
                <a href="{{ route('superadmin.tenants.permissions', $user->tenant) }}" class="btn-secondary min-h-[38px] text-xs inline-flex items-center gap-1.5 font-bold text-[#111111]" title="Configure Transaction Form Fields for {{ $user->tenant->name }}">
                    <svg class="w-3.5 h-3.5 text-[#079455]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    <span>Store Form Fields</span>
                </a>
            @endif
            <a href="{{ route('superadmin.users.edit', $user) }}" class="btn-secondary min-h-[38px] text-xs inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                <span>Edit Profile</span>
            </a>
            <a href="{{ route('superadmin.users.index') }}" class="btn-secondary min-h-[38px] text-xs inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Users Directory</span>
            </a>
        </div>
    </div>

    <!-- Status Banner -->
    @if($user->isSuperAdmin())
        <div class="p-4 rounded-xl bg-[#050505] text-white flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-[#B7FF00] text-[#050505] flex items-center justify-center font-bold flex-shrink-0 text-sm">
                &starf;
            </div>
            <div class="space-y-0.5">
                <h4 class="text-xs sm:text-sm font-bold text-[#B7FF00]">Super Administrator Account</h4>
                <p class="text-[11px] text-[#D5DDC8]">
                    Super Admins bypass all module and action permission restrictions with full system-wide access. The switches below represent their configured permission profile if their role changes.
                </p>
            </div>
        </div>
    @elseif($hasCustomOverrides)
        <div class="p-3.5 rounded-xl bg-[#ECFDF3] border border-[#A6F4C5] flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#079455]"></span>
                <span class="text-xs font-bold text-[#027A48]">Custom User Permissions Active</span>
                <span class="text-[11px] text-[#067647] hidden sm:inline">&bull; This user has personalized permissions overriding store defaults.</span>
            </div>
            <form method="POST" action="{{ route('superadmin.users.permissions.update', $user) }}" onsubmit="return confirm('Reset this user\'s permissions to inherit store defaults?');">
                @csrf
                @method('PUT')
                <input type="hidden" name="reset_to_default" value="1">
                <button type="submit" class="text-xs font-bold text-[#027A48] hover:text-[#054F31] underline">
                    Reset to Store Defaults
                </button>
            </form>
        </div>
    @else
        <div class="p-3.5 rounded-xl bg-[#EFF8FF] border border-[#B2DDFF] flex items-center gap-2.5">
            <span class="w-2 h-2 rounded-full bg-[#175CD3]"></span>
            <span class="text-xs font-semibold text-[#175CD3]">Inheriting Store Defaults:</span>
            <span class="text-xs text-[#1E40AF]">Saving any changes below will create a dedicated custom override for this user.</span>
        </div>
    @endif

    <!-- Permissions Form -->
    <form method="POST" action="{{ route('superadmin.users.permissions.update', $user) }}" class="space-y-6">
        @csrf
        @method('PUT')

        @include('superadmin.partials.permissions-form', ['permissions' => $permissions])

        <!-- Sticky Floating Save Bar -->
        <div class="card-surface p-4 shadow-md border border-[#E6E6E6] rounded-xl bg-white flex items-center justify-between gap-3 sticky bottom-3 z-30">
            <div class="text-xs text-[#666666]">
                Permission updates apply <strong class="text-[#111111]">immediately</strong> upon next request.
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('superadmin.users.index') }}" class="btn-secondary min-h-[40px] text-xs">
                    Cancel
                </a>
                <button type="submit" class="btn-lime min-h-[40px] px-6 text-xs font-bold shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    <span>Save User Permissions</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

