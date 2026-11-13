@extends('errors.layout')

@php
    $maintenanceHeading = \App\Models\SystemSetting::get('maintenance_heading', 'Scheduled System Maintenance');
    $maintenanceMessage = \App\Models\SystemSetting::get('maintenance_message', 'The terminal database and POS infrastructure are undergoing scheduled optimization. Normal counter service will resume shortly.');
    $reopenAt = \App\Models\SystemSetting::get('maintenance_reopen_at');
@endphp

@section('body-bg', 'bg-white')
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

@section('custom-actions')
    @if(auth()->check() && auth()->user()->isSuperAdmin())
        <a href="{{ route('superadmin.settings.index') }}#tab=maintenance"
           class="btn-lime w-full sm:w-auto min-h-[44px] px-6 text-xs font-bold inline-flex items-center justify-center gap-2 shadow-xs">
            <svg class="w-4 h-4 text-[#101010]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span>Revoke / Manage Maintenance</span>
        </a>
    @elseif(auth()->check())
        <div class="flex flex-col sm:flex-row items-center gap-2.5 w-full sm:w-auto">
            <a href="{{ route('login') }}"
               class="btn-dark w-full sm:w-auto min-h-[44px] px-6 text-xs font-bold inline-flex items-center justify-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-[#B7FF00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                <span>Login to Terminal</span>
            </a>
            <form action="{{ route('logout') }}" method="POST" class="w-full sm:w-auto">
                @csrf
                <button type="submit" class="btn-secondary w-full sm:w-auto min-h-[44px] px-5 text-xs font-semibold inline-flex items-center justify-center gap-1.5">
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    @else
        <a href="{{ route('login') }}"
           class="btn-dark w-full sm:w-auto min-h-[44px] px-6 text-xs font-bold inline-flex items-center justify-center gap-2 shadow-xs">
            <svg class="w-4 h-4 text-[#B7FF00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
            <span>Login to Terminal</span>
        </a>
    @endif
@endsection
