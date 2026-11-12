@extends('errors.layout')

@section('title', '400 Bad Request · ' . config('app.name', 'INVOIRY'))
@section('code-label', 'HTTP 400')
@section('dot-color', 'bg-[#D97706]')

@section('icon')
<svg class="w-8 h-8 text-[#D97706]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
@endsection

@section('heading', 'Bad Request')
@section('message', 'The terminal received an invalid request format or corrupted parameters.')

