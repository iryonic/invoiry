@extends('layouts.app')

@section('title', 'Email Automations & Event Triggers — Invoiry')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    {{-- Header Banner --}}
    <div class="card-surface p-6 sm:p-7 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-[#B7FF00]/20 text-[#050505] border border-[#B7FF00]">
                    Zero-Touch Dispatch
                </span>
                <span class="text-xs font-mono text-[#666666]">Event-Driven Billing Rules</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-[#050505] tracking-tight">Automated Email Dispatch Rules</h1>
            <p class="text-xs text-[#666666] mt-0.5">Configure hands-free automatic email notifications for invoice generation, payment confirmations, and overdue payment follow-ups.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('mail.templates.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold border border-[#E6E6E6] bg-white text-[#050505] hover:bg-[#F7F7F6] transition-colors">
                Manage Templates
            </a>
        </div>
    </div>

    {{-- Subnavigation Tabs --}}
    @include('mail.partials.nav-tabs')

    {{-- Automations Form --}}
    <form method="POST" action="{{ route('mail.automations.update') }}" class="space-y-5">
        @csrf

        <div class="space-y-4">
            @foreach($automations as $eventKey => $data)
                @php
                    $rec = $data['record'];
                    $meta = $data['meta'];
                @endphp
                <div class="card-surface p-5 sm:p-6 space-y-4 border {{ $rec->is_enabled ? 'border-l-4 border-l-[#B7FF00]' : 'border-[#E6E6E6]' }}">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#050505] text-[#B7FF00] flex items-center justify-center font-bold text-sm shrink-0">
                                @if($eventKey === 'invoice.finalized')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                @elseif($eventKey === 'payment.created')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                @endif
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-bold text-[#050505]">{{ $meta['label'] }}</h3>
                                    <span class="text-[10px] font-mono text-[#888888]">{{ $eventKey }}</span>
                                </div>
                                <p class="text-xs text-[#666666] mt-0.5">{{ $meta['default_description'] }}</p>
                            </div>
                        </div>

                        {{-- Enabled Toggle --}}
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" name="automations[{{ $rec->id }}][is_enabled]" value="1" {{ $rec->is_enabled ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#050505]"></div>
                            <span class="ml-2.5 text-xs font-bold text-[#050505]">{{ $rec->is_enabled ? 'Enabled' : 'Disabled' }}</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-[#E6E6E6]">
                        {{-- Template Selection --}}
                        <div>
                            <label class="block text-xs font-bold text-[#050505] mb-1.5">Email Template to Dispatch</label>
                            <select name="automations[{{ $rec->id }}][template_id]" class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs text-[#050505] focus:outline-none focus:border-[#050505]">
                                @foreach($templates as $tmpl)
                                    <option value="{{ $tmpl->id }}" {{ $rec->template_id == $tmpl->id ? 'selected' : '' }}>
                                        {{ $tmpl->name }} ({{ $tmpl->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Timing / Delay --}}
                        <div>
                            @if($eventKey === 'invoice.due_reminder')
                                <label class="block text-xs font-bold text-[#050505] mb-1.5">Reminder Timing</label>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-[#666666]">Send when invoice is</span>
                                    <input type="number" name="automations[{{ $rec->id }}][timing_days]" value="{{ $rec->timing_days ?: 3 }}" min="1" max="60" class="w-16 px-2.5 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs font-mono font-bold text-center text-[#050505] focus:outline-none focus:border-[#050505]">
                                    <span class="text-xs text-[#666666]">days past due date</span>
                                </div>
                            @else
                                <label class="block text-xs font-bold text-[#050505] mb-1.5">Dispatch Schedule</label>
                                <div class="flex items-center gap-2 text-xs text-[#666666] pt-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span>Immediate trigger on event completion</span>
                                    <input type="hidden" name="automations[{{ $rec->id }}][timing_days]" value="0">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E6E6E6]">
            <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-[#050505] text-[#B7FF00] hover:bg-black transition-all shadow-sm">
                Save Automation Rules
            </button>
        </div>
    </form>
</div>
@endsection

