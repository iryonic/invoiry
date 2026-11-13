@extends('layouts.app')

@section('title', 'Features & Pricing PDF Management · Super Admin')
@section('page-title', 'Brochure & Pricing Generator')

@section('content')
<div class="max-w-7xl mx-auto space-y-5">

    <!-- Top Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold font-mono bg-[#111111] text-[#B7FF00] tracking-wider uppercase">
                    v{{ $settings['version'] ?? '2.4.0' }}
                </span>
                <span class="text-xs font-semibold text-[#079455] bg-[#ECFDF3] border border-[#A6F4C5] px-2 py-0.5 rounded">
                    Dynamic Document Engine
                </span>
                @if($latestGeneration)
                    <span class="text-[11px] text-[#666666]">
                        Last compiled: {{ $latestGeneration->created_at->diffForHumans() }} ({{ round($latestGeneration->file_size / 1024, 1) }} KB)
                    </span>
                @endif
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-[#111111] tracking-tight">Features & Pricing PDF Management</h1>
            <p class="text-xs sm:text-sm text-[#666666]">Manage dynamic software features, authentic UI screenshots, commercial pricing plans, and compile publication-ready A4 brochures.</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('superadmin.brochure.preview') }}" target="_blank" class="btn-secondary min-h-[40px] text-xs inline-flex items-center gap-1.5 shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span>Full A4 Preview</span>
            </a>

            <a href="{{ route('superadmin.brochure.download') }}" class="btn-secondary min-h-[40px] text-xs inline-flex items-center gap-1.5 font-bold shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Download PDF</span>
            </a>

            <form method="POST" action="{{ route('superadmin.brochure.generate') }}" class="inline">
                @csrf
                <input type="hidden" name="bump_version" value="0">
                <button type="submit" class="btn-lime min-h-[40px] text-xs font-bold inline-flex items-center gap-1.5 shadow-xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Compile Latest PDF</span>
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-[#ECFDF3] border border-[#A6F4C5] rounded-xl text-xs sm:text-sm text-[#027A48] font-bold flex items-center gap-2 shadow-2xs">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-[#FEF3F2] border border-[#FECDCA] rounded-xl text-xs sm:text-sm text-[#B42318] space-y-1 shadow-2xs">
            <div class="font-bold">Please correct the following errors:</div>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Functional Tabs Bar -->
    <div class="card-surface p-1.5 shadow-2xs">
        <nav class="flex items-center gap-1 overflow-x-auto scrollbar-none" id="brochureTabsNav">
            <button type="button" onclick="switchBrochureTab('cover')" id="tabBtn-cover"
                class="brochure-tab-btn flex items-center gap-2 px-3.5 py-2.5 rounded-lg text-xs font-bold transition whitespace-nowrap bg-[#111111] text-[#B7FF00] shadow-xs cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>1. Cover & Identity</span>
            </button>

            <button type="button" onclick="switchBrochureTab('overview')" id="tabBtn-overview"
                class="brochure-tab-btn flex items-center gap-2 px-3.5 py-2.5 rounded-lg text-xs font-semibold text-[#666666] hover:text-[#111111] hover:bg-[#F7F7F6] transition whitespace-nowrap cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>2. Overview & Value</span>
            </button>

            <button type="button" onclick="switchBrochureTab('modules')" id="tabBtn-modules"
                class="brochure-tab-btn flex items-center gap-2 px-3.5 py-2.5 rounded-lg text-xs font-semibold text-[#666666] hover:text-[#111111] hover:bg-[#F7F7F6] transition whitespace-nowrap cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span>3. Software Modules ({{ $modules->count() }})</span>
            </button>

            <button type="button" onclick="switchBrochureTab('screenshots')" id="tabBtn-screenshots"
                class="brochure-tab-btn flex items-center gap-2 px-3.5 py-2.5 rounded-lg text-xs font-semibold text-[#666666] hover:text-[#111111] hover:bg-[#F7F7F6] transition whitespace-nowrap cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>4. UI Screenshots ({{ $screenshots->count() }})</span>
            </button>

            <button type="button" onclick="switchBrochureTab('pricing')" id="tabBtn-pricing"
                class="brochure-tab-btn flex items-center gap-2 px-3.5 py-2.5 rounded-lg text-xs font-semibold text-[#666666] hover:text-[#111111] hover:bg-[#F7F7F6] transition whitespace-nowrap cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>5. Commercial Pricing ({{ $plans->count() }})</span>
            </button>

            <button type="button" onclick="switchBrochureTab('toggles')" id="tabBtn-toggles"
                class="brochure-tab-btn flex items-center gap-2 px-3.5 py-2.5 rounded-lg text-xs font-semibold text-[#666666] hover:text-[#111111] hover:bg-[#F7F7F6] transition whitespace-nowrap cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                <span>6. Sections & Toggles</span>
            </button>

            <button type="button" onclick="switchBrochureTab('preview')" id="tabBtn-preview"
                class="brochure-tab-btn flex items-center gap-2 px-3.5 py-2.5 rounded-lg text-xs font-semibold text-[#666666] hover:text-[#111111] hover:bg-[#F7F7F6] transition whitespace-nowrap cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span>7. Live A4 Preview & Build</span>
            </button>
        </nav>
    </div>

    <!-- TAB 1: COVER & IDENTITY -->
    <div id="brochureTabContent-cover" class="brochure-tab-panel space-y-5">
        <form method="POST" action="{{ route('superadmin.brochure.settings.update') }}" class="space-y-5">
            @csrf
            <div class="card-surface p-5 sm:p-6 space-y-4">
                <h3 class="text-sm font-bold text-[#111111] uppercase tracking-wider border-b border-[#E6E6E6] pb-2">
                    Brochure Header & Product Branding
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Product Name</label>
                        <input type="text" name="product_name" value="{{ old('product_name', $settings['product_name'] ?? 'INVOIRY') }}" required
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-bold focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Document Version</label>
                        <input type="text" name="version" value="{{ old('version', $settings['version'] ?? '2.4.0') }}" required
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono font-bold focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Tagline</label>
                        <input type="text" name="product_tagline" value="{{ old('product_tagline', $settings['product_tagline'] ?? '') }}"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Document Title</label>
                        <input type="text" name="document_title" value="{{ old('document_title', $settings['document_title'] ?? '') }}" required
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-semibold focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Document Subtitle</label>
                        <input type="text" name="document_subtitle" value="{{ old('document_subtitle', $settings['document_subtitle'] ?? '') }}"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#111111] mb-1">Hero Main Headline</label>
                    <input type="text" name="hero_headline" value="{{ old('hero_headline', $settings['hero_headline'] ?? '') }}" required
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-bold focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#111111] mb-1">Hero Subheadline & Value Proposition</label>
                    <textarea name="hero_subheadline" rows="2"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">{{ old('hero_subheadline', $settings['hero_subheadline'] ?? '') }}</textarea>
                </div>
            </div>

            <!-- Contact Information Required Section -->
            <div class="card-surface p-5 sm:p-6 space-y-4">
                <h3 class="text-sm font-bold text-[#111111] uppercase tracking-wider border-b border-[#E6E6E6] pb-2">
                    Official Contact Information (Printed on Back Cover)
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Primary Direct Phone</label>
                        <input type="text" name="contact_phone_1" value="{{ old('contact_phone_1', $settings['contact_phone_1'] ?? '6006801960') }}" required
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono font-bold focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Secondary Direct Phone</label>
                        <input type="text" name="contact_phone_2" value="{{ old('contact_phone_2', $settings['contact_phone_2'] ?? '6005958161') }}"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono font-bold focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Official Email Address</label>
                        <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? 'hire.iry@gmail.com') }}" required
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono font-bold focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Official Website / Repo</label>
                        <input type="text" name="contact_website" value="{{ old('contact_website', $settings['contact_website'] ?? 'https://github.com/iryonic/invoiry.git') }}"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Headquarters / Physical Address</label>
                        <input type="text" name="contact_address" value="{{ old('contact_address', $settings['contact_address'] ?? '') }}"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="btn-lime min-h-[42px] px-6 text-xs font-bold inline-flex items-center gap-2 cursor-pointer shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Save Cover & Contact Settings</span>
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 2: OVERVIEW & VALUE PROPOSITION -->
    <div id="brochureTabContent-overview" class="brochure-tab-panel hidden space-y-5">
        <form method="POST" action="{{ route('superadmin.brochure.settings.update') }}" class="space-y-5">
            @csrf
            <!-- Preserve other settings -->
            <input type="hidden" name="product_name" value="{{ $settings['product_name'] ?? 'INVOIRY' }}">
            <input type="hidden" name="version" value="{{ $settings['version'] ?? '2.4.0' }}">
            <input type="hidden" name="document_title" value="{{ $settings['document_title'] ?? 'INVOIRY Features & Pricing' }}">
            <input type="hidden" name="hero_headline" value="{{ $settings['hero_headline'] ?? '' }}">
            <input type="hidden" name="contact_phone_1" value="{{ $settings['contact_phone_1'] ?? '6006801960' }}">
            <input type="hidden" name="contact_email" value="{{ $settings['contact_email'] ?? 'hire.iry@gmail.com' }}">

            <div class="card-surface p-5 sm:p-6 space-y-4">
                <h3 class="text-sm font-bold text-[#111111] uppercase tracking-wider border-b border-[#E6E6E6] pb-2">
                    Product Overview & Commercial Differentiators
                </h3>

                <div>
                    <label class="block text-xs font-semibold text-[#111111] mb-1">What the Software Does (Full Summary)</label>
                    <textarea name="overview_what_it_does" rows="3"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">{{ old('overview_what_it_does', $settings['overview_what_it_does'] ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#111111] mb-1">Target Retailers & Audiences</label>
                    <textarea name="overview_target_audience" rows="2"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">{{ old('overview_target_audience', $settings['overview_target_audience'] ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#111111] mb-1">Critical Retail Problems Solved (One per line)</label>
                    <textarea name="overview_problems_solved" rows="4" font-mono
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">{{ old('overview_problems_solved', $settings['overview_problems_solved'] ?? '') }}</textarea>
                    <span class="text-[11px] text-[#666666]">Enter each problem on a new line; bullet points will be formatted automatically.</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#111111] mb-1">Key Benefits & Competitive Advantage (One per line)</label>
                    <textarea name="overview_key_benefits" rows="4"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">{{ old('overview_key_benefits', $settings['overview_key_benefits'] ?? '') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-lime min-h-[42px] px-6 text-xs font-bold inline-flex items-center gap-2 cursor-pointer shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Save Overview Content</span>
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 3: SOFTWARE MODULES -->
    <div id="brochureTabContent-modules" class="brochure-tab-panel hidden space-y-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-[#111111]">Software Modules & Feature Sets</h3>
                <p class="text-xs text-[#666666]">Each module appears in the PDF with technical capabilities and feature checklists.</p>
            </div>

            <button type="button" onclick="openAddModuleModal()" class="btn-lime text-xs min-h-[38px] px-4 font-bold inline-flex items-center gap-1.5 cursor-pointer shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Module</span>
            </button>
        </div>

        <div class="space-y-3" id="modulesList">
            @foreach($modules as $mod)
                <div class="card-surface p-4 sm:p-5 flex flex-col md:flex-row md:items-start justify-between gap-4 border border-[#E6E6E6] hover:border-[#CCCCCC] transition">
                    <div class="space-y-1.5 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-[#111111] text-[#B7FF00] font-mono text-xs font-bold">
                                {{ $mod->sort_order }}
                            </span>
                            @if($mod->badge)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#F2F4F7] text-[#344054] border border-[#EAECF0] uppercase">
                                    {{ $mod->badge }}
                                </span>
                            @endif
                            <h4 class="text-sm font-bold text-[#111111]">{{ $mod->title }}</h4>
                            @if(!$mod->is_enabled)
                                <span class="text-[10px] font-bold text-[#D92D20] bg-[#FEF3F2] px-2 py-0.5 rounded">Disabled</span>
                            @endif
                        </div>

                        @if($mod->subtitle)
                            <div class="text-xs font-semibold text-[#079455]">{{ $mod->subtitle }}</div>
                        @endif

                        <p class="text-xs text-[#555555] line-clamp-2">{{ $mod->description }}</p>

                        @if(!empty($mod->features))
                            <div class="pt-2 flex flex-wrap gap-1.5">
                                @foreach(array_slice($mod->features, 0, 4) as $feat)
                                    <span class="inline-flex items-center gap-1 text-[11px] bg-[#F7F7F6] text-[#333333] px-2 py-1 rounded-md border border-[#E6E6E6]">
                                        <span class="text-[#079455] font-bold">✓</span> {{ $feat }}
                                    </span>
                                @endforeach
                                @if(count($mod->features) > 4)
                                    <span class="text-[11px] text-[#666666] self-center">+{{ count($mod->features) - 4 }} more features</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0 self-end md:self-start">
                        <button type="button" onclick="editModule({{ json_encode($mod) }})" class="btn-secondary text-xs min-h-[34px] px-3 inline-flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Edit</span>
                        </button>

                        <form method="POST" action="{{ route('superadmin.brochure.modules.destroy', $mod) }}" onsubmit="return confirm('Remove module \'{{ $mod->title }}\'?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-secondary text-xs min-h-[34px] px-2.5 text-[#D92D20] hover:bg-[#FEF3F2] cursor-pointer" title="Delete module">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- TAB 4: APPLICATION SCREENSHOTS -->
    <div id="brochureTabContent-screenshots" class="brochure-tab-panel hidden space-y-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-[#111111]">Real Application UI Screenshots</h3>
                <p class="text-xs text-[#666666]">Curated real screens showcased in the brochure gallery with technical captions.</p>
            </div>

            <button type="button" onclick="openUploadScreenshotModal()" class="btn-lime text-xs min-h-[38px] px-4 font-bold inline-flex items-center gap-1.5 cursor-pointer shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span>Upload Screenshot</span>
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($screenshots as $shot)
                <div class="card-surface overflow-hidden border border-[#E6E6E6] flex flex-col justify-between">
                    <div>
                        <div class="bg-[#111111] text-white p-2.5 flex items-center justify-between text-xs font-bold">
                            <span class="truncate">{{ $shot->title }}</span>
                            <span class="text-[10px] text-[#B7FF00] font-mono font-normal">#{{ $shot->sort_order }}</span>
                        </div>
                        <div class="bg-black/5 aspect-video overflow-hidden flex items-center justify-center relative">
                            <img src="{{ $shot->url }}" alt="{{ $shot->title }}" class="w-full h-full object-cover">
                            @if(!$shot->is_enabled)
                                <div class="absolute inset-0 bg-black/60 flex items-center justify-center text-white text-xs font-bold">
                                    Hidden from PDF
                                </div>
                            @endif
                        </div>
                        <div class="p-3 text-xs text-[#555555] line-clamp-2">
                            {{ $shot->caption ?? 'No caption provided.' }}
                        </div>
                    </div>

                    <div class="p-3 pt-0 flex items-center justify-between border-t border-[#E6E6E6] mt-2">
                        <span class="text-[11px] text-[#666666] truncate">
                            {{ $shot->module?->title ?? 'General Showcase' }}
                        </span>

                        <div class="flex items-center gap-1">
                            <button type="button" onclick="editScreenshot({{ json_encode($shot) }})" class="btn-secondary text-[11px] min-h-[30px] px-2 cursor-pointer">
                                Edit
                            </button>
                            <form method="POST" action="{{ route('superadmin.brochure.screenshots.destroy', $shot) }}" onsubmit="return confirm('Remove screenshot?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-secondary text-[11px] min-h-[30px] px-2 text-[#D92D20] hover:bg-[#FEF3F2] cursor-pointer">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- TAB 5: COMMERCIAL PRICING PLANS -->
    <div id="brochureTabContent-pricing" class="brochure-tab-panel hidden space-y-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-[#111111]">Commercial Plans & Pricing Matrix</h3>
                <p class="text-xs text-[#666666]">Configure subscription tiers, price points, feature checklists, and the popular tier banner.</p>
            </div>

            <button type="button" onclick="openAddPlanModal()" class="btn-lime text-xs min-h-[38px] px-4 font-bold inline-flex items-center gap-1.5 cursor-pointer shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Pricing Plan</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($plans as $plan)
                <div class="card-surface p-5 border {{ $plan->is_popular ? 'border-2 border-[#111111] ring-1 ring-black/10' : 'border-[#E6E6E6]' }} flex flex-col justify-between relative shadow-2xs">
                    @if($plan->badge)
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold font-mono tracking-wider uppercase {{ $plan->is_popular ? 'bg-[#111111] text-[#B7FF00]' : 'bg-[#E6E6E6] text-[#333333]' }}">
                                {{ $plan->badge }}
                            </span>
                        </div>
                    @endif

                    <div class="space-y-3">
                        <div class="text-center pt-2 border-b border-[#E6E6E6] pb-3">
                            <h4 class="text-base font-bold text-[#111111]">{{ $plan->name }}</h4>
                            <div class="text-2xl font-black text-[#111111] font-mono mt-1">{{ $plan->price }}</div>
                            <div class="text-xs text-[#666666] font-semibold">{{ $plan->period }}</div>
                        </div>

                        <p class="text-xs text-[#555555] text-center min-h-[32px]">{{ $plan->description }}</p>

                        <div class="space-y-1.5 pt-2 border-t border-[#E6E6E6]">
                            @if(!empty($plan->features))
                                @foreach($plan->features as $feat)
                                    <div class="text-xs text-[#333333] flex items-start gap-1.5">
                                        <span class="text-[#079455] font-bold">✓</span>
                                        <span>{{ $feat }}</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        @if($plan->limitations)
                            <div class="text-[11px] text-[#777777] italic pt-2 border-t border-dashed border-[#E6E6E6]">
                                {{ $plan->limitations }}
                            </div>
                        @endif
                    </div>

                    <div class="pt-4 mt-4 border-t border-[#E6E6E6] flex items-center justify-between">
                        <span class="text-xs font-mono font-bold text-[#666666]">#{{ $plan->sort_order }}</span>

                        <div class="flex items-center gap-1">
                            <button type="button" onclick="editPlan({{ json_encode($plan) }})" class="btn-secondary text-xs min-h-[32px] px-3 cursor-pointer">
                                Edit Plan
                            </button>
                            <form method="POST" action="{{ route('superadmin.brochure.plans.destroy', $plan) }}" onsubmit="return confirm('Remove plan \'{{ $plan->name }}\'?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-secondary text-xs min-h-[32px] px-2 text-[#D92D20] hover:bg-[#FEF3F2] cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- TAB 6: SECTIONS & TOGGLES -->
    <div id="brochureTabContent-toggles" class="brochure-tab-panel hidden space-y-5">
        <form method="POST" action="{{ route('superadmin.brochure.settings.update') }}" class="space-y-5">
            @csrf
            <!-- Preserve other fields -->
            <input type="hidden" name="product_name" value="{{ $settings['product_name'] ?? 'INVOIRY' }}">
            <input type="hidden" name="version" value="{{ $settings['version'] ?? '2.4.0' }}">
            <input type="hidden" name="document_title" value="{{ $settings['document_title'] ?? 'INVOIRY Features & Pricing' }}">
            <input type="hidden" name="hero_headline" value="{{ $settings['hero_headline'] ?? '' }}">
            <input type="hidden" name="contact_phone_1" value="{{ $settings['contact_phone_1'] ?? '6006801960' }}">
            <input type="hidden" name="contact_email" value="{{ $settings['contact_email'] ?? 'hire.iry@gmail.com' }}">

            <div class="card-surface p-5 sm:p-6 space-y-4">
                <h3 class="text-sm font-bold text-[#111111] uppercase tracking-wider border-b border-[#E6E6E6] pb-2">
                    Publication Section Visibility
                </h3>
                <p class="text-xs text-[#666666]">Select which editorial pages should be generated in the compiled PDF brochure.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="flex items-center gap-3 p-3 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] cursor-pointer hover:bg-white transition">
                        <input type="checkbox" name="show_cover" value="1" {{ ($settings['show_cover'] ?? '1') == '1' ? 'checked' : '' }} class="w-4 h-4 text-black focus:ring-black rounded">
                        <div>
                            <div class="text-xs font-bold text-[#111111]">1. Cover Page</div>
                            <div class="text-[11px] text-[#666666]">Hero header, branding, version badge, and value props.</div>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] cursor-pointer hover:bg-white transition">
                        <input type="checkbox" name="show_overview" value="1" {{ ($settings['show_overview'] ?? '1') == '1' ? 'checked' : '' }} class="w-4 h-4 text-black focus:ring-black rounded">
                        <div>
                            <div class="text-xs font-bold text-[#111111]">2. Executive Overview</div>
                            <div class="text-[11px] text-[#666666]">Problems solved, target retailers, and core differentiators.</div>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] cursor-pointer hover:bg-white transition">
                        <input type="checkbox" name="show_modules" value="1" {{ ($settings['show_modules'] ?? '1') == '1' ? 'checked' : '' }} class="w-4 h-4 text-black focus:ring-black rounded">
                        <div>
                            <div class="text-xs font-bold text-[#111111]">3. Software Modules (Parts 1 & 2)</div>
                            <div class="text-[11px] text-[#666666]">POS, IMEI, Inventory, Khata, Printing, and Multi-Tenancy.</div>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] cursor-pointer hover:bg-white transition">
                        <input type="checkbox" name="show_screenshots" value="1" {{ ($settings['show_screenshots'] ?? '1') == '1' ? 'checked' : '' }} class="w-4 h-4 text-black focus:ring-black rounded">
                        <div>
                            <div class="text-xs font-bold text-[#111111]">4. Application UI Gallery</div>
                            <div class="text-[11px] text-[#666666]">Framed production screenshots with verified captions.</div>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] cursor-pointer hover:bg-white transition">
                        <input type="checkbox" name="show_pricing" value="1" {{ ($settings['show_pricing'] ?? '1') == '1' ? 'checked' : '' }} class="w-4 h-4 text-black focus:ring-black rounded">
                        <div>
                            <div class="text-xs font-bold text-[#111111]">5. Commercial Pricing & Matrix</div>
                            <div class="text-[11px] text-[#666666]">Subscription plans, feature comparison, and tier limits.</div>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] cursor-pointer hover:bg-white transition">
                        <input type="checkbox" name="show_contact" value="1" {{ ($settings['show_contact'] ?? '1') == '1' ? 'checked' : '' }} class="w-4 h-4 text-black focus:ring-black rounded">
                        <div>
                            <div class="text-xs font-bold text-[#111111]">6. Hardware Compatibility & Contact</div>
                            <div class="text-[11px] text-[#666666]">Printer specifications, onboarding roadmap, and direct phones.</div>
                        </div>
                    </label>
                </div>

                <div class="pt-4 border-t border-[#E6E6E6]">
                    <label class="block text-xs font-semibold text-[#111111] mb-1">Important Commercial & Fiscal Notes (Printed on Closing Page)</label>
                    <textarea name="terms_notes" rows="4"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">{{ old('terms_notes', $settings['terms_notes'] ?? '') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-lime min-h-[42px] px-6 text-xs font-bold inline-flex items-center gap-2 cursor-pointer shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Save Section Configurations</span>
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 7: LIVE A4 PREVIEW & COMPILE BUILD -->
    <div id="brochureTabContent-preview" class="brochure-tab-panel hidden space-y-5">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
            <!-- Left 3 Cols: Embedded Live A4 Sheet Preview -->
            <div class="lg:col-span-3 space-y-3">
                <div class="card-surface p-3 flex items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-[#111111]">Live A4 Document Viewer</span>
                        <span class="text-[#666666] hidden sm:inline">&bull; 100% High-Fidelity Print Emulation</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('superadmin.brochure.preview') }}" target="_blank" class="text-xs font-bold text-[#079455] hover:underline flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            <span>Open in Full Tab</span>
                        </a>
                        <button type="button" onclick="document.getElementById('previewIframe').contentWindow.print()" class="btn-secondary text-xs min-h-[32px] px-2.5 inline-flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Print Preview</span>
                        </button>
                    </div>
                </div>

                <div class="card-surface p-2 sm:p-4 bg-[#525659] rounded-xl overflow-hidden shadow-inner flex justify-center">
                    <iframe id="previewIframe" src="{{ route('superadmin.brochure.preview') }}" class="w-full bg-white rounded shadow-2xl border border-black/20" style="height: 850px;" frameborder="0"></iframe>
                </div>
            </div>

            <!-- Right 1 Col: Build & Version Controls -->
            <div class="space-y-4">
                <div class="card-surface p-5 space-y-4">
                    <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider border-b border-[#E6E6E6] pb-2">
                        Compile New PDF Document
                    </h3>

                    <form method="POST" action="{{ route('superadmin.brochure.generate') }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-[#111111] mb-1">Target Document Version</label>
                            <input type="text" name="version" value="{{ $settings['version'] ?? '2.4.0' }}" readonly
                                class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs font-mono font-bold">
                        </div>

                        <label class="flex items-center gap-2 text-xs font-semibold text-[#111111] cursor-pointer">
                            <input type="checkbox" name="bump_version" value="1" class="w-4 h-4 text-black focus:ring-black rounded">
                            <span>Auto-bump patch version (e.g. 2.4.1)</span>
                        </label>

                        <div>
                            <label class="block text-xs font-semibold text-[#111111] mb-1">Compilation Changelog Note</label>
                            <input type="text" name="notes" placeholder="e.g. Updated 2026 pricing and modules"
                                class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                        </div>

                        <button type="submit" class="w-full btn-lime min-h-[42px] text-xs font-bold inline-flex items-center justify-center gap-2 shadow-xs cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            <span>Compile & Save PDF</span>
                        </button>
                    </form>

                    <div class="pt-3 border-t border-[#E6E6E6]">
                        <a href="{{ route('superadmin.brochure.download') }}" class="w-full btn-secondary min-h-[40px] text-xs font-bold inline-flex items-center justify-center gap-2 shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Download Current PDF</span>
                        </a>
                    </div>
                </div>

                <!-- Version History -->
                <div class="card-surface p-4 space-y-3">
                    <h4 class="text-xs font-bold text-[#111111] uppercase tracking-wider border-b border-[#E6E6E6] pb-1.5">
                        Build History Log
                    </h4>

                    @if($generationHistory->isEmpty())
                        <p class="text-xs text-[#888888]">No previous builds recorded yet.</p>
                    @else
                        <div class="space-y-2 text-xs">
                            @foreach($generationHistory as $gen)
                                <div class="p-2.5 bg-[#F7F7F6] rounded-lg border border-[#E6E6E6] space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold font-mono text-[#111111]">v{{ $gen->version }}</span>
                                        <span class="text-[10px] text-[#666666]">{{ $gen->created_at->format('M d, H:i') }}</span>
                                    </div>
                                    <div class="text-[11px] text-[#555555] truncate">{{ $gen->notes ?? 'Compiled document' }}</div>
                                    <div class="text-[10px] text-[#888888]">{{ round($gen->file_size / 1024, 1) }} KB &bull; by {{ $gen->generator?->name ?? 'System' }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- =========================================================================
     MODALS FOR CRUD ACTIONS
     ========================================================================= --}}

<!-- Add Module Modal -->
<div id="addModuleModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl border border-black/10 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
            <h3 class="text-sm font-bold text-[#111111]" id="moduleModalTitle">Add New Software Module</h3>
            <button type="button" onclick="closeModuleModal()" class="text-gray-400 hover:text-black text-lg font-bold cursor-pointer">&times;</button>
        </div>

        <form id="moduleForm" method="POST" action="{{ route('superadmin.brochure.modules.store') }}" class="space-y-3.5">
            @csrf
            <input type="hidden" name="_method" id="moduleFormMethod" value="POST">

            <div>
                <label class="block text-xs font-semibold text-[#111111] mb-1">Module Title</label>
                <input type="text" name="title" id="module_title" required placeholder="e.g. Dual-SIM IMEI Tracking"
                    class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-bold focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-[#111111] mb-1">Subtitle / Sub-domain</label>
                    <input type="text" name="subtitle" id="module_subtitle" placeholder="e.g. Hardware Governance"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#111111] mb-1">Tag / Badge</label>
                    <input type="text" name="badge" id="module_badge" placeholder="e.g. COMPLIANCE"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm uppercase font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#111111] mb-1">Module Description</label>
                <textarea name="description" id="module_description" rows="2" required placeholder="Comprehensive hardware identity governance..."
                    class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#111111] mb-1">Features Checklist (One per line)</label>
                <textarea name="features_text" id="module_features_text" rows="4" placeholder="Dual-SIM IMEI tracking on handsets&#10;Unique serial number logging&#10;Warranty status tracking"
                    class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-[#111111] mb-1">Sort Order</label>
                    <input type="number" name="sort_order" id="module_sort_order" value="1" min="1"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs font-mono">
                </div>
                <div class="flex items-center pt-5">
                    <label class="flex items-center gap-2 text-xs font-semibold text-[#111111] cursor-pointer">
                        <input type="checkbox" name="is_enabled" id="module_is_enabled" value="1" checked class="w-4 h-4 text-black focus:ring-black rounded">
                        <span>Enabled in PDF</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-[#E6E6E6]">
                <button type="button" onclick="closeModuleModal()" class="btn-secondary text-xs min-h-[38px] px-4 cursor-pointer">Cancel</button>
                <button type="submit" class="btn-lime text-xs min-h-[38px] px-5 font-bold cursor-pointer">Save Module</button>
            </div>
        </form>
    </div>
</div>

<!-- Upload / Edit Screenshot Modal -->
<div id="screenshotModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl border border-black/10">
        <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
            <h3 class="text-sm font-bold text-[#111111]" id="screenshotModalTitle">Upload Application Screenshot</h3>
            <button type="button" onclick="closeScreenshotModal()" class="text-gray-400 hover:text-black text-lg font-bold cursor-pointer">&times;</button>
        </div>

        <form id="screenshotForm" method="POST" action="{{ route('superadmin.brochure.screenshots.store') }}" enctype="multipart/form-data" class="space-y-3.5">
            @csrf
            <input type="hidden" name="_method" id="screenshotFormMethod" value="POST">

            <div>
                <label class="block text-xs font-semibold text-[#111111] mb-1">Screenshot Title</label>
                <input type="text" name="title" id="shot_title" required placeholder="e.g. POS Billing & Scanner Screen"
                    class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-bold focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#111111] mb-1">Associated Software Module</label>
                <select name="module_id" id="shot_module_id" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    <option value="">-- General Showcase (No Module) --</option>
                    @foreach($modules as $m)
                        <option value="{{ $m->id }}">{{ $m->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#111111] mb-1">Screenshot Caption</label>
                <textarea name="caption" id="shot_caption" rows="2" placeholder="Descriptive caption highlighting what user sees..."
                    class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#111111] mb-1">Screenshot Image File (PNG, JPG, Max 5MB)</label>
                <input type="file" name="image" id="shot_image" accept="image/png,image/jpeg,image/webp"
                    class="w-full px-3 py-1.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs cursor-pointer">
                <span class="text-[11px] text-[#666666]">For best print results, upload 16:9 or 16:10 aspect ratio images (min 1280x800).</span>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-[#111111] mb-1">Sort Order</label>
                    <input type="number" name="sort_order" id="shot_sort_order" value="1" min="1"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs font-mono">
                </div>
                <div class="flex items-center pt-5">
                    <label class="flex items-center gap-2 text-xs font-semibold text-[#111111] cursor-pointer">
                        <input type="checkbox" name="is_enabled" id="shot_is_enabled" value="1" checked class="w-4 h-4 text-black focus:ring-black rounded">
                        <span>Enabled in PDF</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-[#E6E6E6]">
                <button type="button" onclick="closeScreenshotModal()" class="btn-secondary text-xs min-h-[38px] px-4 cursor-pointer">Cancel</button>
                <button type="submit" class="btn-lime text-xs min-h-[38px] px-5 font-bold cursor-pointer">Upload & Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Add / Edit Plan Modal -->
<div id="planModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl border border-black/10 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
            <h3 class="text-sm font-bold text-[#111111]" id="planModalTitle">Add Commercial Pricing Plan</h3>
            <button type="button" onclick="closePlanModal()" class="text-gray-400 hover:text-black text-lg font-bold cursor-pointer">&times;</button>
        </div>

        <form id="planForm" method="POST" action="{{ route('superadmin.brochure.plans.store') }}" class="space-y-3.5">
            @csrf
            <input type="hidden" name="_method" id="planFormMethod" value="POST">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-[#111111] mb-1">Plan Name</label>
                    <input type="text" name="name" id="plan_name" required placeholder="e.g. Professional Retail Pro"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-bold focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#111111] mb-1">Price Display</label>
                    <input type="text" name="price" id="plan_price" required placeholder="e.g. ₹1,299"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono font-bold focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-[#111111] mb-1">Billing Period</label>
                    <input type="text" name="period" id="plan_period" required placeholder="e.g. per month (or ₹11,999/yr)"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#111111] mb-1">Badge</label>
                    <input type="text" name="badge" id="plan_badge" placeholder="e.g. MOST POPULAR"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm uppercase font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#111111] mb-1">Plan Description</label>
                <textarea name="description" id="plan_description" rows="2" placeholder="Ideal for growing electronics & multi-counter shops..."
                    class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#111111] mb-1">Included Features (One per line)</label>
                <textarea name="features_text" id="plan_features_text" rows="4" placeholder="Up to 2 Store Outlets Included&#10;Unlimited Staff Accounts with RBAC&#10;Both 58mm & 80mm ESC/POS Thermal"
                    class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#111111] mb-1">Plan Limitations / Notes</label>
                <input type="text" name="limitations" id="plan_limitations" placeholder="e.g. Additional branches at ₹499/store"
                    class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
            </div>

            <div class="grid grid-cols-3 gap-3 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-[#111111] mb-1">Sort Order</label>
                    <input type="number" name="sort_order" id="plan_sort_order" value="1" min="1"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs font-mono">
                </div>
                <div class="flex items-center pt-5">
                    <label class="flex items-center gap-2 text-xs font-semibold text-[#111111] cursor-pointer">
                        <input type="checkbox" name="is_popular" id="plan_is_popular" value="1" class="w-4 h-4 text-black focus:ring-black rounded">
                        <span>Highlight as Popular</span>
                    </label>
                </div>
                <div class="flex items-center pt-5">
                    <label class="flex items-center gap-2 text-xs font-semibold text-[#111111] cursor-pointer">
                        <input type="checkbox" name="is_enabled" id="plan_is_enabled" value="1" checked class="w-4 h-4 text-black focus:ring-black rounded">
                        <span>Enabled in PDF</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-[#E6E6E6]">
                <button type="button" onclick="closePlanModal()" class="btn-secondary text-xs min-h-[38px] px-4 cursor-pointer">Cancel</button>
                <button type="submit" class="btn-lime text-xs min-h-[38px] px-5 font-bold cursor-pointer">Save Plan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function switchBrochureTab(tabName) {
        document.querySelectorAll('.brochure-tab-panel').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.brochure-tab-btn').forEach(btn => {
            btn.className = 'brochure-tab-btn flex items-center gap-2 px-3.5 py-2.5 rounded-lg text-xs font-semibold text-[#666666] hover:text-[#111111] hover:bg-[#F7F7F6] transition whitespace-nowrap cursor-pointer';
        });

        const activePanel = document.getElementById('brochureTabContent-' + tabName);
        const activeBtn = document.getElementById('tabBtn-' + tabName);

        if (activePanel) activePanel.classList.remove('hidden');
        if (activeBtn) {
            activeBtn.className = 'brochure-tab-btn flex items-center gap-2 px-3.5 py-2.5 rounded-lg text-xs font-bold transition whitespace-nowrap bg-[#111111] text-[#B7FF00] shadow-xs cursor-pointer';
        }

        // If switching to preview, refresh iframe to reflect latest saved content
        if (tabName === 'preview') {
            const iframe = document.getElementById('previewIframe');
            if (iframe) iframe.src = iframe.src;
        }

        // Update URL hash
        window.location.hash = 'tab=' + tabName;
    }

    // Modal Handlers: Modules
    function openAddModuleModal() {
        document.getElementById('moduleModalTitle').innerText = 'Add New Software Module';
        document.getElementById('moduleForm').action = "{{ route('superadmin.brochure.modules.store') }}";
        document.getElementById('moduleFormMethod').value = 'POST';
        document.getElementById('module_title').value = '';
        document.getElementById('module_subtitle').value = '';
        document.getElementById('module_badge').value = '';
        document.getElementById('module_description').value = '';
        document.getElementById('module_features_text').value = '';
        document.getElementById('module_sort_order').value = '{{ $modules->count() + 1 }}';
        document.getElementById('module_is_enabled').checked = true;
        document.getElementById('addModuleModal').classList.remove('hidden');
    }

    function editModule(mod) {
        document.getElementById('moduleModalTitle').innerText = 'Edit Module: ' + mod.title;
        document.getElementById('moduleForm').action = '/superadmin/brochure/modules/' + mod.id;
        document.getElementById('moduleFormMethod').value = 'PUT';
        document.getElementById('module_title').value = mod.title || '';
        document.getElementById('module_subtitle').value = mod.subtitle || '';
        document.getElementById('module_badge').value = mod.badge || '';
        document.getElementById('module_description').value = mod.description || '';
        document.getElementById('module_features_text').value = (mod.features || []).join('\n');
        document.getElementById('module_sort_order').value = mod.sort_order || 1;
        document.getElementById('module_is_enabled').checked = Boolean(mod.is_enabled);
        document.getElementById('addModuleModal').classList.remove('hidden');
    }

    function closeModuleModal() {
        document.getElementById('addModuleModal').classList.add('hidden');
    }

    // Modal Handlers: Screenshots
    function openUploadScreenshotModal() {
        document.getElementById('screenshotModalTitle').innerText = 'Upload Application Screenshot';
        document.getElementById('screenshotForm').action = "{{ route('superadmin.brochure.screenshots.store') }}";
        document.getElementById('screenshotFormMethod').value = 'POST';
        document.getElementById('shot_title').value = '';
        document.getElementById('shot_module_id').value = '';
        document.getElementById('shot_caption').value = '';
        document.getElementById('shot_sort_order').value = '{{ $screenshots->count() + 1 }}';
        document.getElementById('shot_is_enabled').checked = true;
        document.getElementById('shot_image').required = true;
        document.getElementById('screenshotModal').classList.remove('hidden');
    }

    function editScreenshot(shot) {
        document.getElementById('screenshotModalTitle').innerText = 'Edit Screenshot: ' + shot.title;
        document.getElementById('screenshotForm').action = '/superadmin/brochure/screenshots/' + shot.id;
        document.getElementById('screenshotFormMethod').value = 'PUT';
        document.getElementById('shot_title').value = shot.title || '';
        document.getElementById('shot_module_id').value = shot.module_id || '';
        document.getElementById('shot_caption').value = shot.caption || '';
        document.getElementById('shot_sort_order').value = shot.sort_order || 1;
        document.getElementById('shot_is_enabled').checked = Boolean(shot.is_enabled);
        document.getElementById('shot_image').required = false;
        document.getElementById('screenshotModal').classList.remove('hidden');
    }

    function closeScreenshotModal() {
        document.getElementById('screenshotModal').classList.add('hidden');
    }

    // Modal Handlers: Plans
    function openAddPlanModal() {
        document.getElementById('planModalTitle').innerText = 'Add Commercial Pricing Plan';
        document.getElementById('planForm').action = "{{ route('superadmin.brochure.plans.store') }}";
        document.getElementById('planFormMethod').value = 'POST';
        document.getElementById('plan_name').value = '';
        document.getElementById('plan_price').value = '';
        document.getElementById('plan_period').value = 'per month';
        document.getElementById('plan_badge').value = '';
        document.getElementById('plan_description').value = '';
        document.getElementById('plan_features_text').value = '';
        document.getElementById('plan_limitations').value = '';
        document.getElementById('plan_is_popular').checked = false;
        document.getElementById('plan_sort_order').value = '{{ $plans->count() + 1 }}';
        document.getElementById('plan_is_enabled').checked = true;
        document.getElementById('planModal').classList.remove('hidden');
    }

    function editPlan(plan) {
        document.getElementById('planModalTitle').innerText = 'Edit Plan: ' + plan.name;
        document.getElementById('planForm').action = '/superadmin/brochure/plans/' + plan.id;
        document.getElementById('planFormMethod').value = 'PUT';
        document.getElementById('plan_name').value = plan.name || '';
        document.getElementById('plan_price').value = plan.price || '';
        document.getElementById('plan_period').value = plan.period || '';
        document.getElementById('plan_badge').value = plan.badge || '';
        document.getElementById('plan_description').value = plan.description || '';
        document.getElementById('plan_features_text').value = (plan.features || []).join('\n');
        document.getElementById('plan_limitations').value = plan.limitations || '';
        document.getElementById('plan_is_popular').checked = Boolean(plan.is_popular);
        document.getElementById('plan_sort_order').value = plan.sort_order || 1;
        document.getElementById('plan_is_enabled').checked = Boolean(plan.is_enabled);
        document.getElementById('planModal').classList.remove('hidden');
    }

    function closePlanModal() {
        document.getElementById('planModal').classList.add('hidden');
    }

    // Restore tab from hash
    document.addEventListener('DOMContentLoaded', function() {
        const hash = window.location.hash;
        if (hash && hash.startsWith('#tab=')) {
            const tab = hash.replace('#tab=', '');
            switchBrochureTab(tab);
        }
    });
</script>
@endsection

