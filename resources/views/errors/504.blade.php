@extends('errors.layout')

@section('title', '504 Gateway Timeout · ' . config('app.name', 'INVOIRY'))
@section('code-label', 'HTTP 504')
@section('dot-color', 'bg-[#DC2626]')

@section('icon')
<svg class="w-8 h-8 text-[#DC2626]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
@endsection

@section('heading', 'Gateway Timeout')
@section('message', 'The upstream server did not respond within the allocated time window.')

