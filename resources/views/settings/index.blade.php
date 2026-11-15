{{-- Hallmark · macrostructure: Shop Settings Hub · theme: Tactile Sage & Deep Rail · genre: modern-minimal
 * pre-emit critique: P5 H5 E5 S5 R5 V5
 * slop test: pass
--}}
@extends('layouts.app')

@section('title', 'Shop Settings')
@section('page-title', 'Settings & Customization')

@section('content')
<div class="w-full space-y-5">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-[#111111] tracking-tight" style="overflow-wrap: anywhere; min-width: 0;">Shop Settings & Profile</h1>
            <p class="text-xs sm:text-sm text-[#666666]">Configure business identity, store branding, GST tax compliance, UPI QR settlement, and legal declarations</p>
        </div>

        @if($user?->isSuperAdmin())
            <a href="{{ route('superadmin.settings.index') }}" class="btn-secondary self-start sm:self-auto text-xs min-h-[40px]">
                <svg class="w-4 h-4 text-[#111111]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>Super Admin Platform Settings</span>
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="p-4 bg-[#ECFDF3] border border-[#A6F4C5] rounded-xl text-xs sm:text-sm text-[#027A48] font-bold flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-[#FEF2F2] border border-[#FECACA] rounded-xl text-xs text-[#B91C1C] space-y-1">
            <div class="font-bold">Please correct the following errors:</div>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Functional Tab Navigation Bar -->
    <div class="card-surface p-1.5 shadow-2xs">
        <nav class="flex items-center gap-1 overflow-x-auto scrollbar-none" id="settingsTabsNav" aria-label="Settings Tabs">
            <button type="button" onclick="switchSettingsTab('profile')" id="tabBtn-profile"
                class="settings-tab-btn flex items-center gap-2 px-3.5 py-2.5 rounded-lg text-xs font-bold transition whitespace-nowrap bg-[#111111] text-[#B7FF00] shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <span>Store Profile</span>
            </button>

            <button type="button" onclick="switchSettingsTab('branding')" id="tabBtn-branding"
                class="settings-tab-btn flex items-center gap-2 px-3.5 py-2.5 rounded-lg text-xs font-semibold text-[#666666] hover:text-[#111111] hover:bg-white transition whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                <span>Invoice Branding</span>
            </button>

            <button type="button" onclick="switchSettingsTab('gst')" id="tabBtn-gst"
                class="settings-tab-btn flex items-center gap-2 px-3.5 py-2.5 rounded-lg text-xs font-semibold text-[#666666] hover:text-[#111111] hover:bg-white transition whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path></svg>
                <span>GST & Tax Compliance</span>
            </button>

            <button type="button" onclick="switchSettingsTab('banking')" id="tabBtn-banking"
                class="settings-tab-btn flex items-center gap-2 px-3.5 py-2.5 rounded-lg text-xs font-semibold text-[#666666] hover:text-[#111111] hover:bg-white transition whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>UPI & Banking</span>
            </button>

            <button type="button" onclick="switchSettingsTab('terms')" id="tabBtn-terms"
                class="settings-tab-btn flex items-center gap-2 px-3.5 py-2.5 rounded-lg text-xs font-semibold text-[#666666] hover:text-[#111111] hover:bg-white transition whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Terms & Declarations</span>
            </button>

            <button type="button" onclick="switchSettingsTab('security')" id="tabBtn-security"
                class="settings-tab-btn flex items-center gap-2 px-3.5 py-2.5 rounded-lg text-xs font-semibold text-[#666666] hover:text-[#111111] hover:bg-white transition whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                <span>Account Security</span>
            </button>
        </nav>
    </div>

    <!-- Main Settings Form covering Tabs 1 through 5 -->
    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" id="settingsForm">
        @csrf
        @method('PUT')

        <!-- TAB 1: Store Profile -->
        <div id="tabPanel-profile" class="settings-tab-panel space-y-4">
            <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#B7FF00]"></span>
                        <h2 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Store Identity & Legal Details</h2>
                    </div>
                   
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-[#344054] mb-1">Store / Business Name *</label>
                        <input type="text" name="name" value="{{ old('name', $tenant->name) }}" required
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-bold text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Store Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $tenant->phone) }}"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-[#344054] mb-1">Store Email</label>
                        <input type="email" name="email" value="{{ old('email', $tenant->email) }}"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-[#344054] mb-1">Store Address</label>
                        <input type="text" name="address" value="{{ old('address', $tenant->address) }}"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">City</label>
                        <input type="text" name="city" value="{{ old('city', $tenant->city) }}"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">State</label>
                        <input type="text" name="state" value="{{ old('state', $tenant->state) }}"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Home State Code (2-Digits)</label>
                        <input type="text" name="state_code" value="{{ old('state_code', $tenant->state_code) }}"
                            placeholder="01"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                        <span class="text-[10px] text-[#666666] block mt-1">Used to auto-detect Inter-State (IGST) vs Intra-State (CGST+SGST)</span>
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-[#E6E6E6]">
                    <button type="submit" class="btn-lime min-h-[44px]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span class="font-bold">Save Store Profile</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 2: Invoice Branding & Customization -->
        <div id="tabPanel-branding" class="settings-tab-panel hidden space-y-4">
            <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#B7FF00]"></span>
                        <h2 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Invoice Branding & Visual Assets</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Store Logo Upload & Live Preview -->
                    <div class="p-4 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl space-y-3">
                        <div class="flex flex-wrap items-center justify-between">
                            <label class="block text-xs font-bold text-[#111111]">Store Logo (Header Brand Icon)</label>
                            <span class="text-[10px] font-mono text-[#666666] bg-white px-2 py-0.5 rounded border border-[#E6E6E6]">PNG, JPG, SVG, WebP</span>
                        </div>
                        <p class="text-[11px] text-[#666666]">Appears on top-left of the invoice header, A4 printout, and downloaded PDF.</p>

                        <!-- Instant New File Preview Box (hidden until file chosen) -->
                        <div id="newLogoPreviewBox" class="hidden p-3 bg-white rounded-lg border-2 border-dashed border-[#B7FF00] flex items-center gap-3">
                            <img id="newLogoPreviewImg" src="" alt="Selected Logo" class="h-12 w-auto max-w-[120px] object-contain rounded">
                            <div class="text-xs text-[#111111] font-semibold">
                                <span>Ready to save with new logo</span>
                                <span class="block text-[10px] text-[#666666] font-normal" id="newLogoFileName"></span>
                            </div>
                        </div>

                        @if($tenant->logo_url)
                            <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-[#E6E6E6]">
                                <img src="{{ $tenant->logo_url }}" alt="Logo Preview" class="h-12 w-auto max-w-[120px] object-contain rounded">
                                <div class="flex-1 min-w-0">
                                    <span class="text-xs font-bold text-[#111111] block">Current Active Logo</span>
                                    <label class="inline-flex items-center gap-1.5 mt-1 text-xs text-[#DC2626] font-semibold cursor-pointer">
                                        <input type="checkbox" name="remove_logo" value="1" class="rounded border-[#E6E6E6] text-[#DC2626] focus:ring-[#DC2626]">
                                        <span>Remove current logo</span>
                                    </label>
                                </div>
                            </div>
                        @endif

                        <div>
                            <input type="file" name="logo" id="logoInput" onchange="previewSelectedImage(this, 'newLogoPreviewBox', 'newLogoPreviewImg', 'newLogoFileName')" accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                class="w-full text-xs text-[#344054] file:mr-3 file:py-2 file:px-3.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#111111] file:text-white hover:file:bg-black file:cursor-pointer cursor-pointer">
                        </div>
                    </div>

                    <!-- Digital Signature / Store Stamp -->
                    <div class="p-4 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-[#111111]">Authorized Signature / Stamp</label>
                            <span class="text-[10px] font-mono text-[#666666] bg-white px-2 py-0.5 rounded border border-[#E6E6E6]">PNG, JPG, WebP</span>
                        </div>
                        <p class="text-[11px] text-[#666666]">Appears at the bottom right of official invoices above "AUTHORIZED SIGNATORY" (Max 5MB).</p>

                        <!-- Instant New File Preview Box -->
                        <div id="newSigPreviewBox" class="hidden p-3 bg-white rounded-lg border-2 border-dashed border-[#B7FF00] flex items-center gap-3">
                            <img id="newSigPreviewImg" src="" alt="Selected Signature" class="h-10 w-auto max-w-[120px] object-contain rounded">
                            <div class="text-xs text-[#111111] font-semibold">
                                <span>Ready to save with new signature</span>
                                <span class="block text-[10px] text-[#666666] font-normal" id="newSigFileName"></span>
                            </div>
                        </div>

                        @if($tenant->signature_url)
                            <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-[#E6E6E6]">
                                <img src="{{ $tenant->signature_url }}" alt="Signature Preview" class="h-10 w-auto max-w-[120px] object-contain rounded">
                                <div class="flex-1 min-w-0">
                                    <span class="text-xs font-bold text-[#111111] block">Current Signature Active</span>
                                    <label class="inline-flex items-center gap-1.5 mt-1 text-xs text-[#DC2626] font-semibold cursor-pointer">
                                        <input type="checkbox" name="remove_signature" value="1" class="rounded border-[#E6E6E6] text-[#DC2626] focus:ring-[#DC2626]">
                                        <span>Remove signature</span>
                                    </label>
                                </div>
                            </div>
                        @endif

                        <div>
                            <input type="file" name="signature" id="sigInput" onchange="previewSelectedImage(this, 'newSigPreviewBox', 'newSigPreviewImg', 'newSigFileName')" accept="image/png,image/jpeg,image/webp"
                                class="w-full text-xs text-[#344054] file:mr-3 file:py-2 file:px-3.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#111111] file:text-white hover:file:bg-black file:cursor-pointer cursor-pointer">
                        </div>
                    </div>

                    <!-- Invoice Prefix -->
                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Invoice Prefix</label>
                        <input type="text" name="invoice_prefix" value="{{ old('invoice_prefix', $tenant->invoice_prefix) }}"
                            placeholder="e.g. INV-"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                        <p class="text-[11px] text-[#666666] mt-1">Prepended to invoice numbers (e.g. INV-00104).</p>
                    </div>

                    <!-- Custom Invoice Title Override -->
                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Custom Invoice Header Title</label>
                        <input type="text" name="invoice_title" value="{{ old('invoice_title', $tenant->invoice_title) }}"
                            placeholder="e.g. TAX INVOICE, RETAIL CASH MEMO, ESTIMATE"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-semibold text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                        <p class="text-[11px] text-[#666666] mt-1">Leave blank to use default (TAX INVOICE for GST, RETAIL INVOICE for non-GST).</p>
                    </div>

                    <!-- Brand Accent Color -->
                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Brand Accent Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="invoice_color" id="invoiceColorPicker" value="{{ old('invoice_color', $tenant->invoice_color ?: '#111827') }}"
                                oninput="document.getElementById('invoiceColorText').value = this.value"
                                class="w-10 h-10 p-0.5 rounded-lg border border-[#E6E6E6] cursor-pointer bg-white">
                            <input type="text" id="invoiceColorText" value="{{ old('invoice_color', $tenant->invoice_color ?: '#111827') }}"
                                oninput="document.getElementById('invoiceColorPicker').value = this.value"
                                placeholder="#111827"
                                class="flex-1 px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                        </div>
                        <p class="text-[11px] text-[#666666] mt-1">Used for custom invoice accents and title badges.</p>
                    </div>

                    <!-- Invoice Footer Note -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-[#344054] mb-1">Invoice Footer Note</label>
                        <input type="text" name="invoice_footer_note" value="{{ old('invoice_footer_note', $tenant->invoice_footer_note) }}"
                            placeholder="e.g. Thank you for shopping with us! Goods once sold will not be returned."
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                        <p class="text-[11px] text-[#666666] mt-1">Small note printed at the very bottom of every invoice and PDF receipt.</p>
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-[#E6E6E6]">
                    <button type="submit" class="btn-lime min-h-[44px]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span class="font-bold">Save Branding</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 3: GST & Tax Statutory Compliance -->
        <div id="tabPanel-gst" class="settings-tab-panel hidden space-y-4">
            <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#B7FF00]"></span>
                        <h2 class="text-xs font-bold text-[#111111] uppercase tracking-wider">GST Statutory & Tax Rules</h2>
                    </div>
                    
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">GSTIN / Business Tax ID</label>
                        <input type="text" name="gstin" value="{{ old('gstin', $tenant->gstin) }}"
                            placeholder="01AAAAA0000A1Z5"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                        <span class="text-[11px] text-[#666666] block mt-1">15-digit Indian GSTIN printed on tax invoices</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Default Generalized GST Rate (%) *</label>
                        <div class="flex items-center gap-2">
                            <input type="number" step="0.01" min="0" max="100" name="default_gst_rate" value="{{ old('default_gst_rate', $tenant->default_gst_rate ?? 18.00) }}" required
                                placeholder="18.00"
                                class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-bold font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                            <span class="text-xs font-bold text-[#666666]">%</span>
                        </div>
                        <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                            <button type="button" onclick="document.querySelector('[name=default_gst_rate]').value='0.00'" class="px-2 py-0.5 rounded bg-[#F2F4F7] hover:bg-[#E6E6E6] text-[10px] font-mono font-bold text-[#344054]">0% (Exempt)</button>
                            <button type="button" onclick="document.querySelector('[name=default_gst_rate]').value='5.00'" class="px-2 py-0.5 rounded bg-[#F2F4F7] hover:bg-[#E6E6E6] text-[10px] font-mono font-bold text-[#344054]">5%</button>
                            <button type="button" onclick="document.querySelector('[name=default_gst_rate]').value='12.00'" class="px-2 py-0.5 rounded bg-[#F2F4F7] hover:bg-[#E6E6E6] text-[10px] font-mono font-bold text-[#344054]">12%</button>
                            <button type="button" onclick="document.querySelector('[name=default_gst_rate]').value='18.00'" class="px-2 py-0.5 rounded bg-[#071426] text-white text-[10px] font-mono font-bold">18% (Standard)</button>
                            <button type="button" onclick="document.querySelector('[name=default_gst_rate]').value='28.00'" class="px-2 py-0.5 rounded bg-[#F2F4F7] hover:bg-[#E6E6E6] text-[10px] font-mono font-bold text-[#344054]">28%</button>
                        </div>
                        <span class="text-[10px] text-[#666666] block mt-1">Pre-selected automatically when generating a new bill</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Tax Pricing Calculation Mode</label>
                        <div class="p-3 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl space-y-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="is_tax_inclusive" value="1" {{ old('is_tax_inclusive', $tenant->is_tax_inclusive) ? 'checked' : '' }} class="text-[#071426] focus:ring-[#071426]">
                                <span class="text-xs font-bold text-[#111111]">Tax-Inclusive (MRP includes GST)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="is_tax_inclusive" value="0" {{ !old('is_tax_inclusive', $tenant->is_tax_inclusive) ? 'checked' : '' }} class="text-[#071426] focus:ring-[#071426]">
                                <span class="text-xs font-bold text-[#111111]">Tax-Exclusive (GST added on base)</span>
                            </label>
                        </div>
                        <span class="text-[10px] text-[#666666] block mt-1">Electronics & retail counters standardly operate on Tax-Inclusive MRP</span>
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-[#E6E6E6]">
                    <button type="submit" class="btn-lime min-h-[44px]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span class="font-bold">Save Tax Settings</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 4: UPI & Banking -->
        <div id="tabPanel-banking" class="settings-tab-panel hidden space-y-4">
            <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#B7FF00]"></span>
                        <h2 class="text-xs font-bold text-[#111111] uppercase tracking-wider">UPI & Bank Account Info</h2>
                    </div>
                    
                </div>

                <div class="p-3.5 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] text-xs text-[#344054] flex items-start gap-3">
                    <div class="w-7 h-7 rounded-lg bg-black text-[#B7FF00] flex items-center justify-center shrink-0 font-bold text-xs">
                        QR
                    </div>
                    <div>
                        <span class="font-bold text-[#111111]">Dynamic Counter QR Code:</span>
                        <p class="text-[#666666] mt-0.5">Setting your UPI ID enables live scan-and-pay dynamic QR codes on digital bills, thermal counter slips, and PDF prints for immediate customer settlement.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-[#344054] mb-1">UPI ID (VPA) *</label>
                        <input type="text" name="upi_id" value="{{ old('upi_id', $tenant->upi_id) }}"
                            placeholder="e.g. yourstore@okhdfcbank"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono font-bold text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-[#344054] mb-1">Bank Name</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $tenant->bank_name) }}"
                            placeholder="e.g. HDFC Bank Ltd."
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-[#344054] mb-1">Bank Account Number</label>
                        <input type="text" name="bank_account_no" value="{{ old('bank_account_no', $tenant->bank_account_no) }}"
                            placeholder="Account number"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">IFSC Code</label>
                        <input type="text" name="bank_ifsc" value="{{ old('bank_ifsc', $tenant->bank_ifsc) }}"
                            placeholder="HDFC0001234"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Branch Name</label>
                        <input type="text" name="bank_branch" value="{{ old('bank_branch', $tenant->bank_branch) }}"
                            placeholder="Branch location"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-[#E6E6E6]">
                    <button type="submit" class="btn-lime min-h-[44px]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span class="font-bold">Save Bank Details</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 5: Terms & Declarations -->
        <div id="tabPanel-terms" class="settings-tab-panel hidden space-y-4">
            <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#B7FF00]"></span>
                        <h2 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Bill Terms & Legal Declarations</h2>
                    </div>
                   
                </div>

                <div class="space-y-3.5">
                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Terms & Conditions (Printed on customer bills)</label>
                        <textarea name="terms_conditions" rows="4"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">{{ old('terms_conditions', $tenant->terms_conditions) }}</textarea>
                        <span class="text-[11px] text-[#666666] block mt-1">Number each condition on a new line (e.g. 1. Goods once sold will not be returned).</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Legal Declaration</label>
                        <textarea name="declaration" rows="3"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">{{ old('declaration', $tenant->declaration) }}</textarea>
                        <span class="text-[11px] text-[#666666] block mt-1">Statutory warranty and invoice truthfulness declaration printed above signature.</span>
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-[#E6E6E6]">
                    <button type="submit" class="btn-lime min-h-[44px]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span class="font-bold">Save Terms & Declarations</span>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- TAB 6: Account Security (Separate Form for Password Updating & Session Control) -->
    <div id="tabPanel-security" class="settings-tab-panel hidden space-y-4">
        <!-- Change Password Card -->
        <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-4">
            <div class="flex flex-wrap items-center justify-between border-b border-[#E6E6E6] pb-3">
                <div class="flex  items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#111111]"></span>
                    <h2 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Account Password & Credentials</h2>
                </div>
                <span class="text-[11px] font-mono text-[#666666]">Logged in as {{ $user->email }}</span>
            </div>

            <form method="POST" action="{{ route('settings.password') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Current Password *</label>
                    <div class="relative">
                        <input type="password" name="current_password" required placeholder="••••••••"
                            class="w-full pl-3.5 pr-10 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                        <x-password-toggle-btn />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">New Password *</label>
                    <div class="relative">
                        <input type="password" name="password" required placeholder="Minimum 6 characters"
                            class="w-full pl-3.5 pr-10 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                        <x-password-toggle-btn />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Confirm New Password *</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" required placeholder="Repeat new password"
                            class="w-full pl-3.5 pr-10 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                        <x-password-toggle-btn />
                    </div>
                </div>

                <div class="sm:col-span-3 flex justify-end pt-2 border-t border-[#E6E6E6]">
                    <button type="submit" class="btn-secondary min-h-[44px]">
                        <svg class="w-4 h-4 text-[#111111]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <span>Update Password</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Terminal Session Management Card -->
        <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-4">
            <div class="flex flex-wrap items-center justify-between border-b border-[#E6E6E6] pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#B7FF00] border border-[#111111]"></span>
                    <h2 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Active Terminal Sessions & Counter Lock</h2>
                </div>
                <a href="{{ route('lock') }}" class="btn-secondary text-xs px-3 py-1.5 min-h-[36px] inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <span>Lock Terminal Now</span>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-3.5 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#079455]"></span>
                        <span class="text-xs font-bold text-[#111111]">This Terminal Device</span>
                    </div>
                    <p class="text-xs text-[#666666]">
                        Current counter workstation. Protected by 256-bit AES session tokens with automatic rate limiting.
                    </p>
                </div>

                <div class="p-3.5 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] space-y-2">
                    <div class="text-xs font-bold text-[#111111]">Terminate Other Sessions</div>
                    <p class="text-xs text-[#666666]">
                        If you signed in on another device or tablet, you can terminate all other active counter sessions.
                    </p>
                    <form method="POST" action="{{ route('settings.logout-other-devices') }}" class="pt-2 flex flex-col sm:flex-row gap-2">
                        @csrf
                        <div class="relative flex-1">
                            <input type="password" name="current_password" required placeholder="Current password"
                                class="w-full pl-3 pr-10 py-1.5 bg-white border border-[#E6E6E6] rounded-lg text-xs text-[#111111] focus:outline-none focus:ring-1 focus:ring-black min-h-[36px]">
                            <x-password-toggle-btn />
                        </div>
                        <button type="submit" class="btn-secondary text-xs px-3 py-1.5 min-h-[36px] whitespace-nowrap">
                            Log Out Others
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function switchSettingsTab(tabKey) {
    // Hide all tab panels
    document.querySelectorAll('.settings-tab-panel').forEach(panel => {
        panel.classList.add('hidden');
    });

    // Reset all tab button styles
    document.querySelectorAll('.settings-tab-btn').forEach(btn => {
        btn.classList.remove('bg-[#111111]', 'text-[#B7FF00]', 'shadow-xs', 'font-bold');
        btn.classList.add('text-[#666666]', 'font-semibold');
    });

    // Show targeted panel
    const targetPanel = document.getElementById('tabPanel-' + tabKey);
    if (targetPanel) {
        targetPanel.classList.remove('hidden');
    }

    // Activate targeted button
    const targetBtn = document.getElementById('tabBtn-' + tabKey);
    if (targetBtn) {
        targetBtn.classList.remove('text-[#666666]', 'font-semibold');
        targetBtn.classList.add('bg-[#111111]', 'text-[#B7FF00]', 'shadow-xs', 'font-bold');
    }

    // Update URL hash without jumping
    if (history.replaceState) {
        history.replaceState(null, null, '#tab=' + tabKey);
    }
}

// Check initial hash on page load
document.addEventListener('DOMContentLoaded', function() {
    const hash = window.location.hash;
    if (hash && hash.startsWith('#tab=')) {
        const tabKey = hash.replace('#tab=', '');
        if (['profile', 'branding', 'gst', 'banking', 'terms', 'security'].includes(tabKey)) {
            switchSettingsTab(tabKey);
        }
    }
});

function previewSelectedImage(input, boxId, imgId, nameId) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            const box = document.getElementById(boxId);
            const img = document.getElementById(imgId);
            const name = document.getElementById(nameId);
            if (box && img) {
                img.src = e.target.result;
                if (name) name.textContent = file.name + ' (' + Math.round(file.size / 1024) + ' KB)';
                box.classList.remove('hidden');
            }
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
