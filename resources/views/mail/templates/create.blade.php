@extends('layouts.app')

@section('title', 'Create Email Template — Invoiry')

@section('content')
<div class="space-y-6  mx-auto">
    {{-- Header Banner --}}
    <div class="card-surface p-6 sm:p-7 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <a href="{{ route('mail.templates.index') }}" class="text-xs font-semibold text-[#666666] hover:text-[#050505] flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                    <span>Back to Templates</span>
                </a>
                <span class="text-xs text-[#CCCCCC]">/</span>
                <span class="text-xs font-mono text-[#666666]">Builder</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-[#050505] tracking-tight">Create Email Template</h1>
            <p class="text-xs text-[#666666] mt-0.5">Author a responsive, branded HTML email template with dynamic placeholder tags.</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="previewDraft()" class="px-3.5 py-2 rounded-xl text-xs font-bold border border-[#E6E6E6] bg-white text-[#050505] hover:bg-[#F7F7F6] transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#666666]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><circle cx="12" cy="12" r="3"/></svg>
                <span>Live Preview</span>
            </button>
        </div>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('mail.templates.store') }}" id="templateForm" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Column (Form Fields) --}}
            <div class="lg:col-span-2 space-y-5">
                <div class="card-surface p-5 sm:p-6 space-y-4">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-[#666666] border-b border-[#E6E6E6] pb-2">
                        Basic Identity
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#050505] mb-1.5">Template Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="templateName" value="{{ old('name') }}" placeholder="e.g. Tax Invoice with QR" required class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs text-[#050505] focus:outline-none focus:border-[#050505]">
                            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#050505] mb-1.5">System Code (Slug) <span class="text-red-500">*</span></label>
                            <input type="text" name="code" id="templateCode" value="{{ old('code') }}" placeholder="e.g. invoice_tax_qr" required class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs font-mono text-[#050505] focus:outline-none focus:border-[#050505]">
                            @error('code') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#050505] mb-1.5">Category <span class="text-red-500">*</span></label>
                            <select name="category" required class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs text-[#050505] focus:outline-none focus:border-[#050505]">
                                <option value="invoices" {{ old('category') === 'invoices' ? 'selected' : '' }}>Invoicing & Bills</option>
                                <option value="payments" {{ old('category') === 'payments' ? 'selected' : '' }}>Payments & Receipts</option>
                                <option value="reminders" {{ old('category') === 'reminders' ? 'selected' : '' }}>Reminders & Khata</option>
                                <option value="customers" {{ old('category') === 'customers' ? 'selected' : '' }}>Customer Care & Welcome</option>
                                <option value="system" {{ old('category') === 'system' ? 'selected' : '' }}>System & Security</option>
                                <option value="general" {{ old('category') === 'general' ? 'selected' : '' }}>General Business Notice</option>
                            </select>
                            @error('category') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center gap-6 pt-5">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 rounded border-[#E6E6E6] text-[#050505] focus:ring-0">
                                <span class="text-xs font-semibold text-[#050505]">Active</span>
                            </label>

                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }} class="w-4 h-4 rounded border-[#E6E6E6] text-[#050505] focus:ring-0">
                                <span class="text-xs font-semibold text-[#050505]">Set as Default for Category</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Subject Line --}}
                <div class="card-surface p-5 sm:p-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-[#050505]">Email Subject Line <span class="text-red-500">*</span></label>
                        <span class="text-[11px] text-[#888888]">Supports placeholders</span>
                    </div>
                    <input type="text" name="subject" id="subjectInput" value="{{ old('subject') }}" placeholder="e.g. Tax Invoice {{ '{{' }}invoice_number{{ '}}' }} from {{ '{{' }}business_name{{ '}}' }}" required class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs font-mono text-[#050505] focus:outline-none focus:border-[#050505]">
                    @error('subject') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- HTML Body Editor --}}
                <div class="card-surface p-5 sm:p-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="block text-xs font-bold text-[#050505]">HTML Template Content <span class="text-red-500">*</span></label>
                            <p class="text-[11px] text-[#888888]">Use full HTML structure or table layout with inline styles for high email-client compatibility.</p>
                        </div>
                        <button type="button" onclick="loadStarterBoilerplate()" class="text-[11px] font-bold text-[#050505] underline decoration-[#B7FF00] decoration-2">
                            Load Clean Starter HTML
                        </button>
                    </div>

                    <textarea name="body_html" id="bodyHtml" rows="18" required placeholder="<!DOCTYPE html><html><body style='font-family: sans-serif; background-color: #f7f7f6;'>...</body></html>" class="w-full p-3.5 bg-[#050505] text-[#F7F7F6] font-mono text-xs rounded-xl border border-gray-800 focus:outline-none focus:ring-1 focus:ring-[#B7FF00] leading-relaxed">{{ old('body_html') }}</textarea>
                    @error('body_html') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Plain-Text Alternative --}}
                <div class="card-surface p-5 sm:p-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-[#050505]">Plain-Text Fallback (Optional)</label>
                        <button type="button" onclick="generatePlainTextFromHtml()" class="text-[11px] font-bold text-[#666666] hover:text-[#050505]">
                            Auto-Generate from HTML
                        </button>
                    </div>
                    <textarea name="body_text" id="bodyText" rows="6" placeholder="Plain text version delivered to legacy or text-only mail clients..." class="w-full p-3.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs font-mono text-[#050505] focus:outline-none focus:border-[#050505]">{{ old('body_text') }}</textarea>
                    @error('body_text') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Sidebar Column (Placeholders & Tips) --}}
            <div class="space-y-5">
                {{-- Dynamic Placeholder Picker --}}
                <div class="card-surface p-5 space-y-4 sticky top-6">
                    <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-2">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-[#050505]">Dynamic Tags</h3>
                        <span class="text-[10px] font-mono text-[#888888]">Click tag to copy/insert</span>
                    </div>

                    <p class="text-[11px] text-[#666666]">
                        These tags are automatically replaced with runtime values when delivering emails.
                    </p>

                    <div class="space-y-2 max-h-[500px] overflow-y-auto pr-1">
                        @foreach($standardPlaceholders as $tag => $desc)
                            <div class="p-2 bg-[#F7F7F6] rounded-lg border border-[#E6E6E6] hover:border-[#050505] transition-colors group cursor-pointer" onclick="insertPlaceholder('{{ '{{' }}{{ $tag }}{{ '}}' }}')">
                                <div class="flex items-center justify-between">
                                    <code class="text-[11px] font-mono font-bold text-[#050505] group-hover:text-black">
                                        {{ '{{' }}{{ $tag }}{{ '}}' }}
                                    </code>
                                    <span class="text-[9px] uppercase font-bold text-[#888888] group-hover:text-[#050505]">+ Insert</span>
                                </div>
                                <p class="text-[10px] text-[#666666] mt-0.5">{{ $desc }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-[11px] text-amber-800 space-y-1">
                        <div class="font-bold flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                            <span>Email Compatibility Tip</span>
                        </div>
                        <p>Keep CSS inlined inside <code>style=""</code> attributes. Avoid modern CSS flexbox and grid; use table cells (<code>&lt;td&gt;</code>) for layout across Outlook and Gmail.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E6E6E6]">
            <a href="{{ route('mail.templates.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold border border-[#E6E6E6] bg-white text-[#666666] hover:text-[#050505] hover:bg-[#F7F7F6] transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-[#050505] text-[#B7FF00] hover:bg-black transition-all shadow-sm">
                Save Template
            </button>
        </div>
    </form>
