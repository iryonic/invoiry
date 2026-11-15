{{-- Hallmark · macrostructure: Platform System Settings Hub · theme: Tactile Sage & Deep Rail · genre: modern-minimal
 * pre-emit critique: P5 H5 E5 S5 R5 V5
 * slop test: pass
--}}
@extends('layouts.app')

@section('title', 'Platform Settings · Super Admin')
@section('page-title', 'Platform Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-4">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-[#111111] tracking-tight" style="overflow-wrap: anywhere; min-width: 0;">Platform System Settings</h1>
            <p class="text-xs sm:text-sm text-[#666666]">Configure login credentials visibility, global tenant defaults, platform branding, and maintenance mode</p>
        </div>

        <a href="{{ route('superadmin.index') }}" class="btn-secondary self-start sm:self-auto text-xs min-h-[40px]">
            Back to Console
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 bg-[#ECFDF3] border border-[#A6F4C5] rounded-xl text-xs sm:text-sm text-[#027A48] font-bold flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Functional Tab Navigation Bar -->
    <div class="card-surface p-1.5 shadow-2xs">
        <nav class="flex items-center gap-1 overflow-x-auto scrollbar-none" id="superSettingsNav" aria-label="Super Admin Tabs">
            <button type="button" onclick="switchSuperTab('auth')" id="superTabBtn-auth"
                class="super-tab-btn flex items-center gap-2 px-3.5 py-2.5 rounded-lg text-xs font-bold transition whitespace-nowrap bg-[#111111] text-[#B7FF00] shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                <span>Auth & Login Screen</span>
            </button>

            <button type="button" onclick="switchSuperTab('identity')" id="superTabBtn-identity"
                class="super-tab-btn flex items-center gap-2 px-3.5 py-2.5 rounded-lg text-xs font-semibold text-[#666666] hover:text-[#111111] hover:bg-white transition whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <span>Platform Identity & Support</span>
            </button>

            <button type="button" onclick="switchSuperTab('broadcast')" id="superTabBtn-broadcast"
                class="super-tab-btn flex items-center gap-2 px-3.5 py-2.5 rounded-lg text-xs font-semibold text-[#666666] hover:text-[#111111] hover:bg-white transition whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                <span>System Broadcast</span>
            </button>

            <button type="button" onclick="switchSuperTab('maintenance')" id="superTabBtn-maintenance"
                class="super-tab-btn flex items-center gap-2 px-3.5 py-2.5 rounded-lg text-xs font-semibold text-[#666666] hover:text-[#111111] hover:bg-white transition whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>Maintenance Controls</span>
            </button>

            <button type="button" onclick="switchSuperTab('footer')" id="superTabBtn-footer"
                class="super-tab-btn flex items-center gap-2 px-3.5 py-2.5 rounded-lg text-xs font-semibold text-[#666666] hover:text-[#111111] hover:bg-white transition whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                <span>Global Page Footer</span>
            </button>

            <button type="button" onclick="switchSuperTab('preloader')" id="superTabBtn-preloader"
                class="super-tab-btn flex items-center gap-2 px-3.5 py-2.5 rounded-lg text-xs font-semibold text-[#666666] hover:text-[#111111] hover:bg-white transition whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                <span>Preloader & Motion</span>
            </button>
        </nav>
    </div>

    <form method="POST" action="{{ route('superadmin.settings.update') }}" class="space-y-4">
        @csrf
        @method('PUT')

        <!-- TAB 1: Auth & Login Screen -->
        <div id="superTabPanel-auth" class="super-tab-panel space-y-4">
            <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-4">
                <div class="flex items-center gap-2 border-b border-[#E6E6E6] pb-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#B7FF00]"></span>
                    <h2 class="text-xs font-bold text-[#111111] uppercase tracking-wider">
                        Login Page & Pre-Credential Container Visibility
                    </h2>
                </div>

                <div class="p-4 rounded-xl bg-[#F7F7F6] border border-[#E6E6E6] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-[#111111]">Show Quick Evaluation Roles on Login Screen</span>
                            @if($settings['show_login_demo_credentials'])
                                <span class="badge-paid">Visible</span>
                            @else
                                <span class="badge-unpaid">Hidden</span>
                            @endif
                        </div>
                        <p class="text-xs text-[#666666] max-w-xl">
                            When enabled, visitors to <a href="{{ route('login') }}" target="_blank" class="font-mono text-[#111111] underline">/login</a> see the interactive "Quick Evaluation Roles" card with one-click test logins (Shop Admin, Cashier, Super Admin, Apex Store). Uncheck this for production environments to show only the honest email & password authentication form.
                        </p>
                    </div>

                    <div class="shrink-0">
                        <x-toggle-switch
                            name="show_login_demo_credentials"
                            :checked="(bool)$settings['show_login_demo_credentials']"
                            label="Evaluation Logins"
                        />
                    </div>
                </div>

                <div class="flex justify-end pt-2 border-t border-[#E6E6E6]">
                    <button type="submit" class="btn-lime min-h-[44px]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span class="font-bold">Save Settings</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 2: Platform Identity & Support -->
        <div id="superTabPanel-identity" class="super-tab-panel hidden space-y-4">
            <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-4">
                <div class="flex items-center gap-2 border-b border-[#E6E6E6] pb-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#B7FF00]"></span>
                    <h2 class="text-xs font-bold text-[#111111] uppercase tracking-wider">
                        Platform Branding & Support Identity
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-[#344054] mb-1">Platform Brand Name *</label>
                        <input type="text" name="platform_name" value="{{ old('platform_name', $settings['platform_name']) }}" required
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-bold text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Application Version *</label>
                        <input type="text" name="app_version" value="{{ old('app_version', $settings['app_version'] ?? 'v1.0.0') }}" required
                            placeholder="e.g. v1.0.0"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono font-bold text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                        <span class="text-[11px] text-[#666666] block mt-1">Single source of truth reflected across login screen and global footer.</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Default Trial Days *</label>
                        <input type="number" name="default_trial_days" value="{{ old('default_trial_days', $settings['default_trial_days']) }}" required min="1" max="365"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Central Support Email *</label>
                        <input type="email" name="support_email" value="{{ old('support_email', $settings['support_email']) }}" required
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Central Support Phone</label>
                        <input type="text" name="support_phone" value="{{ old('support_phone', $settings['support_phone']) }}"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>
                </div>

                <div class="flex justify-end pt-2 border-t border-[#E6E6E6]">
                    <button type="submit" class="btn-lime min-h-[44px]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span class="font-bold">Save Settings</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 3: System Broadcast -->
        <div id="superTabPanel-broadcast" class="super-tab-panel hidden space-y-4">
            <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-4">
                <div class="flex items-center gap-2 border-b border-[#E6E6E6] pb-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#B7FF00]"></span>
                    <h2 class="text-xs font-bold text-[#111111] uppercase tracking-wider">
                        System Announcement Notice
                    </h2>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Platform Banner (Shown at top of tenant store screens)</label>
                    <input type="text" name="announcement_banner" value="{{ old('announcement_banner', $settings['announcement_banner']) }}"
                        placeholder="e.g. Scheduled system upgrade tonight at 11:00 PM IST. Service unaffected."
                        class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    <span class="text-[11px] text-[#666666] block mt-1">Leave blank to disable banner.</span>
                </div>

                <div class="flex justify-end pt-2 border-t border-[#E6E6E6]">
                    <button type="submit" class="btn-lime min-h-[44px]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span class="font-bold">Save Settings</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 4: Maintenance Controls -->
        <div id="superTabPanel-maintenance" class="super-tab-panel hidden space-y-4">
            <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-4">
                <div class="flex items-center gap-2 border-b border-[#E6E6E6] pb-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#111111]"></span>
                    <h2 class="text-xs font-bold text-[#111111] uppercase tracking-wider">
                        Maintenance & Platform Guardrails
                    </h2>
                </div>

                <div class="flex items-center justify-between p-4 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6]">
                    <div>
                        <span class="text-xs font-bold text-[#111111] block">Maintenance Mode Flag</span>
                        <span class="text-[11px] text-[#666666]">Enable to notify users of scheduled upgrades and prevent write operations (Super Admins bypass automatically)</span>
                    </div>

                    <x-toggle-switch
                        name="maintenance_mode"
                        :checked="(bool)$settings['maintenance_mode']"
                        label="Maintenance Mode"
                    />
                </div>

                <div class="space-y-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Maintenance Window Heading</label>
                        <input type="text" name="maintenance_heading" value="{{ old('maintenance_heading', $settings['maintenance_heading']) }}"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-bold text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Maintenance Notice Message</label>
                        <textarea name="maintenance_message" rows="2"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">{{ old('maintenance_message', $settings['maintenance_message']) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Estimated Reopening Time / Schedule</label>
                        <input type="text" name="maintenance_reopen_at" value="{{ old('maintenance_reopen_at', $settings['maintenance_reopen_at']) }}"
                            placeholder="e.g. 15 minutes or 2026-11-13 02:00 AM UTC"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>
                </div>

                <div class="flex justify-end pt-2 border-t border-[#E6E6E6]">
                    <button type="submit" class="btn-lime min-h-[44px]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span class="font-bold">Save Settings</span>
                    </button>
                </div>
            </div>
        </div>
        <!-- TAB 5: Global Page Footer -->
        <div id="superTabPanel-footer" class="super-tab-panel hidden space-y-4">
            <input type="hidden" name="footer_settings_submitted" value="1">
            <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-5">
                <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#B7FF00]"></span>
                        <h2 class="text-xs font-bold text-[#111111] uppercase tracking-wider">
                            Global Page Footer Configuration
                        </h2>
                    </div>
                    <span class="text-[11px] font-mono text-[#666666]">Appears on all authenticated workspace & terminal pages</span>
                </div>

                <!-- Footer Visibility Switch -->
                <div class="p-4 rounded-xl bg-[#F7F7F6] border border-[#E6E6E6] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-[#111111]">Enable Global Footer</span>
                            @if($settings['footer_enabled'])
                                <span class="badge-paid">Enabled Globally</span>
                            @else
                                <span class="badge-unpaid">Hidden</span>
                            @endif
                        </div>
                        <p class="text-xs text-[#666666] max-w-xl">
                            When enabled, this footer is rendered across all tenant workspaces, POS terminals, store admin screens, and super admin consoles. Authentication pages (/login, /register, etc.) are always excluded for focused checkout security.
                        </p>
                    </div>

                    <div class="shrink-0">
                        <x-toggle-switch
                            name="footer_enabled"
                            :checked="(bool)$settings['footer_enabled']"
                            label="Global Footer"
                        />
                    </div>
                </div>

                <!-- Live Preview of Footer -->
                <div class="p-4 rounded-xl bg-white border border-[#E6E6E6] space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-[#929292] uppercase font-mono tracking-wider">Live Footer Preview</span>
                        <span class="text-[10px] text-[#666666] font-mono">Real-time styling snapshot</span>
                    </div>
                    <div class="p-4 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] text-xs text-[#666666]">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-bold text-[#111111]" id="preview_footer_copyright">{{ str_replace('{year}', date('Y'), $settings['footer_copyright']) }}</span>
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-[#050505] text-[#B7FF00] text-[10px] font-mono font-bold tracking-wide" id="preview_footer_badge">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#B7FF00] animate-pulse"></span>
                                        <span id="preview_footer_badge_text">{{ $settings['footer_badge_text'] ?: 'v2.4 Live POS' }}</span>
                                    </span>
                                </div>
                                <p class="text-[11px] text-[#666666]" id="preview_footer_tagline">{{ $settings['footer_tagline'] }}</p>
                                <p class="text-[11px] font-mono text-[#737373]" id="preview_footer_contact">{{ $settings['footer_contact_info'] }}</p>
                            </div>
                            <div class="flex items-center gap-3 text-[11px]">
                                <span class="hover:underline text-[#111111]" id="preview_footer_link1">{{ $settings['footer_link_1_text'] ?: 'Privacy Policy' }}</span>
                                <span class="text-[#D6D6D6]">&bull;</span>
                                <span class="hover:underline text-[#111111]" id="preview_footer_link2">{{ $settings['footer_link_2_text'] ?: 'Terms of Service' }}</span>
                                <span class="text-[#D6D6D6]">&bull;</span>
                                <span class="hover:underline text-[#111111]" id="preview_footer_link3">{{ $settings['footer_link_3_text'] ?: 'Help & Documentation' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Controls -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-[#344054] mb-1">Copyright Line *</label>
                        <input type="text" name="footer_copyright" id="input_footer_copyright" value="{{ old('footer_copyright', $settings['footer_copyright']) }}" required
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                        <span class="text-[11px] text-[#666666] block mt-1 font-mono">Use <code class="text-[#111111] font-bold">{year}</code> for auto current year and <code class="text-[#111111] font-bold">{shop}</code> for the active store name.</span>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-[#344054] mb-1">Footer Brand Tagline / Mission Note</label>
                        <textarea name="footer_tagline" id="input_footer_tagline" rows="2"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">{{ old('footer_tagline', $settings['footer_tagline']) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Support Contact / Hotline Details</label>
                        <input type="text" name="footer_contact_info" id="input_footer_contact" value="{{ old('footer_contact_info', $settings['footer_contact_info']) }}"
                            placeholder="e.g. Central Support: support@invoicepos.com · +91 7006079473"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Status Badge / System Version</label>
                        <input type="text" name="footer_badge_text" id="input_footer_badge" value="{{ old('footer_badge_text', $settings['footer_badge_text']) }}"
                            placeholder="e.g. v2.4 Live POS"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>
                </div>

                <!-- Footer Navigation Links -->
                <div class="border-t border-[#E6E6E6] pt-4 space-y-3">
                    <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Footer Navigation Links</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="space-y-1.5 p-3 rounded-xl bg-[#F7F7F6] border border-[#E6E6E6]">
                            <span class="text-[11px] font-bold text-[#111111] block">Link 1</span>
                            <input type="text" name="footer_link_1_text" id="input_footer_link1" value="{{ old('footer_link_1_text', $settings['footer_link_1_text']) }}" placeholder="Label (e.g. Privacy Policy)"
                                class="w-full px-3 py-1.5 bg-white border border-[#E6E6E6] rounded-lg text-xs text-[#111111] focus:outline-none focus:ring-1 focus:ring-black">
                            <input type="text" name="footer_link_1_url" value="{{ old('footer_link_1_url', $settings['footer_link_1_url']) }}" placeholder="URL (e.g. # or /privacy)"
                                class="w-full px-3 py-1.5 bg-white border border-[#E6E6E6] rounded-lg text-xs font-mono text-[#111111] focus:outline-none focus:ring-1 focus:ring-black">
                        </div>

                        <div class="space-y-1.5 p-3 rounded-xl bg-[#F7F7F6] border border-[#E6E6E6]">
                            <span class="text-[11px] font-bold text-[#111111] block">Link 2</span>
                            <input type="text" name="footer_link_2_text" id="input_footer_link2" value="{{ old('footer_link_2_text', $settings['footer_link_2_text']) }}" placeholder="Label (e.g. Terms of Service)"
                                class="w-full px-3 py-1.5 bg-white border border-[#E6E6E6] rounded-lg text-xs text-[#111111] focus:outline-none focus:ring-1 focus:ring-black">
                            <input type="text" name="footer_link_2_url" value="{{ old('footer_link_2_url', $settings['footer_link_2_url']) }}" placeholder="URL (e.g. # or /terms)"
                                class="w-full px-3 py-1.5 bg-white border border-[#E6E6E6] rounded-lg text-xs font-mono text-[#111111] focus:outline-none focus:ring-1 focus:ring-black">
                        </div>

                        <div class="space-y-1.5 p-3 rounded-xl bg-[#F7F7F6] border border-[#E6E6E6]">
                            <span class="text-[11px] font-bold text-[#111111] block">Link 3</span>
                            <input type="text" name="footer_link_3_text" id="input_footer_link3" value="{{ old('footer_link_3_text', $settings['footer_link_3_text']) }}" placeholder="Label (e.g. Documentation)"
                                class="w-full px-3 py-1.5 bg-white border border-[#E6E6E6] rounded-lg text-xs text-[#111111] focus:outline-none focus:ring-1 focus:ring-black">
                            <input type="text" name="footer_link_3_url" value="{{ old('footer_link_3_url', $settings['footer_link_3_url']) }}" placeholder="URL (e.g. # or /docs)"
                                class="w-full px-3 py-1.5 bg-white border border-[#E6E6E6] rounded-lg text-xs font-mono text-[#111111] focus:outline-none focus:ring-1 focus:ring-black">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-[#E6E6E6]">
                    <button type="submit" class="btn-lime min-h-[44px]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span class="font-bold">Save Settings</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 6: Preloader & Animation Controls -->
        <div id="superTabPanel-preloader" class="super-tab-panel hidden space-y-4">
            <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-5">
                <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#80F644]"></span>
                        <h2 class="text-xs font-bold text-[#111111] uppercase tracking-wider">
                            Full-Screen Page Preloader & Motion Controls
                        </h2>
                    </div>
                    <span class="text-[11px] font-mono text-[#666666]">Lime (#80F644) dot & White square rotating animation</span>
                </div>

                <!-- Preloader Switch -->
                <div class="p-4 rounded-xl bg-[#F7F7F6] border border-[#E6E6E6] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-[#111111]">Enable Full-Screen Preloader</span>
                            @if($settings['preloader_enabled'])
                                <span class="badge-paid">Active</span>
                            @else
                                <span class="badge-unpaid">Disabled</span>
                            @endif
                        </div>
                        <p class="text-xs text-[#666666] max-w-xl">
                            Displays the bouncing-dot and rotating-square preloader overlay upon initial application load and major transitions, gracefully fading out once DOM resources are ready.
                        </p>
                    </div>

                    <div class="shrink-0">
                        <x-toggle-switch
                            name="preloader_enabled"
                            :checked="(bool)$settings['preloader_enabled']"
                            label="Preloader"
                        />
                    </div>
                </div>

                <!-- Timing Controls -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 rounded-xl bg-white border border-[#E6E6E6] space-y-2">
                        <label class="block text-xs font-bold text-[#344054]">Minimum Display Duration (ms)</label>
                        <input type="number" name="preloader_min_time" value="{{ old('preloader_min_time', $settings['preloader_min_time']) }}" min="0" max="5000" step="50"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                        <span class="text-[11px] text-[#666666] block">Default: 0ms (dismiss as soon as DOM is ready without artificial delays).</span>
                    </div>

                    <div class="p-4 rounded-xl bg-white border border-[#E6E6E6] space-y-2">
                        <label class="block text-xs font-bold text-[#344054]">Safety Timeout Ceiling (ms)</label>
                        <input type="number" name="preloader_max_time" value="{{ old('preloader_max_time', $settings['preloader_max_time']) }}" min="500" max="10000" step="100"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                        <span class="text-[11px] text-[#666666] block">Guarantees the loader never gets stuck if a background script stalls.</span>
                    </div>

                    <div class="p-4 rounded-xl bg-white border border-[#E6E6E6] space-y-2">
                        <label class="block text-xs font-bold text-[#344054]">Fade-Out Transition (ms)</label>
                        <input type="number" name="preloader_fade_duration" value="{{ old('preloader_fade_duration', $settings['preloader_fade_duration']) }}" min="50" max="2000" step="50"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                        <span class="text-[11px] text-[#666666] block">CSS opacity animation duration when revealing the application.</span>
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-[#E6E6E6]">
                    <button type="submit" class="btn-lime min-h-[44px]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span class="font-bold">Save Settings</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function switchSuperTab(tabKey) {
    document.querySelectorAll('.super-tab-panel').forEach(panel => {
        panel.classList.add('hidden');
    });

    document.querySelectorAll('.super-tab-btn').forEach(btn => {
        btn.classList.remove('bg-[#111111]', 'text-[#B7FF00]', 'shadow-xs', 'font-bold');
        btn.classList.add('text-[#666666]', 'font-semibold');
    });

    const targetPanel = document.getElementById('superTabPanel-' + tabKey);
    if (targetPanel) {
        targetPanel.classList.remove('hidden');
    }

    const targetBtn = document.getElementById('superTabBtn-' + tabKey);
    if (targetBtn) {
        targetBtn.classList.remove('text-[#666666]', 'font-semibold');
        targetBtn.classList.add('bg-[#111111]', 'text-[#B7FF00]', 'shadow-xs', 'font-bold');
    }

    if (history.replaceState) {
        history.replaceState(null, null, '#tab=' + tabKey);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const hash = window.location.hash;
    if (hash && hash.startsWith('#tab=')) {
        const tabKey = hash.replace('#tab=', '');
        if (['auth', 'identity', 'broadcast', 'maintenance', 'footer', 'preloader'].includes(tabKey)) {
            switchSuperTab(tabKey);
        }
    }

    // Live preview sync for footer tab inputs
    const copyInput = document.getElementById('input_footer_copyright');
    if (copyInput) {
        copyInput.addEventListener('input', function(e) {
            const yr = new Date().getFullYear();
            document.getElementById('preview_footer_copyright').textContent = e.target.value.replace('{year}', yr);
        });
    }

    const taglineInput = document.getElementById('input_footer_tagline');
    if (taglineInput) {
        taglineInput.addEventListener('input', function(e) {
            document.getElementById('preview_footer_tagline').textContent = e.target.value;
        });
    }

    const contactInput = document.getElementById('input_footer_contact');
    if (contactInput) {
        contactInput.addEventListener('input', function(e) {
            document.getElementById('preview_footer_contact').textContent = e.target.value;
        });
    }

    const badgeInput = document.getElementById('input_footer_badge');
    if (badgeInput) {
        badgeInput.addEventListener('input', function(e) {
            document.getElementById('preview_footer_badge_text').textContent = e.target.value || 'v2.4 Live POS';
        });
    }

    const link1Input = document.getElementById('input_footer_link1');
    if (link1Input) {
        link1Input.addEventListener('input', function(e) {
            document.getElementById('preview_footer_link1').textContent = e.target.value || 'Privacy Policy';
        });
    }

    const link2Input = document.getElementById('input_footer_link2');
    if (link2Input) {
        link2Input.addEventListener('input', function(e) {
            document.getElementById('preview_footer_link2').textContent = e.target.value || 'Terms of Service';
        });
    }

    const link3Input = document.getElementById('input_footer_link3');
    if (link3Input) {
        link3Input.addEventListener('input', function(e) {
            document.getElementById('preview_footer_link3').textContent = e.target.value || 'Help & Documentation';
        });
    }
});
</script>
@endsection
