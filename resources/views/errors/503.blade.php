@extends('errors.layout')

@php
    $maintenanceHeading = \App\Models\SystemSetting::get('maintenance_heading', 'Scheduled System Maintenance');
    $maintenanceMessage = \App\Models\SystemSetting::get('maintenance_message', 'The terminal database and POS infrastructure are undergoing scheduled optimization. Normal counter service will resume shortly.');
    $reopenAt = \App\Models\SystemSetting::get('maintenance_reopen_at');
@endphp

@section('title', '503 Maintenance Mode · ' . config('app.name', 'INVOIRY'))
@section('code-label', '503 TERMINAL MAINTENANCE')
@section('dot-color', 'bg-[#D97706]')

@section('icon')
<svg class="w-8 h-8 text-[#D97706]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
</svg>
@endsection

@section('heading', $maintenanceHeading)

@section('message')
{{ $maintenanceMessage }}
@endsection

@if($reopenAt)
    @section('guidance')
    <div class="flex items-center justify-between text-xs font-mono">
        <span class="font-bold text-[#111111]">Estimated Resumption:</span>
        <span class="text-[#D97706] font-bold">{{ $reopenAt }}</span>
    </div>
    @endsection
@endif

@section('primary-action')
<div class="flex flex-col sm:flex-row items-center gap-2">
    <button type="button" onclick="window.location.reload()"
        class="btn-lime w-full sm:w-auto min-h-[44px] text-xs font-bold inline-flex items-center justify-center gap-2 shadow-xs">
        <svg class="w-4 h-4 text-[#101010]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        <span>Check Availability</span>
    </button>

    @if(auth()->check() && auth()->user()->isSuperAdmin())
        <a href="{{ route('superadmin.settings.index') }}#tab=maintenance"
           class="btn-dark w-full sm:w-auto min-h-[44px] text-xs font-bold inline-flex items-center justify-center gap-2">
            <span>Platform Controls</span>
        </a>
    @endif
</div>
@endsection
