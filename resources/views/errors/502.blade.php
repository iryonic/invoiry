@extends('errors.layout')

@section('title', '502 Bad Gateway · ' . config('app.name', 'INVOIRY'))
@section('code-label', 'HTTP 502')
@section('dot-color', 'bg-[#DC2626]')

@section('icon')
<svg class="w-8 h-8 text-[#DC2626]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
@endsection

@section('heading', 'Bad Gateway')
@section('message', 'The terminal proxy received an invalid upstream response. Please retry in a few moments.')

