@extends('layouts.app')

@section('title', 'Mail Delivery & SMTP Settings — Invoiry')

@section('content')
<div class="space-y-6">
    {{-- Header Banner --}}
    <div class="card-surface p-6 sm:p-7 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-[#B7FF00]/20 text-[#050505] border border-[#B7FF00]">
                    Enterprise Transport
                </span>
                <span class="text-xs font-mono text-[#666666]">Encrypted AES-256 Credentials</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-[#050505] tracking-tight">Mail Server & SMTP Configuration</h1>
            <p class="text-xs text-[#666666] mt-0.5">Configure authenticated SMTP relays (Gmail, Google Workspace, Hostinger, Outlook, or Custom SMTP).</p>
        </div>

        <div class="flex items-center gap-2.5">
            <button type="button" onclick="document.getElementById('newProviderCard').scrollIntoView({ behavior: 'smooth' })" class="px-4 py-2 rounded-xl text-xs font-bold bg-[#050505] text-[#B7FF00] hover:bg-black transition-all shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>Add SMTP Provider</span>
            </button>
        </div>
    </div>

    {{-- Subnavigation Tabs --}}
    @include('mail.partials.nav-tabs')

    {{-- Active Provider Status Card --}}
    <div class="card-surface p-5 sm:p-6 border-l-4 border-l-[#B7FF00] space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#050505] text-[#B7FF00] flex items-center justify-center font-bold text-sm shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 0 1-3-3m3 3a3 3 0 1 0 0 6h13.5a3 3 0 1 0 0-6m-16.5-3a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3m-19.5 0a4.5 4.5 0 0 1 .9-2.7L5.75 5.1a4.5 4.5 0 0 1 3.6-1.85h5.3a4.5 4.5 0 0 1 3.6 1.85l2.1 3.45c.58.95.9 2.05.9 3.15"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-[#666666]">Active Default Transport</span>
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Online
                        </span>
                    </div>
                    <div class="text-base font-black text-[#050505] mt-0.5">
                        {{ $activeSetting ? $activeSetting->display_name : 'System Fallback (PHP Mail / Localhost)' }}
                    </div>
                </div>
            </div>

            @if($activeSetting)
                <div class="flex items-center gap-2">
                    <button type="button" onclick="testConnection({{ $activeSetting->id }})" class="px-3 py-1.5 rounded-lg text-xs font-bold border border-[#E6E6E6] bg-white text-[#050505] hover:bg-[#F7F7F6] flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#666666]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                        <span>Test Socket</span>
                    </button>
                    <button type="button" onclick="openTestEmailModal({{ $activeSetting->id }}, '{{ addslashes($activeSetting->display_name) }}')" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-[#050505] text-[#B7FF00] hover:bg-black flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                        <span>Send Test Email</span>
                    </button>
                </div>
            @endif
        </div>

        @if($activeSetting)
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-3 border-t border-[#E6E6E6] text-xs">
                <div>
                    <span class="text-[#888888] block text-[10px] uppercase font-bold">Host & Port</span>
                    <span class="font-mono font-bold text-[#050505]">{{ $activeSetting->mail_host }}:{{ $activeSetting->mail_port }}</span>
                </div>
                <div>
                    <span class="text-[#888888] block text-[10px] uppercase font-bold">Encryption</span>
                    <span class="font-mono font-bold text-[#050505] uppercase">{{ $activeSetting->mail_encryption }}</span>
                </div>
                <div>
                    <span class="text-[#888888] block text-[10px] uppercase font-bold">Sender Address</span>
                    <span class="font-mono font-semibold text-[#050505] truncate block">{{ $activeSetting->from_address }}</span>
                </div>
                <div>
                    <span class="text-[#888888] block text-[10px] uppercase font-bold">Sender Name</span>
                    <span class="font-semibold text-[#050505] truncate block">{{ $activeSetting->from_name }}</span>
                </div>
            </div>
        @else
            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-800">
                No active SMTP server is configured. Please configure an SMTP provider below to ensure reliable delivery of bills, payment receipts, and customer notices.
            </div>
        @endif
    </div>

    {{-- Grid of Configured Providers --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold uppercase tracking-wider text-[#050505]">Configured Mail Providers ({{ $settings->count() }})</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($settings as $st)
                <div class="card-surface p-5 space-y-4 relative flex flex-col justify-between {{ $st->is_default ? 'ring-2 ring-[#050505]' : '' }}">
                    <div>
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-bold text-[#050505]">{{ $st->display_name }}</h3>
                                    @if($st->is_default)
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-[#B7FF00]/30 text-[#050505] border border-[#B7FF00]">Default</span>
                                    @endif
                                </div>
                                <p class="text-[11px] font-mono text-[#888888] mt-0.5 uppercase">{{ $st->provider_name }} &bull; {{ $st->mail_encryption }}</p>
                            </div>

                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $st->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-500' }}">
                                {{ $st->is_active ? 'Active' : 'Disabled' }}
                            </span>
                        </div>

                        <div class="mt-4 p-3 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-[#888888]">Host:</span>
                                <span class="font-mono font-bold text-[#050505]">{{ $st->mail_host }}:{{ $st->mail_port }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[#888888]">Username:</span>
                                <span class="font-mono text-[#050505]">{{ $st->mail_username ?: 'None (Open Relay)' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[#888888]">Password:</span>
                                <span class="font-mono text-[#666666]">{{ $st->getMaskedPassword() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[#888888]">Sender (From):</span>
                                <span class="font-mono text-[#050505] truncate max-w-[200px]">{{ $st->from_name }} &lt;{{ $st->from_address }}&gt;</span>
                            </div>
                        </div>
                    </div>

                    {{-- Actions Bar --}}
                    <div class="pt-3 border-t border-[#E6E6E6] flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5">
                            <button type="button" onclick="testConnection({{ $st->id }})" class="px-2.5 py-1 text-[11px] font-bold border border-[#E6E6E6] bg-white text-[#050505] hover:bg-[#F7F7F6] rounded-lg">
                                Test Socket
                            </button>
                            <button type="button" onclick="openTestEmailModal({{ $st->id }}, '{{ addslashes($st->display_name) }}')" class="px-2.5 py-1 text-[11px] font-bold bg-[#050505] text-[#B7FF00] hover:bg-black rounded-lg">
                                Send Test
                            </button>
                        </div>

                        <div class="flex items-center gap-1">
                            @if(!$st->is_default)
                                <form method="POST" action="{{ route('mail.settings.default', $st) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 text-[11px] font-semibold text-[#050505] hover:bg-[#F7F7F6] rounded-lg">
                                        Make Default
                                    </button>
                                </form>
                            @endif

                            <button type="button" onclick="editProvider({{ json_encode($st) }})" class="p-1.5 text-[#666666] hover:text-[#050505] hover:bg-white rounded-lg" title="Edit Configuration">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                            </button>

                            <form method="POST" action="{{ route('mail.settings.destroy', $st) }}" onsubmit="return confirm('Remove configuration \'{{ addslashes($st->display_name) }}\'?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="md:col-span-2 card-surface p-8 text-center text-[#666666]">
                    <p class="text-xs font-semibold text-[#050505]">No SMTP providers configured yet.</p>
                    <p class="text-[11px] text-[#888888] mt-1">Use the quick preset configuration form below to set up your mail server.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Add New / Edit Provider Form --}}
    <div id="newProviderCard" class="card-surface p-6 sm:p-7 space-y-6">
        <div class="border-b border-[#E6E6E6] pb-4">
            <h2 id="formTitle" class="text-base font-black text-[#050505] tracking-tight">Configure New SMTP Provider</h2>
            <p class="text-xs text-[#666666] mt-0.5">Select a provider preset for auto-fill or enter custom SMTP credentials.</p>
        </div>

        {{-- Provider Preset Buttons --}}
        <div>
            <label class="block text-xs font-bold text-[#050505] mb-2">Provider Quick Preset</label>
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                @foreach($presets as $key => $preset)
                    <button type="button" onclick="applyPreset('{{ $key }}', '{{ $preset['name'] }}', '{{ $preset['host'] }}', {{ $preset['port'] }}, '{{ $preset['encryption'] }}')" class="preset-btn p-3 rounded-xl border border-[#E6E6E6] bg-[#F7F7F6] hover:border-[#050505] hover:bg-white text-left transition-all group">
                        <div class="text-xs font-bold text-[#050505]">{{ $preset['name'] }}</div>
                        <div class="text-[10px] font-mono text-[#888888] mt-0.5 truncate">{{ $preset['host'] ?: 'Custom IP/Domain' }}</div>
                    </button>
                @endforeach
            </div>
        </div>

        <form method="POST" action="{{ route('mail.settings.store') }}" id="smtpForm" class="space-y-5">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="provider_name" id="providerNameInput" value="custom">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-[#050505] mb-1.5">Profile Label <span class="text-red-500">*</span></label>
                    <input type="text" name="display_name" id="displayNameInput" placeholder="e.g. Hostinger Official Webmail" required class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs text-[#050505] focus:outline-none focus:border-[#050505]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#050505] mb-1.5">SMTP Host <span class="text-red-500">*</span></label>
                    <input type="text" name="mail_host" id="hostInput" placeholder="smtp.domain.com" required class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs font-mono text-[#050505] focus:outline-none focus:border-[#050505]">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-bold text-[#050505] mb-1.5">Port <span class="text-red-500">*</span></label>
                        <input type="number" name="mail_port" id="portInput" value="587" required class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs font-mono text-[#050505] focus:outline-none focus:border-[#050505]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#050505] mb-1.5">Security</label>
                        <select name="mail_encryption" id="encryptionInput" class="w-full px-3 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs font-mono text-[#050505] focus:outline-none">
                            <option value="tls">TLS (587)</option>
                            <option value="ssl">SSL (465)</option>
                            <option value="none">None (25)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-[#050505] mb-1.5">SMTP Username / Email</label>
                    <input type="text" name="mail_username" id="usernameInput" placeholder="billing@yourdomain.com" class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs font-mono text-[#050505] focus:outline-none focus:border-[#050505]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#050505] mb-1.5">SMTP Password / App Secret</label>
                    <input type="password" name="mail_password" id="passwordInput" placeholder="••••••••••••••••" class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs font-mono text-[#050505] focus:outline-none focus:border-[#050505]">
                    <p class="text-[10px] text-[#888888] mt-1">Encrypted on save via AES-256. Leave blank on edit to preserve existing password.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-[#050505] mb-1.5">Sender Email (From) <span class="text-red-500">*</span></label>
                    <input type="email" name="from_address" id="fromAddressInput" placeholder="billing@yourdomain.com" required class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs font-mono text-[#050505] focus:outline-none focus:border-[#050505]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#050505] mb-1.5">Sender Name (From) <span class="text-red-500">*</span></label>
                    <input type="text" name="from_name" id="fromNameInput" placeholder="Apex Telecommunications" required class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs text-[#050505] focus:outline-none focus:border-[#050505]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#050505] mb-1.5">Reply-To Address (Optional)</label>
                    <input type="email" name="reply_to_address" id="replyToInput" placeholder="support@yourdomain.com" class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs font-mono text-[#050505] focus:outline-none focus:border-[#050505]">
                </div>
            </div>

            <div class="flex items-center gap-6 pt-2">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" id="isActiveInput" value="1" checked class="w-4 h-4 rounded border-[#E6E6E6] text-[#050505] focus:ring-0">
                    <span class="text-xs font-semibold text-[#050505]">Active Configuration</span>
                </label>

                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_default" id="isDefaultInput" value="1" class="w-4 h-4 rounded border-[#E6E6E6] text-[#050505] focus:ring-0">
                    <span class="text-xs font-semibold text-[#050505]">Set as Default Outgoing Mailer</span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E6E6E6]">
                <button type="button" onclick="resetForm()" class="px-4 py-2.5 rounded-xl text-xs font-bold border border-[#E6E6E6] bg-white text-[#666666] hover:text-[#050505] hover:bg-[#F7F7F6]">
                    Reset
                </button>
                <button type="submit" id="saveBtn" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-[#050505] text-[#B7FF00] hover:bg-black transition-all shadow-sm">
                    Save Configuration
                </button>
            </div>
        </form>
    </div>

    {{-- Deliverability & SPF/DKIM/DMARC Guidance Card --}}
    <div class="card-surface p-6 space-y-4">
        <div class="flex items-center gap-2 border-b border-[#E6E6E6] pb-3">
            <span class="w-2.5 h-2.5 rounded-full bg-[#B7FF00]"></span>
            <h3 class="text-xs font-bold uppercase tracking-wider text-[#050505]">Domain Reputation & Deliverability Best Practices</h3>
        </div>

        <p class="text-xs text-[#666666] leading-relaxed">
            To ensure invoices, receipts, and OTPs consistently land in customer Inboxes rather than Junk/Spam folders, configure the following DNS records on your domain registrar (GoDaddy, Cloudflare, Namecheap, Hostinger):
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs font-mono">
            <div class="p-3.5 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] space-y-1">
                <div class="font-bold text-[#050505] flex items-center justify-between">
                    <span>SPF (Sender Policy)</span>
                    <span class="text-[9px] uppercase font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">TXT Record</span>
                </div>
                <p class="text-[11px] text-[#666666]">Host: <code>@</code></p>
                <code class="text-[10px] block p-2 bg-white rounded border border-[#E6E6E6] text-[#050505] break-all">
                    v=spf1 include:_spf.google.com ~all
                </code>
            </div>

            <div class="p-3.5 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] space-y-1">
                <div class="font-bold text-[#050505] flex items-center justify-between">
                    <span>DKIM (Cryptographic Sign)</span>
                    <span class="text-[9px] uppercase font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">TXT Record</span>
                </div>
                <p class="text-[11px] text-[#666666]">Host: <code>google._domainkey</code></p>
                <code class="text-[10px] block p-2 bg-white rounded border border-[#E6E6E6] text-[#050505] break-all">
                    v=DKIM1; k=rsa; p=MIIBIjANBgkqhkiG9w0...
                </code>
            </div>

            <div class="p-3.5 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] space-y-1">
                <div class="font-bold text-[#050505] flex items-center justify-between">
                    <span>DMARC (Policy & Reports)</span>
                    <span class="text-[9px] uppercase font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">TXT Record</span>
                </div>
                <p class="text-[11px] text-[#666666]">Host: <code>_dmarc</code></p>
                <code class="text-[10px] block p-2 bg-white rounded border border-[#E6E6E6] text-[#050505] break-all">
                    v=DMARC1; p=quarantine; pct=100;
                </code>
            </div>
        </div>
    </div>
</div>

{{-- Send Test Email Modal --}}
<div id="testEmailModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="card-surface max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
            <h3 class="text-sm font-bold text-[#050505]">Send Test Email</h3>
            <button type="button" onclick="closeTestEmailModal()" class="text-[#666666] hover:text-[#050505]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <p class="text-xs text-[#666666]">
            Transmit a real test email through <span id="modalProviderName" class="font-bold text-[#050505]"></span> to verify authentication, TLS handshake, and sender reputation.
        </p>

        <form id="testEmailForm" onsubmit="submitTestEmail(event)" class="space-y-4">
            <input type="hidden" id="modalSettingId" value="">
            <div>
                <label class="block text-xs font-bold text-[#050505] mb-1.5">Recipient Email Address</label>
                <input type="email" id="modalRecipientEmail" value="{{ auth()->user()->email }}" required class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs font-mono text-[#050505] focus:outline-none focus:border-[#050505]">
            </div>

            <div id="testResultBox" class="hidden p-3 rounded-xl text-xs font-mono"></div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeTestEmailModal()" class="px-4 py-2 text-xs font-bold text-[#666666] hover:text-[#050505]">
                    Close
                </button>
                <button type="submit" id="testEmailSubmitBtn" class="px-4 py-2 bg-[#050505] text-[#B7FF00] text-xs font-bold rounded-xl hover:bg-black">
                    Send Test Now
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function applyPreset(provider, name, host, port, encryption) {
    document.getElementById('providerNameInput').value = provider;
    document.getElementById('displayNameInput').value = name;
    document.getElementById('hostInput').value = host;
    document.getElementById('portInput').value = port;
    document.getElementById('encryptionInput').value = encryption;
}

function testConnection(settingId) {
    const btn = event?.currentTarget;
    if (btn) btn.disabled = true;

    fetch('{{ route("mail.settings.test-connection") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ setting_id: settingId })
    })
    .then(res => res.json())
    .then(data => {
        if (btn) btn.disabled = false;
        if (data.connected) {
            alert(`✅ SMTP Socket Success!\nConnected in ${data.latency_ms}ms to ${data.banner || 'host'}.`);
        } else {
            alert(`❌ SMTP Connection Failed:\n${data.error}`);
        }
    })
    .catch(err => {
        if (btn) btn.disabled = false;
        alert('Socket check error: ' + err.message);
    });
}

function openTestEmailModal(settingId, name) {
    document.getElementById('modalSettingId').value = settingId;
    document.getElementById('modalProviderName').textContent = name;
    document.getElementById('testResultBox').classList.add('hidden');
    document.getElementById('testEmailModal').classList.remove('hidden');
}

function closeTestEmailModal() {
    document.getElementById('testEmailModal').classList.add('hidden');
}

function submitTestEmail(e) {
    e.preventDefault();
    const btn = document.getElementById('testEmailSubmitBtn');
    const resultBox = document.getElementById('testResultBox');
    btn.disabled = true;
    btn.textContent = 'Sending...';
    resultBox.classList.add('hidden');

    fetch('{{ route("mail.settings.send-test-email") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            setting_id: document.getElementById('modalSettingId').value,
            recipient_email: document.getElementById('modalRecipientEmail').value
        })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.textContent = 'Send Test Now';
        resultBox.classList.remove('hidden');
        if (data.success) {
            resultBox.className = 'p-3 rounded-xl text-xs font-mono bg-emerald-50 border border-emerald-200 text-emerald-800';
            resultBox.textContent = `✅ ${data.message} (${data.latency_ms}ms)`;
        } else {
            resultBox.className = 'p-3 rounded-xl text-xs font-mono bg-red-50 border border-red-200 text-red-800';
            resultBox.textContent = `❌ ${data.message}`;
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.textContent = 'Send Test Now';
        resultBox.classList.remove('hidden');
        resultBox.className = 'p-3 rounded-xl text-xs font-mono bg-red-50 border border-red-200 text-red-800';
        resultBox.textContent = 'Error: ' + err.message;
    });
}

function editProvider(st) {
    document.getElementById('newProviderCard').scrollIntoView({ behavior: 'smooth' });
    document.getElementById('formTitle').textContent = `Edit SMTP: ${st.display_name}`;
    document.getElementById('smtpForm').action = `{{ url('mail/settings') }}/${st.id}`;
    document.getElementById('formMethod').value = 'PUT';

    document.getElementById('providerNameInput').value = st.provider_name;
    document.getElementById('displayNameInput').value = st.display_name;
    document.getElementById('hostInput').value = st.mail_host;
    document.getElementById('portInput').value = st.mail_port;
    document.getElementById('encryptionInput').value = st.mail_encryption;
    document.getElementById('usernameInput').value = st.mail_username || '';
    document.getElementById('fromAddressInput').value = st.from_address;
    document.getElementById('fromNameInput').value = st.from_name;
    document.getElementById('replyToInput').value = st.reply_to_address || '';
    document.getElementById('isActiveInput').checked = !!st.is_active;
    document.getElementById('isDefaultInput').checked = !!st.is_default;
    document.getElementById('saveBtn').textContent = 'Update Configuration';
}

function resetForm() {
    document.getElementById('formTitle').textContent = 'Configure New SMTP Provider';
    document.getElementById('smtpForm').action = '{{ route("mail.settings.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('smtpForm').reset();
    document.getElementById('saveBtn').textContent = 'Save Configuration';
}
</script>
@endsection

