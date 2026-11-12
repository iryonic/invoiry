@extends('errors.layout')

@section('title', '404 Not Found')
@section('code-label', '404 NOT FOUND')
@section('dot-color', 'bg-[#DC2626]')

@section('icon')
<svg class="w-8 h-8 text-[#DC2626]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
</svg>
@endsection

@section('heading', 'Record or Page Not Located')

@section('message')
{{ $exception?->getMessage() ?: 'The requested invoice, device, customer ledger, or URL route could not be found on this store terminal.' }}
@endsection

@section('guidance')
<div class="flex items-start gap-2">
    <span class="font-bold text-[#111111] shrink-0 font-mono">DIAGNOSTIC:</span>
    <span>The record may have been deleted, moved to another store partition, or the link may have expired.</span>
</div>
@endsection