</div>

{{-- Live Preview Modal --}}
<div id="previewModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="card-surface max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl">
        <div class="p-4 border-b border-[#E6E6E6] flex items-center justify-between bg-[#F7F7F6]">
            <div>
                <h3 class="text-sm font-bold text-[#050505]">Draft Template Preview</h3>
                <p id="previewSubjectText" class="text-xs font-mono text-[#666666] mt-0.5 truncate max-w-xl"></p>
            </div>
            <button type="button" onclick="closePreviewModal()" class="p-1 text-[#666666] hover:text-[#050505] rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 p-4 overflow-y-auto bg-[#F7F7F6]">
            <div id="previewBodyContainer" class="bg-white rounded-xl border border-[#E6E6E6] p-4 min-h-[300px]"></div>
        </div>
        <div class="p-3 border-t border-[#E6E6E6] bg-white flex justify-end">
            <button type="button" onclick="closePreviewModal()" class="px-4 py-2 bg-[#050505] text-white text-xs font-bold rounded-xl hover:bg-black">
                Close
            </button>
        </div>
    </div>
</div>

@verbatim
<script>
// Auto-slugify name into code
document.getElementById('templateName').addEventListener('input', function(e) {
    const codeInput = document.getElementById('templateCode');
    if (!codeInput.dataset.touched) {
        codeInput.value = e.target.value.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
    }
});
document.getElementById('templateCode').addEventListener('input', function() {
    this.dataset.touched = 'true';
});

let lastFocusedInput = document.getElementById('bodyHtml');
document.getElementById('subjectInput').addEventListener('focus', () => lastFocusedInput = document.getElementById('subjectInput'));
document.getElementById('bodyHtml').addEventListener('focus', () => lastFocusedInput = document.getElementById('bodyHtml'));
document.getElementById('bodyText').addEventListener('focus', () => lastFocusedInput = document.getElementById('bodyText'));

