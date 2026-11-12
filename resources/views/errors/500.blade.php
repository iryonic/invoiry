@extends('errors.layout')

@section('title', '500 Internal Server Error')
@section('code-label', '500 TERMINAL EXCEPTION')
@section('dot-color', 'bg-[#DC2626]')

@section('icon')
<svg class="w-8 h-8 text-[#DC2626]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
</svg>
@endsection

@section('heading', 'Terminal Processing Error')

@section('message')
An unexpected exception was encountered while executing this operation. System diagnostics have safely captured the event.
@endsection

@section('guidance')
<div class="space-y-1">
    <div class="flex items-start gap-2">
        <span class="font-bold text-[#111111] shrink-0 font-mono">SAFETY:</span>
        <span>Your inventory stock and finalized invoice data remain intact and unaffected.</span>
    </div>
    @if(config('app.debug') && $exception)
        <div class="mt-2 p-2 bg-[#FEF2F2] border border-[#FECDCA] text-[#B42318] rounded font-mono text-[11px] overflow-x-auto">
            {{ $exception->getMessage() }}
        </div>
    @endif
</div>
@endsection

