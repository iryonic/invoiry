@extends('errors.layout')

@section('title', '403 Forbidden · Access Restricted')
@section('code-label', '403 ACCESS RESTRICTED')
@section('dot-color', 'bg-[#D97706]')

@section('icon')
<svg class="w-8 h-8 text-[#D97706]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
</svg>
@endsection

@section('heading', 'Administrative Permission Required')

@section('message')
{{ $exception?->getMessage() ?: 'Your staff account role does not have authorization to view this section or execute this action on the counter terminal.' }}
@endsection

@section('guidance')
<div class="flex items-start gap-2">
    <span class="font-bold text-[#111111] shrink-0 font-mono">ROLE ACCESS:</span>
    <span>Permissions are configured per store. Contact your Store Admin or Platform Super Admin to grant access to this module.</span>
</div>
@endsection

