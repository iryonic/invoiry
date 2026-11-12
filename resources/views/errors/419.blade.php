@extends('errors.layout')

@section('title', '419 Page Expired · Session Timeout')
@section('code-label', '419 SESSION EXPIRED')
@section('dot-color', 'bg-[#D97706]')

@section('icon')
<svg class="w-8 h-8 text-[#D97706]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
</svg>
@endsection

@section('heading', 'Security Session Expired')

@section('message')
Your browser CSRF session token has expired due to inactivity. This protects counter invoicing transactions from stale data.
@endsection

@section('guidance')
<div class="flex items-start gap-2">
    <span class="font-bold text-[#111111] shrink-0 font-mono">SOLUTION:</span>
    <span>Click the button below to refresh your security credentials and reload the terminal counter.</span>
</div>
@endsection

@section('primary-action')
<button type="button" onclick="window.location.reload()"
    class="btn-lime w-full sm:w-auto min-h-[44px] text-xs font-bold inline-flex items-center justify-center gap-2 shadow-xs">
    <svg class="w-4 h-4 text-[#101010]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
    <span>Refresh Terminal Session</span>
</button>
@endsection

