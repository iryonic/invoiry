@extends('layouts.app')

@section('title', 'Edit Template — ' . $template->name . ' — Invoiry')

@section('content')
<div class="space-y-6 mx-auto">
    {{-- Header Banner --}}
    <div class="card-surface p-6 sm:p-7 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <a href="{{ route('mail.templates.index') }}" class="text-xs font-semibold text-[#666666] hover:text-[#050505] flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                    <span>Back to Templates</span>
                </a>
                <span class="text-xs text-[#CCCCCC]">/</span>
                <span class="text-xs font-mono text-[#666666]">{{ $template->code }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-[#050505] tracking-tight">Edit Template: {{ $template->name }}</h1>
            <p class="text-xs text-[#666666] mt-0.5">Modify content, subject lines, category, and active status.</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="previewDraft()" class="px-3.5 py-2 rounded-xl text-xs font-bold border border-[#E6E6E6] bg-white text-[#050505] hover:bg-[#F7F7F6] transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#666666]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><circle cx="12" cy="12" r="3"/></svg>
                <span>Live Preview</span>
            </button>
        </div>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('mail.templates.update', $template) }}" id="templateForm" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Column --}}
            <div class="lg:col-span-2 space-y-5">
                <div class="card-surface p-5 sm:p-6 space-y-4">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-[#666666] border-b border-[#E6E6E6] pb-2">
                        Basic Identity
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#050505] mb-1.5">Template Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $template->name) }}" required class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs text-[#050505] focus:outline-none focus:border-[#050505]">
                            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#050505] mb-1.5">System Code (Slug)</label>
                            <input type="text" disabled value="{{ $template->code }}" class="w-full px-3.5 py-2.5 bg-gray-100 border border-[#E6E6E6] rounded-xl text-xs font-mono text-[#888888] cursor-not-allowed">
                            <p class="text-[10px] text-[#888888] mt-1">Slug codes are permanent identifiers used by automation triggers.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#050505] mb-1.5">Category <span class="text-red-500">*</span></label>
                            <select name="category" required class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs text-[#050505] focus:outline-none focus:border-[#050505]">
                                <option value="invoices" {{ old('category', $template->category) === 'invoices' ? 'selected' : '' }}>Invoicing & Bills</option>
                                <option value="payments" {{ old('category', $template->category) === 'payments' ? 'selected' : '' }}>Payments & Receipts</option>
                                <option value="reminders" {{ old('category', $template->category) === 'reminders' ? 'selected' : '' }}>Reminders & Khata</option>
                                <option value="customers" {{ old('category', $template->category) === 'customers' ? 'selected' : '' }}>Customer Care & Welcome</option>
                                <option value="system" {{ old('category', $template->category) === 'system' ? 'selected' : '' }}>System & Security</option>
                                <option value="general" {{ old('category', $template->category) === 'general' ? 'selected' : '' }}>General Business Notice</option>
                            </select>
                            @error('category') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center gap-6 pt-5">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $template->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded border-[#E6E6E6] text-[#050505] focus:ring-0">
                                <span class="text-xs font-semibold text-[#050505]">Active</span>
                            </label>

                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_default" value="1" {{ old('is_default', $template->is_default) ? 'checked' : '' }} class="w-4 h-4 rounded border-[#E6E6E6] text-[#050505] focus:ring-0">
                                <span class="text-xs font-semibold text-[#050505]">Set as Default for Category</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Subject Line --}}
                <div class="card-surface p-5 sm:p-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-[#050505]">Email Subject Line <span class="text-red-500">*</span></label>
                        <span class="text-[11px] text-[#888888]">Supports dynamic placeholders</span>
                    </div>
                    <input type="text" name="subject" id="subjectInput" value="{{ old('subject', $template->subject) }}" required class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs font-mono text-[#050505] focus:outline-none focus:border-[#050505]">
                    @error('subject') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- HTML Body Editor --}}
                <div class="card-surface p-5 sm:p-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="block text-xs font-bold text-[#050505]">HTML Template Content <span class="text-red-500">*</span></label>
                            <p class="text-[11px] text-[#888888]">Directly rendered across web & mobile mail clients.</p>
                        </div>
                    </div>

                    <textarea name="body_html" id="bodyHtml" rows="18" required class="w-full p-3.5 bg-[#050505] text-[#F7F7F6] font-mono text-xs rounded-xl border border-gray-800 focus:outline-none focus:ring-1 focus:ring-[#B7FF00] leading-relaxed">{{ old('body_html', $template->body_html) }}</textarea>
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
                    <textarea name="body_text" id="bodyText" rows="6" class="w-full p-3.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs font-mono text-[#050505] focus:outline-none focus:border-[#050505]">{{ old('body_text', $template->body_text) }}</textarea>
                    @error('body_text') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Sidebar Column --}}
            <div class="space-y-5">
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
                Update Template
            </button>
        </div>
    </form>
</div>

{{-- Live Preview Modal --}}
<div id="previewModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="card-surface max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl">
        <div class="p-4 border-b border-[#E6E6E6] flex items-center justify-between bg-[#F7F7F6]">
            <div>
                <h3 class="text-sm font-bold text-[#050505]">Template Preview</h3>
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