function insertPlaceholder(tag) {
    if (!lastFocusedInput) lastFocusedInput = document.getElementById('bodyHtml');
    const start = lastFocusedInput.selectionStart ?? lastFocusedInput.value.length;
    const end = lastFocusedInput.selectionEnd ?? lastFocusedInput.value.length;
    const val = lastFocusedInput.value;
    lastFocusedInput.value = val.substring(0, start) + tag + val.substring(end);
    lastFocusedInput.focus();
    lastFocusedInput.setSelectionRange(start + tag.length, start + tag.length);
}

function generatePlainTextFromHtml() {
    const html = document.getElementById('bodyHtml').value;
    const tmp = document.createElement('div');
    tmp.innerHTML = html;
    document.getElementById('bodyText').value = (tmp.textContent || tmp.innerText || '').replace(/\n{3,}/g, '\n\n').trim();
}

function loadStarterBoilerplate() {
    if (document.getElementById('bodyHtml').value.trim() !== '' && !confirm('Replace current editor content with clean starter template?')) {
        return;
    }
    document.getElementById('bodyHtml').value = `<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{business_name}}</title>
</head>
<body style="margin:0; padding:24px; background-color:#F7F7F6; font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color:#050505;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:580px; margin:0 auto; background-color:#ffffff; border-radius:16px; border:1px solid #E6E6E6; overflow:hidden;">
    <tr>
      <td style="padding:28px 32px; background-color:#050505; color:#ffffff;">
        <h1 style="margin:0; font-size:20px; font-weight:900; letter-spacing:-0.5px;">{{business_name}}</h1>
        <p style="margin:4px 0 0; font-size:12px; color:#B7FF00; font-family:monospace;">{{business_phone}} &bull; {{business_email}}</p>
      </td>
    </tr>
    <tr>
      <td style="padding:32px;">
        <h2 style="margin:0 0 12px; font-size:16px; font-weight:800; color:#050505;">Hello {{customer_name}},</h2>
        <p style="margin:0 0 18px; font-size:13px; line-height:1.6; color:#444444;">
          Thank you for choosing {{business_name}}. Here is an update regarding your transaction.
        </p>

        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:20px 0; background-color:#F7F7F6; border-radius:12px; border:1px solid #E6E6E6;">
          <tr>
            <td style="padding:14px 18px; font-size:12px; color:#666666; border-bottom:1px solid #E6E6E6;">Invoice Reference</td>
            <td style="padding:14px 18px; font-size:12px; font-weight:700; color:#050505; text-align:right; font-family:monospace; border-bottom:1px solid #E6E6E6;">{{invoice_number}}</td>
          </tr>
          <tr>
            <td style="padding:14px 18px; font-size:12px; color:#666666;">Total Amount</td>
            <td style="padding:14px 18px; font-size:14px; font-weight:900; color:#050505; text-align:right; font-family:monospace;">{{invoice_total}}</td>
          </tr>
        </table>

        <p style="margin:18px 0 0; font-size:12px; line-height:1.5; color:#666666;">
          If you have any questions, feel free to reply directly to this email or reach us at {{business_phone}}.
        </p>
      </td>
    </tr>
    <tr>
      <td style="padding:18px 32px; background-color:#F7F7F6; border-top:1px solid #E6E6E6; text-align:center; font-size:11px; color:#888888;">
        &copy; {{current_year}} {{business_name}}. All rights reserved.
      </td>
    </tr>
  </table>
</body>
</html>`;
}

function previewDraft() {
    const rawSubject = document.getElementById('subjectInput').value;
    const rawHtml = document.getElementById('bodyHtml').value;

    const sample = {
        '{{customer_name}}': 'John Doe',
        '{{customer_email}}': 'customer@example.com',
        '{{customer_phone}}': '+91 98765 43210',
        '{{invoice_number}}': 'INV-2026-0089',
        '{{invoice_date}}': '15 Nov 2026',
        '{{invoice_total}}': '₹49,999.00',
        '{{invoice_balance_due}}': '₹9,999.00',
        '{{business_name}}': 'Apex Telecommunications',
        '{{business_phone}}': '+91 98200 12345',
        '{{business_email}}': 'billing@apextech.in',
        '{{current_year}}': '2026',
    };

    let renderedSubject = rawSubject;
    let renderedHtml = rawHtml;
    for (const [k, v] of Object.entries(sample)) {
        renderedSubject = renderedSubject.split(k).join(v);
        renderedHtml = renderedHtml.split(k).join(v);
    }

    document.getElementById('previewSubjectText').textContent = 'Subject: ' + renderedSubject;
    document.getElementById('previewBodyContainer').innerHTML = renderedHtml;
    document.getElementById('previewModal').classList.remove('hidden');
}

function closePreviewModal() {
    document.getElementById('previewModal').classList.add('hidden');
}
</script>
@endverbatim
@endsection

