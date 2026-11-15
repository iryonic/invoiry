@extends('layouts.app')

@section('title', 'Compose Email')
@section('page-title', 'Compose Email')
@section('page-subtitle', 'Send transactional invoices, receipts, payment notices, or general business communication')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Navigation Tabs -->
    @include('mail.partials.nav-tabs')

    <form method="POST" action="{{ route('mail.send') }}" enctype="multipart/form-data" id="mailComposeForm" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Main Compose Form -->
            <div class="lg:col-span-2 space-y-4">
                <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-4">
                    <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#111111]"></span>
                            <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Message Details</h3>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="toggleCcBcc()" class="text-xs font-semibold text-[#666666] hover:text-[#111111]">
                                + CC / BCC
                            </button>
                        </div>
                    </div>

                    <!-- Template Selector -->
                    <div>
                        <label class="block text-xs font-bold text-[#111111] mb-1.5">Load from Template (Optional)</label>
                        <select id="templateSelector" name="template_id" onchange="onTemplateChange()" class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-1 focus:ring-black min-h-[44px]">
                            <option value="">-- Start with Blank Message --</option>
                            @foreach($templates as $tmpl)
                                <option value="{{ $tmpl->id }}" 
                                    data-subject="{{ $tmpl->subject }}" 
                                    data-category="{{ $tmpl->category }}"
                                    data-body="{{ htmlspecialchars($tmpl->body_html, ENT_QUOTES) }}"
                                    data-body-text="{{ htmlspecialchars($tmpl->body_text, ENT_QUOTES) }}"
                                    {{ request('template_id') == $tmpl->id ? 'selected' : '' }}>
                                    [{{ ucfirst($tmpl->category) }}] {{ $tmpl->name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="category" id="emailCategory" value="general">
                    </div>

                    <!-- Recipient Field & Customer Quick Picker -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-[#111111]">To: Recipients <span class="text-red-500">*</span></label>
                            @if($customers->isNotEmpty())
                                <select onchange="onCustomerSelect(this)" class="text-[11px] bg-transparent text-[#666666] border border-[#E6E6E6] rounded-md px-2 py-0.5 focus:outline-none">
                                    <option value="">Quick Pick Customer...</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->email }}" data-name="{{ $c->name }}" data-id="{{ $c->id }}">
                                            {{ $c->name }} ({{ $c->email }})
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <input type="text" name="recipient_email" id="recipientEmail" required
                            value="{{ old('recipient_email', request('recipient')) }}"
                            placeholder="customer@example.com (comma separated for multiple)"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-1 focus:ring-black min-h-[44px] font-mono">
                        <input type="hidden" name="recipient_name" id="recipientName" value="{{ old('recipient_name') }}">
                        <input type="hidden" name="customer_id" id="customerId" value="{{ request('customer_id') }}">
                    </div>

                    <!-- CC / BCC (Hidden by default, toggled via button) -->
                    <div id="ccBccContainer" class="grid grid-cols-1 sm:grid-cols-2 gap-3 {{ old('cc') || old('bcc') ? '' : 'hidden' }}">
                        <div>
                            <label class="block text-xs font-bold text-[#666666] mb-1">CC</label>
                            <input type="text" name="cc" value="{{ old('cc') }}" placeholder="manager@example.com"
                                class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs font-mono focus:outline-none focus:ring-1 focus:ring-black">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#666666] mb-1">BCC</label>
                            <input type="text" name="bcc" value="{{ old('bcc') }}" placeholder="records@example.com"
                                class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs font-mono focus:outline-none focus:ring-1 focus:ring-black">
                        </div>
                    </div>

                    <!-- Subject -->
                    <div>
                        <label class="block text-xs font-bold text-[#111111] mb-1.5">Subject <span class="text-red-500">*</span></label>
                        <input type="text" name="subject" id="emailSubject" required
                            value="{{ old('subject', request('subject')) }}"
                            placeholder="e.g. Invoice #INV-2026-0001 from {{ $tenant?->name ?? 'INVOIRY' }}"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:ring-1 focus:ring-black min-h-[44px]">
                    </div>

                    <!-- Body HTML -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-[#111111]">HTML Message Body <span class="text-red-500">*</span></label>
                            <span class="text-[11px] text-[#666666] font-mono">HTML & Placeholder markup supported</span>
                        </div>
                        <textarea name="body_html" id="emailBodyHtml" rows="12" required
                            class="w-full px-3.5 py-3 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono focus:outline-none focus:ring-1 focus:ring-black leading-relaxed"
                            placeholder="<p>Dear {{customer_name}},</p><p>Thank you for choosing {{business_name}}.</p>">{{ old('body_html') }}</textarea>
                    </div>

                    <!-- Plain Text Alternative Toggle -->
                    <div>
                        <details class="text-xs">
                            <summary class="font-semibold text-[#666666] cursor-pointer hover:text-black">
                                + Add Plain-Text Alternative (for basic email clients)
                            </summary>
                            <div class="mt-2">
                                <textarea name="body_text" id="emailBodyText" rows="4"
                                    class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs font-mono focus:outline-none focus:ring-1 focus:ring-black"
                                    placeholder="Optional plain text version. If left blank, it will be automatically derived from HTML.">{{ old('body_text') }}</textarea>
                            </div>
                        </details>
                    </div>

                    <!-- File Attachments -->
                    <div>
                        <label class="block text-xs font-bold text-[#111111] mb-1.5">Attachments</label>
                        <div class="p-4 border-2 border-dashed border-[#E6E6E6] rounded-xl bg-[#F7F7F6] text-center">
                            <input type="file" name="attachments[]" multiple id="attachmentInput" class="text-xs text-[#666666] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#111111] file:text-white hover:file:bg-black cursor-pointer">
                            <p class="text-[11px] text-[#888888] mt-2">Permitted: PDF, PNG, JPG, DOCX, XLSX, TXT, CSV (Max 10 MB per file)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Settings, Preview & Dynamic Placeholders -->
            <div class="space-y-4">
                <!-- Send Action & Scheduling Box -->
                <div class="card-surface p-5 shadow-2xs space-y-4">
                    <div class="flex items-center gap-2 border-b border-[#E6E6E6] pb-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#B7FF00]"></span>
                        <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Delivery Dispatch</h3>
                    </div>

                    <!-- Related Invoice Link (for auto-filling placeholders) -->
                    @if($invoices->isNotEmpty())
                        <div>
                            <label class="block text-xs font-bold text-[#111111] mb-1">Context Invoice (Optional)</label>
                            <select name="invoice_id" id="contextInvoiceId" onchange="onInvoiceContextChange()" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs font-mono">
                                <option value="">-- No Related Invoice --</option>
                                @foreach($invoices as $inv)
                                    <option value="{{ $inv->id }}" {{ request('invoice_id') == $inv->id ? 'selected' : '' }}>
                                        #{{ $inv->invoice_number }} (₹{{ number_format($inv->total_amount, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Scheduling Options -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-[#111111]">Dispatch Timing</label>
                        <div class="space-y-1.5 text-xs">
                            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg hover:bg-[#F7F7F6] border border-transparent hover:border-[#E6E6E6]">
                                <input type="radio" name="send_action" value="send_now" checked onchange="toggleScheduleInput(false)" class="text-black focus:ring-black">
                                <span class="font-semibold text-[#111111]">Send Immediately</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg hover:bg-[#F7F7F6] border border-transparent hover:border-[#E6E6E6]">
                                <input type="radio" name="send_action" value="schedule" onchange="toggleScheduleInput(true)" class="text-black focus:ring-black">
                                <span class="font-semibold text-[#111111]">Schedule for Later</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg hover:bg-[#F7F7F6] border border-transparent hover:border-[#E6E6E6]">
                                <input type="radio" name="send_action" value="draft" onchange="toggleScheduleInput(false)" class="text-black focus:ring-black">
                                <span class="font-semibold text-[#666666]">Save as Draft</span>
                            </label>
                        </div>

                        <!-- Schedule Datetime input -->
                        <div id="scheduleDatetimeBox" class="hidden mt-2 p-3 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] space-y-1">
                            <label class="block text-[11px] font-bold text-[#666666]">Dispatch Date & Time</label>
                            <input type="datetime-local" name="scheduled_at" id="scheduledAtInput" min="{{ now()->format('Y-m-d\TH:i') }}" class="w-full px-3 py-1.5 bg-white border border-[#E6E6E6] rounded-lg text-xs font-mono">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-3 border-t border-[#E6E6E6] space-y-2">
                        <button type="submit" id="submitSendBtn" class="w-full py-3 btn-lime rounded-lg font-bold text-xs shadow-2xs flex items-center justify-center gap-2 min-h-[44px]">
                            <svg class="w-4 h-4 text-[#101010]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            <span id="submitBtnText">Dispatch Email</span>
                        </button>

                        <button type="button" onclick="previewComposedEmail()" class="w-full py-2.5 btn-secondary rounded-lg font-semibold text-xs flex items-center justify-center gap-1.5 min-h-[40px]">
                            <svg class="w-4 h-4 text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <span>Preview Rendered Email</span>
                        </button>
                    </div>
                </div>

                <!-- Dynamic Placeholders Helper Card -->
                <div class="card-surface p-5 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-2">
                        <h4 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Dynamic Placeholders</h4>
                        <span class="text-[10px] text-[#666666]">Click to copy</span>
                    </div>
                    <p class="text-[11px] text-[#666666]">Insert placeholders into subject or body; they will be substituted automatically at send time:</p>
                    <div class="flex flex-wrap gap-1.5 max-h-56 overflow-y-auto pr-1">
                        @foreach($standardPlaceholders as $key => $desc)
                            <button type="button" onclick="insertPlaceholder('{{ $key }}')" title="{{ $desc }}" class="px-2 py-1 bg-[#F7F7F6] hover:bg-[#111111] hover:text-[#B7FF00] rounded text-[11px] font-mono text-[#333333] border border-[#E6E6E6] transition">
                                &#123;&#123;{{ $key }}&#125;&#125;
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal: Live Email Preview -->
<div id="composePreviewModal" class="modal-backdrop fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl border border-[#E6E6E6] flex flex-col max-h-[90vh] overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-[#E6E6E6] flex items-center justify-between bg-[#F7F7F6]">
            <div>
                <h3 class="text-sm font-bold text-[#111111]">Live Email Preview</h3>
                <p class="text-[11px] text-[#666666]" id="previewSubjectText">Subject: ...</p>
            </div>
            <button type="button" onclick="closeComposePreview()" class="p-1.5 text-[#888888] hover:text-[#111111] rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="p-5 overflow-y-auto flex-1">
            <div id="previewContentContainer" class="p-4 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] text-xs"></div>
        </div>
        <div class="p-4 border-t border-[#E6E6E6] bg-[#F7F7F6] flex justify-end gap-2">
            <button type="button" onclick="closeComposePreview()" class="btn-secondary px-4 py-2 rounded-lg text-xs font-semibold">
                Close
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleCcBcc() {
        const box = document.getElementById('ccBccContainer');
        box.classList.toggle('hidden');
    }

    function toggleScheduleInput(isScheduled) {
        const box = document.getElementById('scheduleDatetimeBox');
        const btnText = document.getElementById('submitBtnText');
        const radios = document.getElementsByName('send_action');
        let selected = 'send_now';
        for (const r of radios) {
            if (r.checked) selected = r.value;
        }

        if (selected === 'schedule') {
            box.classList.remove('hidden');
            btnText.textContent = 'Schedule Email';
        } else if (selected === 'draft') {
            box.classList.add('hidden');
            btnText.textContent = 'Save as Draft';
        } else {
            box.classList.add('hidden');
            btnText.textContent = 'Dispatch Email';
        }
    }

    function onCustomerSelect(select) {
        const opt = select.options[select.selectedIndex];
        if (!opt.value) return;

        document.getElementById('recipientEmail').value = opt.value;
        document.getElementById('recipientName').value = opt.getAttribute('data-name') || '';
        document.getElementById('customerId').value = opt.getAttribute('data-id') || '';
    }

    function onTemplateChange() {
        const select = document.getElementById('templateSelector');
        const opt = select.options[select.selectedIndex];
        if (!opt.value) return;

        const subject = opt.getAttribute('data-subject');
        const body = opt.getAttribute('data-body');
        const category = opt.getAttribute('data-category');

        if (subject) document.getElementById('emailSubject').value = subject;
        if (body) document.getElementById('emailBodyHtml').value = body;
        if (category) document.getElementById('emailCategory').value = category;
    }

    function insertPlaceholder(placeholderKey) {
        const tag = `{{${placeholderKey}}}`;
        const textarea = document.getElementById('emailBodyHtml');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;

        textarea.value = text.substring(0, start) + tag + text.substring(end);
        textarea.selectionStart = textarea.selectionEnd = start + tag.length;
        textarea.focus();
    }

    async function previewComposedEmail() {
        const subject = document.getElementById('emailSubject').value;
        const bodyHtml = document.getElementById('emailBodyHtml').value;
        const customerId = document.getElementById('customerId').value;
        const invoiceSelect = document.getElementById('contextInvoiceId');
        const invoiceId = invoiceSelect ? invoiceSelect.value : '';

        try {
            const resp = await fetch('{{ route('mail.preview') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    subject: subject,
                    body_html: bodyHtml,
                    customer_id: customerId,
                    invoice_id: invoiceId
                })
            });

            const data = await resp.json();
            document.getElementById('previewSubjectText').textContent = 'Subject: ' + data.subject;
            document.getElementById('previewContentContainer').innerHTML = data.body_html;
            document.getElementById('composePreviewModal').classList.remove('hidden');
        } catch (e) {
            alert('Preview error: ' + e.message);
        }
    }

    function closeComposePreview() {
        document.getElementById('composePreviewModal').classList.add('hidden');
    }

    // Auto-trigger template load if preselected via URL
    document.addEventListener('DOMContentLoaded', () => {
        const select = document.getElementById('templateSelector');
        if (select && select.value) {
            onTemplateChange();
        }
    });
</script>
@endpush
@endsection

