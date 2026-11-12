@extends('errors.layout')

@section('title', '401 Unauthorized · ' . config('app.name', 'INVOIRY'))
@section('code-label', 'HTTP 401')
@section('dot-color', 'bg-[#D97706]')

@section('icon')
<svg class="w-8 h-8 text-[#D97706]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
@endsection

@section('heading', 'Authentication Required')
@section('message', 'Please sign in to the terminal with valid credentials to access this protected workspace.')

@section('primary-action')
<a href="{{ route('login') }}" class="btn-lime w-full sm:w-auto min-h-[44px] text-xs font-bold inline-flex items-center justify-center gap-2 shadow-xs">
    <span>Sign In to Terminal</span>
</a>
@endsection

