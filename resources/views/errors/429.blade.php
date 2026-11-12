@extends('errors.layout')

@section('title', '429 Too Many Requests · Rate Limited')
@section('code-label', '429 RATE LIMIT EXCEEDED')
@section('dot-color', 'bg-[#DC2626]')

@section('icon')
<svg class="w-8 h-8 text-[#DC2626]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
</svg>
@endsection

@section('heading', 'Too Many Requests')

@section('message')
{{ $exception?->getMessage() ?: 'Terminal rate limiting is active to protect transaction integrity. Too many requests were sent in a short timeframe.' }}
@endsection

@section('guidance')
<div class="flex items-start gap-2">
    <span class="font-bold text-[#111111] shrink-0 font-mono">RATE LIMIT:</span>
    <span>Please wait approximately 30 to 60 seconds before submitting another action or retrying sign-in.</span>
</div>
@endsection

@section('primary-action')
<button type="button" onclick="setTimeout(() => window.location.reload(), 1000)"
    class="btn-lime w-full sm:w-auto min-h-[44px] text-xs font-bold inline-flex items-center justify-center gap-2 shadow-xs">
    <span>Wait & Reload Terminal</span>
</button>
@endsection

