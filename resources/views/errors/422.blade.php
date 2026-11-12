@extends('errors.layout')

@section('title', '422 Unprocessable Content · ' . config('app.name', 'INVOIRY'))
@section('code-label', 'HTTP 422')
@section('dot-color', 'bg-[#D97706]')

@section('icon')
<svg class="w-8 h-8 text-[#D97706]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
@endsection

@section('heading', 'Validation Error')
@section('message', 'The submitted data could not be processed due to semantic validation constraints.')

