@extends('layouts.app')

@section('title', 'Email Logs & Delivery Tracking — Invoiry')

@section('content')
<div class="space-y-6">
    {{-- Header Banner --}}
    <div class="card-surface p-6 sm:p-7 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-[#B7FF00]/20 text-[#050505] border border-[#B7FF00]">
                    Audit Trail & Deliverability
                </span>
                <span class="text-xs font-mono text-[#666666]">Outgoing Communications</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-[#050505] tracking-tight">Email Logs & Delivery Tracking</h1>
            <p class="text-xs text-[#666666] mt-0.5">Complete real-time log of every dispatch, queue attempt, failure trace, and customer transmission.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('mail.compose') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-[#050505] text-[#B7FF00] hover:bg-black transition-all shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>Compose Mail</span>
            </a>
        </div>
    </div>

    {{-- Subnavigation Tabs --}}
    @include('mail.partials.nav-tabs')

    {{-- Metric Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('mail.logs.index') }}" class="card-surface p-4 border {{ !request('status') || request('status') === 'all' ? 'border-[#050505] ring-1 ring-[#050505]' : 'border-[#E6E6E6]' }} hover:border-[#050505] transition-all">
            <div class="text-[11px] font-bold uppercase tracking-wider text-[#666666]">Total Dispatches</div>
            <div class="text-2xl font-black text-[#050505] mt-1 font-mono">{{ number_format($counts['all']) }}</div>
            <div class="text-[10px] text-[#888888] mt-1">All communications logged</div>
        </a>

        <a href="{{ route('mail.logs.index', ['status' => 'sent']) }}" class="card-surface p-4 border {{ request('status') === 'sent' ? 'border-emerald-600 ring-1 ring-emerald-600' : 'border-[#E6E6E6]' }} hover:border-emerald-600 transition-all">
            <div class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Delivered / Sent</div>
            <div class="text-2xl font-black text-emerald-600 mt-1 font-mono">{{ number_format($counts['sent']) }}</div>
            <div class="text-[10px] text-emerald-700/80 mt-1">Successfully transmitted via SMTP</div>
        </a>

        <a href="{{ route('mail.logs.index', ['status' => 'queued']) }}" class="card-surface p-4 border {{ request('status') === 'queued' ? 'border-amber-600 ring-1 ring-amber-600' : 'border-[#E6E6E6]' }} hover:border-amber-600 transition-all">
            <div class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Queued / In Flight</div>
            <div class="text-2xl font-black text-amber-600 mt-1 font-mono">{{ number_format($counts['queued']) }}</div>
            <div class="text-[10px] text-amber-700/80 mt-1">Awaiting worker transmission</div>
        </a>

        <a href="{{ route('mail.logs.index', ['status' => 'failed']) }}" class="card-surface p-4 border {{ request('status') === 'failed' ? 'border-red-600 ring-1 ring-red-600' : 'border-[#E6E6E6]' }} hover:border-red-600 transition-all">
            <div class="text-[11px] font-bold uppercase tracking-wider text-red-700">Failed Dispatches</div>
            <div class="text-2xl font-black text-red-600 mt-1 font-mono">{{ number_format($counts['failed']) }}</div>
            <div class="text-[10px] text-red-700/80 mt-1">Requires retry or credential check</div>
        </a>
    </div>

    {{-- Filter Toolbar --}}
    <div class="card-surface p-4">
        <form method="GET" action="{{ route('mail.logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            {{-- Search --}}
            <div class="lg:col-span-2 relative">
                <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-[#888888]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search recipient, subject, or name..." class="w-full pl-9 pr-3.5 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs text-[#050505] focus:outline-none focus:border-[#050505]">
            </div>

            {{-- Status --}}
            <div>
                <select name="status" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs text-[#050505] focus:outline-none">
                    <option value="all">All Statuses</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent / Delivered</option>
                    <option value="queued" {{ request('status') === 'queued' ? 'selected' : '' }}>Queued</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            {{-- Category --}}
            <div>
                <select name="category" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs text-[#050505] focus:outline-none">
                    <option value="all">All Categories</option>
                    <option value="invoices" {{ request('category') === 'invoices' ? 'selected' : '' }}>Invoices</option>
                    <option value="payments" {{ request('category') === 'payments' ? 'selected' : '' }}>Payments</option>
                    <option value="reminders" {{ request('category') === 'reminders' ? 'selected' : '' }}>Reminders</option>
                    <option value="customers" {{ request('category') === 'customers' ? 'selected' : '' }}>Customer Care</option>
                    <option value="system" {{ request('category') === 'system' ? 'selected' : '' }}>System</option>
                    <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>General</option>
                </select>
            </div>

            {{-- Date From --}}
            <div>
                <input type="date" name="from_date" value="{{ request('from_date') }}" title="From Date" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs text-[#050505] focus:outline-none">
            </div>

            {{-- Filter Button & Reset --}}
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 bg-[#050505] text-white text-xs font-bold rounded-xl hover:bg-black transition-colors text-center">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'category', 'from_date', 'to_date', 'invoice_id']))
                    <a href="{{ route('mail.logs.index') }}" class="px-2.5 py-2 text-xs font-semibold text-[#666666] hover:text-[#050505]">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Logs Table --}}
    <div class="card-surface overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#E6E6E6] bg-[#F7F7F6] text-[11px] font-bold uppercase tracking-wider text-[#666666]">
                        <th class="py-3 px-4">Date / Time</th>
                        <th class="py-3 px-4">Recipient</th>
                        <th class="py-3 px-4">Subject</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Linked Reference</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E6E6E6] text-xs">
                    @forelse($logs as $log)
                        <tr class="hover:bg-[#F7F7F6]/60 transition-colors">
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-mono text-[#050505] font-semibold">{{ $log->created_at->format('d M Y') }}</div>
                                <div class="text-[11px] font-mono text-[#888888]">{{ $log->created_at->format('h:i:s A') }}</div>
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="font-bold text-[#050505]">{{ $log->recipient_name ?: 'Customer' }}</div>
                                <div class="text-[11px] font-mono text-[#666666]">{{ $log->recipient_email }}</div>
                                @if(!empty($log->cc))
                                    <div class="text-[10px] text-[#888888] truncate max-w-xs">CC: {{ implode(', ', $log->cc) }}</div>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 max-w-xs">
                                <div class="font-medium text-[#050505] truncate">{{ $log->subject }}</div>
                                @if(!empty($log->attachments))
                                    <span class="inline-flex items-center gap-1 text-[10px] text-[#666666] mt-0.5">
                                        <svg class="w-3 h-3 text-[#888888]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13"/></svg>
                                        <span>{{ count($log->attachments) }} Attachment{{ count($log->attachments) > 1 ? 's' : '' }}</span>
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#F7F7F6] border border-[#E6E6E6] text-[#050505]">
                                    {{ $log->category }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($log->invoice)
                                    <a href="{{ route('invoices.show', $log->invoice) }}" class="inline-flex items-center gap-1 font-mono text-xs font-bold text-[#050505] hover:underline decoration-[#B7FF00] decoration-2">
                                        <span>#{{ $log->invoice->invoice_number }}</span>
                                    </a>
                                @elseif($log->customer)
                                    <span class="text-xs text-[#666666]">{{ $log->customer->name }}</span>
                                @else
                                    <span class="text-[#CCCCCC]">&mdash;</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider {{ $log->getStatusBadgeClass() }}">
                                    {{ $log->status }}
                                </span>
                                @if($log->status === 'failed')
                                    <div class="text-[10px] text-red-500 font-mono mt-0.5 truncate max-w-[120px]" title="{{ $log->error_message }}">
                                        {{ Str::limit($log->error_message, 18) }}
                                    </div>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    {{-- Inspect Modal Trigger --}}
                                    <button type="button" onclick="inspectLog({{ $log->id }})" class="p-1.5 text-[#666666] hover:text-[#050505] hover:bg-white rounded-lg border border-transparent hover:border-[#E6E6E6] transition-colors" title="Inspect Email & Delivery Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>

                                    {{-- Retry if failed --}}
                                    @if($log->status === 'failed' || $log->status === 'queued')
                                        <form method="POST" action="{{ route('mail.logs.retry', $log) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-amber-600 hover:text-amber-800 hover:bg-amber-50 rounded-lg border border-transparent hover:border-amber-200 transition-colors" title="Retry Delivery Now">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Delete --}}
                                    <form method="POST" action="{{ route('mail.logs.destroy', $log) }}" onsubmit="return confirm('Delete this email log record?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg border border-transparent hover:border-red-200 transition-colors" title="Delete Log">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-[#666666]">
                                <svg class="w-10 h-10 mx-auto text-[#CCCCCC] mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                                <p class="text-xs font-semibold text-[#050505]">No email logs found matching filters</p>
                                <p class="text-[11px] text-[#888888] mt-1">Outgoing emails and automated notifications will appear here.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-[#E6E6E6]">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Inspect Modal --}}
<div id="inspectModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="card-surface max-w-4xl w-full max-h-[92vh] flex flex-col overflow-hidden shadow-2xl">
        <div class="p-4 border-b border-[#E6E6E6] flex items-center justify-between bg-[#F7F7F6]">
            <div>
                <div class="flex items-center gap-2">
                    <span id="inspectStatusBadge" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"></span>
                    <h3 id="inspectSubject" class="text-sm font-bold text-[#050505] truncate max-w-xl"></h3>
                </div>
                <p id="inspectRecipient" class="text-xs font-mono text-[#666666] mt-0.5"></p>
            </div>
            <button type="button" onclick="closeInspectModal()" class="p-1 text-[#666666] hover:text-[#050505] rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Tabs inside Modal --}}
        <div class="flex items-center gap-3 px-4 pt-3 border-b border-[#E6E6E6] bg-white text-xs font-bold">
            <button type="button" id="tabBtnPreview" onclick="switchInspectTab('preview')" class="pb-2 border-b-2 border-[#050505] text-[#050505]">
                Rendered HTML
            </button>
            <button type="button" id="tabBtnPlain" onclick="switchInspectTab('plain')" class="pb-2 border-b-2 border-transparent text-[#666666] hover:text-[#050505]">
                Plain Text
            </button>
            <button type="button" id="tabBtnTech" onclick="switchInspectTab('tech')" class="pb-2 border-b-2 border-transparent text-[#666666] hover:text-[#050505]">
                Technical Audit & Trace
            </button>
        </div>

        {{-- Tab Panes --}}
        <div class="flex-1 p-4 overflow-y-auto bg-[#F7F7F6]">
            {{-- Preview Pane --}}
            <div id="panePreview" class="bg-white rounded-xl border border-[#E6E6E6] p-4 min-h-[350px]">
                <div id="inspectHtmlContent">Loading email body...</div>
            </div>

            {{-- Plain Text Pane --}}
            <div id="panePlain" class="hidden bg-white rounded-xl border border-[#E6E6E6] p-4 font-mono text-xs text-[#050505] whitespace-pre-wrap leading-relaxed min-h-[350px]">
                <div id="inspectPlainContent"></div>
            </div>

            {{-- Tech Details Pane --}}
            <div id="paneTech" class="hidden space-y-4">
                <div class="bg-white rounded-xl border border-[#E6E6E6] p-4 space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#666666]">Delivery Diagnostics</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="p-2.5 bg-[#F7F7F6] rounded-lg border border-[#E6E6E6]">
                            <span class="text-[#888888] block text-[10px] uppercase font-bold">Mailer Transport</span>
                            <span id="inspectMailer" class="font-mono font-bold text-[#050505]"></span>
                        </div>
                        <div class="p-2.5 bg-[#F7F7F6] rounded-lg border border-[#E6E6E6]">
                            <span class="text-[#888888] block text-[10px] uppercase font-bold">Attempts</span>
                            <span id="inspectAttempts" class="font-mono font-bold text-[#050505]"></span>
                        </div>
                        <div class="p-2.5 bg-[#F7F7F6] rounded-lg border border-[#E6E6E6]">
                            <span class="text-[#888888] block text-[10px] uppercase font-bold">Created At</span>
                            <span id="inspectCreatedAt" class="font-mono font-semibold text-[#050505]"></span>
                        </div>
                        <div class="p-2.5 bg-[#F7F7F6] rounded-lg border border-[#E6E6E6]">
                            <span class="text-[#888888] block text-[10px] uppercase font-bold">Sent / Delivered At</span>
                            <span id="inspectSentAt" class="font-mono font-semibold text-[#050505]"></span>
                        </div>
                    </div>

                    <div id="inspectErrorBox" class="hidden p-3 bg-red-50 rounded-xl border border-red-200 text-xs text-red-700 font-mono space-y-1">
                        <div class="font-bold uppercase tracking-wider text-[10px]">Error Trace</div>
                        <div id="inspectErrorMsg" class="whitespace-pre-wrap"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-3 border-t border-[#E6E6E6] bg-white flex justify-end">
            <button type="button" onclick="closeInspectModal()" class="px-4 py-2 bg-[#050505] text-white text-xs font-bold rounded-xl hover:bg-black">
                Close
            </button>
        </div>
    </div>
</div>

<script>
let currentLogData = null;

function inspectLog(logId) {
    document.getElementById('inspectModal').classList.remove('hidden');
    document.getElementById('inspectSubject').textContent = 'Loading dispatch details...';
    document.getElementById('inspectHtmlContent').innerHTML = '<div class="flex items-center justify-center h-48 text-[#888888] text-xs">Loading message transmission...</div>';
    switchInspectTab('preview');

    fetch(`{{ url('mail/logs') }}/${logId}`)
        .then(res => res.json())
        .then(data => {
            currentLogData = data;
            document.getElementById('inspectSubject').textContent = data.subject;
            document.getElementById('inspectRecipient').textContent = `To: ${data.recipient_name ? data.recipient_name + ' <' : ''}${data.recipient_email}${data.recipient_name ? '>' : ''}`;
            
            const badge = document.getElementById('inspectStatusBadge');
            badge.textContent = data.status;
            badge.className = `inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider ${data.status_badge}`;

            document.getElementById('inspectHtmlContent').innerHTML = data.body_html || '<p class="text-xs text-[#888888] italic">No HTML body recorded.</p>';
            document.getElementById('inspectPlainContent').textContent = data.body_text || 'No plaintext alternative.';

            document.getElementById('inspectMailer').textContent = data.mailer_used || 'Standard SMTP';
            document.getElementById('inspectAttempts').textContent = data.attempts + ' attempt(s)';
            document.getElementById('inspectCreatedAt').textContent = data.created_at || '—';
            document.getElementById('inspectSentAt').textContent = data.sent_at || 'Pending / In Queue';

            const errBox = document.getElementById('inspectErrorBox');
            if (data.error_message) {
                errBox.classList.remove('hidden');
                document.getElementById('inspectErrorMsg').textContent = data.error_message;
            } else {
                errBox.classList.add('hidden');
            }
        })
        .catch(err => {
            document.getElementById('inspectHtmlContent').innerHTML = '<div class="p-4 text-xs text-red-600 bg-red-50 rounded-lg">Failed to retrieve log details: ' + err.message + '</div>';
        });
}

function switchInspectTab(tab) {
    const tabs = ['preview', 'plain', 'tech'];
    tabs.forEach(t => {
        const pane = document.getElementById('pane' + t.charAt(0).toUpperCase() + t.slice(1));
        const btn = document.getElementById('tabBtn' + t.charAt(0).toUpperCase() + t.slice(1));
        if (t === tab) {
            pane.classList.remove('hidden');
            btn.className = 'pb-2 border-b-2 border-[#050505] text-[#050505]';
        } else {
            pane.classList.add('hidden');
            btn.className = 'pb-2 border-b-2 border-transparent text-[#666666] hover:text-[#050505]';
        }
    });
}

function closeInspectModal() {
    document.getElementById('inspectModal').classList.add('hidden');
}
</script>
@endsection

