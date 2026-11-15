@extends('layouts.app')

@section('title', 'Email Management & Queue Hub')
@section('page-title', 'Email Management Hub')
@section('page-subtitle', 'Monitor transactional delivery, SMTP server health, background queues, and sent communications')

@section('content')
<div class="space-y-6  mx-auto">

    <!-- Top Action & Navigation Tabs -->
    @include('mail.partials.nav-tabs')

    <!-- Top Banner & Quick Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-[#111111] tracking-tight">Email Operations & Delivery Overview</h2>
            <p class="text-xs sm:text-sm text-[#666666] mt-0.5">Centralized email infrastructure for invoices, payment receipts, customer notices, and scheduled queues</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('mail.compose') }}" class="btn-lime text-xs sm:text-sm px-4 py-2.5 rounded-lg font-bold flex items-center gap-2 shadow-2xs min-h-[44px]">
                <svg class="w-4 h-4 text-[#101010]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                <span>Compose Email</span>
            </a>
            <a href="{{ route('mail.settings.index') }}" class="btn-secondary text-xs sm:text-sm px-3.5 py-2.5 rounded-lg font-semibold flex items-center gap-1.5 shadow-2xs min-h-[44px]">
                <svg class="w-4 h-4 text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>SMTP Config</span>
            </a>
        </div>
    </div>

    <!-- Statistical Overview Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <!-- Total Sent -->
        <div class="card-surface p-4 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-[#666666] uppercase tracking-wider">Total Sent</span>
                <span class="w-2 h-2 rounded-full bg-[#079455]"></span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-[#111111] font-mono">{{ number_format($totalSent) }}</div>
                <div class="text-[10px] text-[#079455] font-semibold mt-0.5">{{ $successRate }}% success rate</div>
            </div>
        </div>

        <!-- Sent Today -->
        <div class="card-surface p-4 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-[#666666] uppercase tracking-wider">Sent Today</span>
                <span class="w-2 h-2 rounded-full bg-[#111111]"></span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-[#111111] font-mono">{{ number_format($sentToday) }}</div>
                <div class="text-[10px] text-[#666666] mt-0.5">Last 24 hours</div>
            </div>
        </div>

        <!-- In-Flight Queued -->
        <div class="card-surface p-4 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-[#666666] uppercase tracking-wider">In Queue</span>
                <span class="w-2 h-2 rounded-full bg-[#2563EB] animate-pulse"></span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-[#2563EB] font-mono">{{ number_format($queuedCount) }}</div>
                <div class="text-[10px] text-[#666666] mt-0.5">Background queue</div>
            </div>
        </div>

        <!-- Scheduled -->
        <div class="card-surface p-4 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-[#666666] uppercase tracking-wider">Scheduled</span>
                <span class="w-2 h-2 rounded-full bg-[#D97706]"></span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-[#D97706] font-mono">{{ number_format($scheduledCount) }}</div>
                <div class="text-[10px] text-[#666666] mt-0.5">Future dispatch</div>
            </div>
        </div>

        <!-- Failed -->
        <div class="card-surface p-4 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-[#666666] uppercase tracking-wider">Failed</span>
                <span class="w-2 h-2 rounded-full bg-[#DC2626]"></span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-[#DC2626] font-mono">{{ number_format($failedCount) }}</div>
                <div class="text-[10px] text-[#DC2626] font-semibold mt-0.5">Action required</div>
            </div>
        </div>

        <!-- Delivered -->
        <div class="card-surface p-4 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-[#666666] uppercase tracking-wider">Confirmed</span>
                <span class="w-2 h-2 rounded-full bg-[#B7FF00]"></span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-[#111111] font-mono">{{ number_format($deliveredCount) }}</div>
                <div class="text-[10px] text-[#666666] mt-0.5">Provider verified</div>
            </div>
        </div>
    </div>

    <!-- Active Mail Server Health & Queue System Status -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- SMTP Service Connection Status Card -->
        <div class="card-surface p-5 shadow-2xs space-y-4 lg:col-span-2">
            <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full {{ $activeSetting ? 'bg-[#079455]' : 'bg-[#D97706]' }}"></span>
                    <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Active SMTP Mail Provider</h3>
                </div>
                <a href="{{ route('mail.settings.index') }}" class="text-[11px] font-semibold text-[#111111] hover:underline">Configure Providers &rarr;</a>
            </div>

            @if($activeSetting)
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="p-3 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6]">
                        <span class="text-[10px] font-bold text-[#666666] uppercase">Provider Name</span>
                        <div class="text-sm font-bold text-[#111111] mt-0.5">{{ $activeSetting->display_name }}</div>
                        <span class="text-[11px] font-mono text-[#079455] font-semibold capitalize">{{ $activeSetting->provider_name }}</span>
                    </div>

                    <div class="p-3 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6]">
                        <span class="text-[10px] font-bold text-[#666666] uppercase">Host & Port</span>
                        <div class="text-sm font-mono font-bold text-[#111111] mt-0.5">{{ $activeSetting->mail_host }}</div>
                        <span class="text-[11px] text-[#666666] font-mono">Port {{ $activeSetting->mail_port }} ({{ strtoupper($activeSetting->mail_encryption) }})</span>
                    </div>

                    <div class="p-3 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6]">
                        <span class="text-[10px] font-bold text-[#666666] uppercase">Sender Identity</span>
                        <div class="text-xs font-semibold text-[#111111] truncate mt-0.5">{{ $activeSetting->from_address }}</div>
                        <span class="text-[11px] text-[#666666] truncate block">{{ $activeSetting->from_name }}</span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-1 text-xs text-[#666666]">
                    <div>
                        Connection Status: 
                        @if($activeSetting->last_test_status === 'success')
                            <span class="inline-flex items-center gap-1 text-[#027A48] font-bold ml-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                <span>Verified Online</span>
                            </span>
                            <span class="text-[11px] font-mono text-[#888888]">({{ $activeSetting->last_tested_at?->diffForHumans() }})</span>
                        @elseif($activeSetting->last_test_status === 'failed')
                            <span class="inline-flex items-center gap-1 text-[#DC2626] font-bold ml-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                <span>Connection Failed</span>
                            </span>
                        @else
                            <span class="text-[#888888] font-mono ml-1">Untested</span>
                        @endif
                    </div>

                    <button type="button" onclick="testActiveSmtp({{ $activeSetting->id }})" id="testSmtpBtn" class="btn-secondary px-3 py-1.5 rounded-lg text-xs font-semibold inline-flex items-center gap-1.5 self-start sm:self-auto min-h-[32px]">
                        <svg class="w-3.5 h-3.5 text-[#111111]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <span>Test SMTP Socket</span>
                    </button>
                </div>
            @else
                <div class="p-6 text-center border-2 border-dashed border-[#E6E6E6] rounded-xl space-y-3">
                    <p class="text-xs text-[#666666]">No active custom SMTP mail provider configured. The application will use system default (<code>{{ config('mail.default') }}</code>).</p>
                    <a href="{{ route('mail.settings.index') }}" class="btn-lime px-4 py-2 rounded-lg text-xs font-bold inline-flex items-center gap-1.5 shadow-2xs">
                        Configure SMTP Provider
                    </a>
                </div>
            @endif
        </div>

        <!-- Queue & Dispatch Health -->
        <div class="card-surface p-5 shadow-2xs space-y-4">
            <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#111111]"></span>
                    <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Queue System Status</h3>
                </div>
                <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-[#F7F7F6] text-[#344054] font-bold border border-[#E6E6E6]">{{ strtoupper(config('queue.default', 'database')) }}</span>
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6]">
                    <span class="text-xs font-semibold text-[#666666]">In-Flight Queue Jobs</span>
                    <span class="text-sm font-bold font-mono text-[#111111]">{{ $jobsInQueue }}</span>
                </div>

                <div class="flex items-center justify-between p-3 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6]">
                    <span class="text-xs font-semibold text-[#666666]">Permanent Failed Jobs</span>
                    <span class="text-sm font-bold font-mono {{ $failedQueueJobs > 0 ? 'text-[#DC2626]' : 'text-[#079455]' }}">{{ $failedQueueJobs }}</span>
                </div>

                <div class="p-3 bg-[#EFF8FF] border border-[#B2DDFF] rounded-xl text-xs text-[#175CD3] leading-relaxed">
                    Background queue worker is processing queued transactional invoices, receipts, and scheduled reminders asynchronously without UI delays.
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Email Activity Table -->
    <div class="card-surface p-5 shadow-2xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-[#E6E6E6] pb-3">
            <div>
                <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Recent Email Activity</h3>
                <p class="text-[11px] text-[#666666] mt-0.5">Most recent transactional and custom emails sent through the platform</p>
            </div>
            <a href="{{ route('mail.logs.index') }}" class="text-xs font-semibold text-[#111111] hover:underline inline-flex items-center gap-1">
                <span>View Complete Log ({{ number_format($totalSent + $failedCount + $queuedCount) }})</span>
                <span>&rarr;</span>
            </a>
        </div>

        @if($recentEmails->isEmpty())
            <div class="p-12 text-center">
                <div class="w-12 h-12 rounded-full bg-[#F2F4F7] text-[#666666] flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <h4 class="text-sm font-bold text-[#111111]">No email activity logged yet</h4>
                <p class="text-xs text-[#666666] mt-1 mb-4">Send a bill invoice or compose your first message to see real-time delivery logs.</p>
                <a href="{{ route('mail.compose') }}" class="btn-lime px-4 py-2 rounded-lg text-xs font-bold shadow-2xs inline-flex items-center gap-1.5 min-h-[40px]">
                    Compose First Email
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F7F7F6] border-b border-[#E6E6E6] text-[#666666] uppercase tracking-wider font-semibold">
                        <tr>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-4">Recipient</th>
                            <th class="py-3 px-4">Subject</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Related Doc</th>
                            <th class="py-3 px-4">Date / Time</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E6E6E6] text-[#111111]">
                        @foreach($recentEmails as $log)
                            <tr class="hover:bg-[#F7F7F6]/80 transition">
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $log->getStatusBadgeClass() }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="font-semibold text-[#111111]">{{ $log->recipient_email }}</div>
                                    @if($log->recipient_name)
                                        <span class="text-[11px] text-[#666666]">{{ $log->recipient_name }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 max-w-xs truncate" title="{{ $log->subject }}">
                                    <div class="truncate font-medium text-[#111111]">{{ $log->subject }}</div>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap capitalize">
                                    <span class="text-[11px] font-mono text-[#666666] bg-[#F2F4F7] px-2 py-0.5 rounded border border-[#E6E6E6]">
                                        {{ $log->category }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if($log->invoice)
                                        <a href="{{ route('invoices.show', $log->invoice) }}" class="font-mono text-[#111111] font-bold hover:underline">
                                            #{{ $log->invoice->invoice_number }}
                                        </a>
                                    @elseif($log->customer)
                                        <a href="{{ route('customers.show', $log->customer) }}" class="text-[#111111] hover:underline">
                                            {{ $log->customer->name }}
                                        </a>
                                    @else
                                        <span class="text-[#888888] font-mono">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-[#666666] font-mono text-[11px]">
                                    {{ $log->created_at->format('d M, h:i A') }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-right">
                                    <button type="button" onclick="viewLogModal({{ $log->id }})" class="p-1.5 text-[#666666] hover:text-[#111111] rounded hover:bg-[#F2F4F7]" title="Inspect Email Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Modal: Inspect Email Details -->
<div id="emailDetailModal" class="modal-backdrop fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl w-full max-w-3xl shadow-2xl border border-[#E6E6E6] flex flex-col max-h-[90vh] overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-[#E6E6E6] flex items-center justify-between bg-[#F7F7F6]">
            <div>
                <h3 class="text-sm font-bold text-[#111111]" id="modalSubject">Email Details</h3>
                <p class="text-[11px] text-[#666666] mt-0.5" id="modalMeta">Loading details...</p>
            </div>
            <button type="button" onclick="closeLogModal()" class="p-1.5 text-[#888888] hover:text-[#111111] rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="p-5 overflow-y-auto space-y-4 flex-1">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs bg-[#F7F7F6] p-3.5 rounded-xl border border-[#E6E6E6]">
                <div>
                    <span class="text-[10px] text-[#666666] uppercase font-bold block">Status</span>
                    <span id="modalStatusBadge" class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-semibold border"></span>
                </div>
                <div>
                    <span class="text-[10px] text-[#666666] uppercase font-bold block">Recipient</span>
                    <span id="modalRecipient" class="font-mono text-[11px] font-semibold text-[#111111] block truncate"></span>
                </div>
                <div>
                    <span class="text-[10px] text-[#666666] uppercase font-bold block">Mailer Server</span>
                    <span id="modalMailer" class="font-mono text-[11px] text-[#111111] block truncate"></span>
                </div>
                <div>
                    <span class="text-[10px] text-[#666666] uppercase font-bold block">Timestamp</span>
                    <span id="modalSentAt" class="font-mono text-[11px] text-[#111111] block truncate"></span>
                </div>
            </div>

            <!-- Error message banner if failed -->
            <div id="modalErrorContainer" class="hidden p-3 bg-[#FEF2F2] border border-[#FECACA] rounded-xl text-xs text-[#B91C1C]">
                <strong class="font-bold">Failure Trace:</strong> <span id="modalErrorText"></span>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#666666] uppercase tracking-wider mb-2">Message Body Preview</label>
                <div id="modalBodyHtml" class="p-4 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] text-xs max-h-96 overflow-y-auto"></div>
            </div>
        </div>
        <div class="p-4 border-t border-[#E6E6E6] bg-[#F7F7F6] flex justify-end">
            <button type="button" onclick="closeLogModal()" class="btn-secondary px-4 py-2 rounded-lg text-xs font-semibold">
                Close
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    async function testActiveSmtp(settingId) {
        const btn = document.getElementById('testSmtpBtn');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="inline-block w-3.5 h-3.5 border-2 border-black border-t-transparent rounded-full animate-spin"></span> Testing Socket...';

        try {
            const resp = await fetch('{{ route('mail.settings.test-connection') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ setting_id: settingId })
            });

            const data = await resp.json();
            if (data.success) {
                alert('SUCCESS: ' + data.message);
                window.location.reload();
            } else {
                alert('FAILURE: ' + data.message);
            }
        } catch (e) {
            alert('Request failed: ' + e.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }

    async function viewLogModal(logId) {
        try {
            const resp = await fetch(`/mail/logs/${logId}`);
            const data = await resp.json();

            document.getElementById('modalSubject').textContent = data.subject;
            document.getElementById('modalMeta').textContent = `Log #${data.id} • ${data.category.toUpperCase()} • Created ${data.created_at}`;
            document.getElementById('modalRecipient').textContent = data.recipient_email;
            document.getElementById('modalMailer').textContent = data.mailer_used || 'Standard SMTP';
            document.getElementById('modalSentAt').textContent = data.sent_at || data.created_at;

            const badge = document.getElementById('modalStatusBadge');
            badge.textContent = data.status.toUpperCase();
            badge.className = `inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-semibold border ${data.status_badge}`;

            const errContainer = document.getElementById('modalErrorContainer');
            if (data.error_message) {
                errContainer.classList.remove('hidden');
                document.getElementById('modalErrorText').textContent = data.error_message;
            } else {
                errContainer.classList.add('hidden');
            }

            document.getElementById('modalBodyHtml').innerHTML = data.body_html;
            document.getElementById('emailDetailModal').classList.remove('hidden');
        } catch (e) {
            alert('Unable to load email log details: ' + e.message);
        }
    }

    function closeLogModal() {
        document.getElementById('emailDetailModal').classList.add('hidden');
    }
</script>
@endpush
@endsection

