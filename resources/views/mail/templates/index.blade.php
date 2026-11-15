@extends('layouts.app')

@section('title', 'Email Templates — Invoiry')

@section('content')
<div class="space-y-6">
    {{-- Header Banner --}}
    <div class="card-surface p-6 sm:p-7 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-[#B7FF00]/20 text-[#050505] border border-[#B7FF00]">
                    Branded Mail Engine
                </span>
                <span class="text-xs font-mono text-[#666666]">HTML & Plaintext Templates</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-[#050505] tracking-tight">Email Templates</h1>
            <p class="text-xs text-[#666666] mt-0.5">Manage customer-facing notification templates, dynamic tags, and brand themes.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('mail.compose') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold border border-[#E6E6E6] bg-white text-[#050505] hover:bg-[#F7F7F6] transition-colors">
                Compose Email
            </a>
            <a href="{{ route('mail.templates.create') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-[#050505] text-[#B7FF00] hover:bg-black transition-all shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>New Template</span>
            </a>
        </div>
    </div>

    {{-- Subnavigation Tabs --}}
    @include('mail.partials.nav-tabs')

    {{-- Filter & Search Toolbar --}}
    <div class="card-surface p-4">
        <form method="GET" action="{{ route('mail.templates.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="relative flex-1">
                <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-[#888888]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search templates by title, slug code, or subject line..." class="w-full pl-9 pr-3.5 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs text-[#050505] focus:outline-none focus:border-[#050505]">
            </div>

            <div class="flex items-center gap-2">
                <select name="category" onchange="this.form.submit()" class="px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs text-[#050505] focus:outline-none">
                    @foreach($categories as $catKey => $catLabel)
                        <option value="{{ $catKey }}" {{ request('category', 'all') === $catKey ? 'selected' : '' }}>{{ $catLabel }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-4 py-2 bg-[#050505] text-white text-xs font-bold rounded-xl hover:bg-black transition-colors">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'category']))
                    <a href="{{ route('mail.templates.index') }}" class="px-3 py-2 text-xs font-semibold text-[#666666] hover:text-[#050505]">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Templates Grid / Table --}}
    <div class="card-surface overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#E6E6E6] bg-[#F7F7F6] text-[11px] font-bold uppercase tracking-wider text-[#666666]">
                        <th class="py-3 px-4">Template Name</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Subject Preview</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Usage</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E6E6E6] text-xs">
                    @forelse($templates as $tmpl)
                        <tr class="hover:bg-[#F7F7F6]/60 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-[#050505] text-[#B7FF00] flex items-center justify-center font-mono font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($tmpl->code, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-bold text-[#050505]">{{ $tmpl->name }}</span>
                                            @if($tmpl->is_default)
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-[#B7FF00]/30 text-[#050505] border border-[#B7FF00]">Default</span>
                                            @endif
                                            @if(is_null($tmpl->tenant_id))
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-gray-100 text-[#666666]">System</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] font-mono text-[#888888]">{{ $tmpl->code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#F7F7F6] border border-[#E6E6E6] text-[#050505]">
                                    {{ $tmpl->category_label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs truncate text-[#666666] font-mono text-[11px]">
                                {{ $tmpl->subject }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($tmpl->is_active)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-gray-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span> Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-semibold text-[#050505]">
                                {{ number_format($tmpl->logs_count) }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1">
                                    {{-- Preview Modal Trigger --}}
                                    <button type="button" onclick="previewTemplate({{ $tmpl->id }}, '{{ addslashes($tmpl->name) }}')" class="p-1.5 text-[#666666] hover:text-[#050505] hover:bg-white rounded-lg border border-transparent hover:border-[#E6E6E6] transition-colors" title="Live Preview">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>

                                    {{-- Duplicate --}}
                                    <form method="POST" action="{{ route('mail.templates.duplicate', $tmpl) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-[#666666] hover:text-[#050505] hover:bg-white rounded-lg border border-transparent hover:border-[#E6E6E6] transition-colors" title="Duplicate Template">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="13" height="13" x="9" y="9" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                        </button>
                                    </form>

                                    {{-- Edit --}}
                                    <a href="{{ route('mail.templates.edit', $tmpl) }}" class="p-1.5 text-[#666666] hover:text-[#050505] hover:bg-white rounded-lg border border-transparent hover:border-[#E6E6E6] transition-colors" title="Edit Template">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                    </a>

                                    {{-- Delete --}}
                                    <form method="POST" action="{{ route('mail.templates.destroy', $tmpl) }}" onsubmit="return confirm('Delete template \'{{ addslashes($tmpl->name) }}\'? This cannot be undone.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg border border-transparent hover:border-red-200 transition-colors" title="Delete Template">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-[#666666]">
                                <svg class="w-10 h-10 mx-auto text-[#CCCCCC] mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                                <p class="text-xs font-semibold text-[#050505]">No email templates found</p>
                                <p class="text-[11px] text-[#888888] mt-1">Try clearing your filters or create a custom email template.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($templates->hasPages())
            <div class="p-4 border-t border-[#E6E6E6]">
                {{ $templates->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Preview Modal --}}
<div id="previewModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="card-surface max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl">
        <div class="p-4 border-b border-[#E6E6E6] flex items-center justify-between bg-[#F7F7F6]">
            <div>
                <h3 id="previewTitle" class="text-sm font-bold text-[#050505]">Template Preview</h3>
                <p id="previewSubject" class="text-xs font-mono text-[#666666] mt-0.5 truncate max-w-xl"></p>
            </div>
            <button type="button" onclick="closePreviewModal()" class="p-1 text-[#666666] hover:text-[#050505] rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 p-4 overflow-y-auto bg-[#F7F7F6]">
            <div id="previewBody" class="bg-white rounded-xl border border-[#E6E6E6] p-4 min-h-[300px]">
                <div class="flex items-center justify-center h-48 text-[#888888] text-xs">
                    Loading template rendering...
                </div>
            </div>
        </div>
        <div class="p-3 border-t border-[#E6E6E6] bg-white flex justify-end">
            <button type="button" onclick="closePreviewModal()" class="px-4 py-2 bg-[#050505] text-white text-xs font-bold rounded-xl hover:bg-black">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function previewTemplate(templateId, templateName) {
    document.getElementById('previewTitle').textContent = templateName;
    document.getElementById('previewSubject').textContent = 'Loading sample subject...';
    document.getElementById('previewBody').innerHTML = '<div class="flex items-center justify-center h-48 text-[#888888] text-xs">Loading template rendering with sample variables...</div>';
    document.getElementById('previewModal').classList.remove('hidden');

    fetch(`{{ url('mail/templates') }}/${templateId}/preview`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('previewSubject').textContent = 'Subject: ' + data.subject;
            document.getElementById('previewBody').innerHTML = data.body_html;
        })
        .catch(err => {
            document.getElementById('previewBody').innerHTML = '<div class="p-4 text-xs text-red-600 bg-red-50 rounded-lg">Failed to render template preview: ' + err.message + '</div>';
        });
}

function closePreviewModal() {
    document.getElementById('previewModal').classList.add('hidden');
}
</script>
@endsection

