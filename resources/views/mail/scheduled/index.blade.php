@extends('layouts.app')

@section('title', 'Scheduled Email Queue — Invoiry')

@section('content')
<div class="space-y-6">
    {{-- Header Banner --}}
    <div class="card-surface p-6 sm:p-7 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-[#B7FF00]/20 text-[#050505] border border-[#B7FF00]">
                    Delayed Dispatch
                </span>
                <span class="text-xs font-mono text-[#666666]">Automated Cron Daemon</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-[#050505] tracking-tight">Scheduled Emails Queue</h1>
            <p class="text-xs text-[#666666] mt-0.5">Upcoming queued messages awaiting their designated delivery time window.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('mail.compose') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-[#050505] text-[#B7FF00] hover:bg-black transition-all shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>Schedule New Mail</span>
            </a>
        </div>
    </div>

    {{-- Subnavigation Tabs --}}
    @include('mail.partials.nav-tabs')

    {{-- Daemon Status Info Notice --}}
    <div class="p-4 bg-[#F7F7F6] rounded-2xl border border-[#E6E6E6] flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
        <div class="flex items-start sm:items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-[#050505] text-[#B7FF00] flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <span class="font-bold text-[#050505]">Automated Dispatch Daemon Active</span>
                <p class="text-[11px] text-[#666666]">Runs every 60 seconds via Laravel Scheduler (<code>mail:process-scheduled</code>). Past-due queues are immediately forwarded to background workers.</p>
            </div>
        </div>
        <div class="text-[11px] font-mono text-[#888888] shrink-0">
            Server Clock: {{ now()->format('d M Y, h:i A') }}
        </div>
    </div>

    {{-- Scheduled Table --}}
    <div class="card-surface overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#E6E6E6] bg-[#F7F7F6] text-[11px] font-bold uppercase tracking-wider text-[#666666]">
                        <th class="py-3 px-4">Scheduled For</th>
                        <th class="py-3 px-4">Recipient</th>
                        <th class="py-3 px-4">Subject</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Initiated By</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E6E6E6] text-xs">
                    @forelse($scheduledEmails as $item)
                        <tr class="hover:bg-[#F7F7F6]/60 transition-colors">
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    <div>
                                        <div class="font-mono font-bold text-[#050505]">{{ $item->scheduled_at->format('d M Y, h:i A') }}</div>
                                        <div class="text-[10px] font-mono text-amber-700">Due in {{ $item->scheduled_at->diffForHumans(null, true) }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="font-bold text-[#050505]">{{ $item->recipient_name ?: 'Customer' }}</div>
                                <div class="text-[11px] font-mono text-[#666666]">{{ $item->recipient_email }}</div>
                            </td>

                            <td class="py-3.5 px-4 max-w-xs truncate font-medium text-[#050505]">
                                {{ $item->subject }}
                            </td>

                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#F7F7F6] border border-[#E6E6E6] text-[#050505]">
                                    {{ $item->category }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4 whitespace-nowrap text-xs text-[#666666]">
                                {{ $item->user?->name ?: 'System Automation' }}
                            </td>

                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    {{-- Send Now --}}
                                    <form method="POST" action="{{ route('mail.scheduled.dispatch', $item) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 text-[11px] font-bold bg-[#050505] text-[#B7FF00] hover:bg-black rounded-lg transition-colors" title="Bypass timer and send now">
                                            Send Now
                                        </button>
                                    </form>

                                    {{-- Cancel --}}
                                    <form method="POST" action="{{ route('mail.scheduled.cancel', $item) }}" onsubmit="return confirm('Cancel this scheduled email transmission?');" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 text-[11px] font-bold bg-white text-red-600 hover:bg-red-50 border border-red-200 rounded-lg transition-colors" title="Cancel scheduled email">
                                            Cancel
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-[#666666]">
                                <svg class="w-10 h-10 mx-auto text-[#CCCCCC] mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <p class="text-xs font-semibold text-[#050505]">No pending scheduled emails</p>
                                <p class="text-[11px] text-[#888888] mt-1">When composing a bill or communication, specify a future date & time to queue it here.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($scheduledEmails->hasPages())
            <div class="p-4 border-t border-[#E6E6E6]">
                {{ $scheduledEmails->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

