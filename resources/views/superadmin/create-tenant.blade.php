{{-- Hallmark · macrostructure: Multi-Step Tenant Onboarding Wizard · theme: Tactile Sage & Deep Rail · genre: modern-minimal
 * pre-emit critique: P5 H5 E5 S5 R5 V5
 * slop test: pass
--}}
@extends('layouts.app')

@section('title', 'Onboard Business & Configure Permissions')
@section('page-title', 'Onboard Business')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">

    <!-- Header & Navigation -->
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-[#111111] tracking-tight">Onboard New Business Store</h1>
            <p class="text-xs sm:text-sm text-[#666666]">Configure tenant store profile, RBAC feature permissions, and custom form inputs</p>
        </div>
        <a href="{{ route('superadmin.tenants.index') }}" class="btn-secondary min-h-[38px] text-xs inline-flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Back to Stores</span>
        </a>
    </div>

    <!-- Stepper Navigation Bar -->
    <div class="card-surface p-3 sm:p-4 shadow-2xs border border-[#E6E6E6] rounded-xl bg-white overflow-x-auto">
        <div class="flex items-center justify-between min-w-[560px] gap-2">
            <!-- Step 1 Pill -->
            <button type="button" onclick="goToStep(1)" id="step_pill_1" class="step-pill flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs font-bold transition bg-[#111111] text-[#B7FF00]">
                <span class="w-5 h-5 rounded-full bg-[#B7FF00] text-[#101010] flex items-center justify-center text-[10px] font-black">1</span>
                <span>Basic Information</span>
            </button>
            <div class="h-px bg-[#E6E6E6] flex-1"></div>

            <!-- Step 2 Pill -->
            <button type="button" onclick="goToStep(2)" id="step_pill_2" class="step-pill flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs font-bold transition text-[#666666] hover:text-[#111111]">
                <span class="w-5 h-5 rounded-full bg-[#E6E6E6] text-[#333333] flex items-center justify-center text-[10px] font-bold">2</span>
                <span>Permissions (RBAC)</span>
            </button>
            <div class="h-px bg-[#E6E6E6] flex-1"></div>

            <!-- Step 3 Pill -->
            <button type="button" onclick="goToStep(3)" id="step_pill_3" class="step-pill flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs font-bold transition text-[#666666] hover:text-[#111111]">
                <span class="w-5 h-5 rounded-full bg-[#E6E6E6] text-[#333333] flex items-center justify-center text-[10px] font-bold">3</span>
                <span>Form Customization</span>
            </button>
            <div class="h-px bg-[#E6E6E6] flex-1"></div>

            <!-- Step 4 Pill -->
            <button type="button" onclick="goToStep(4)" id="step_pill_4" class="step-pill flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs font-bold transition text-[#666666] hover:text-[#111111]">
                <span class="w-5 h-5 rounded-full bg-[#E6E6E6] text-[#333333] flex items-center justify-center text-[10px] font-bold">4</span>
                <span>Review & Launch</span>
            </button>
        </div>
    </div>

    @if($errors->any())
        <div class="p-4 bg-[#FEF2F2] border border-[#FECACA] rounded-xl text-xs text-[#B91C1C] space-y-1.5 shadow-xs">
            <div class="font-bold flex items-center gap-2 text-sm text-[#991B1B]">
                <svg class="w-4 h-4 text-[#DC2626] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span>Please correct the following fields before proceeding:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-[11px] font-medium text-[#B91C1C] pl-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const errorFields = {
                    'name': { input: 'input_store_name', err: 'err_msg_store_name' },
                    'email': { input: 'input_email', err: 'err_msg_email' },
                    'phone': { input: 'input_phone', err: 'err_msg_phone' },
                    'admin_name': { input: 'input_admin_name', err: 'err_msg_admin_name' },
                    'password': { input: 'input_password', err: 'err_msg_password' }
                };

                @foreach($errors->keys() as $key)
                    if (errorFields['{{ $key }}']) {
                        const target = errorFields['{{ $key }}'];
                        const inputEl = document.getElementById(target.input);
                        const errEl = document.getElementById(target.err);
                        if (inputEl) {
                            inputEl.classList.add('input-validation-error', 'border-[#DC2626]', 'ring-2', 'ring-[#DC2626]/20', 'bg-[#FEF2F2]');
                        }
                        if (errEl) {
                            errEl.textContent = '{{ $errors->first($key) }}';
                            errEl.classList.remove('hidden');
                        }
                    }
                @endforeach

                // Auto-focus first error field
                const firstInvalid = document.querySelector('.input-validation-error');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalid.focus();
                }
            });
        </script>
    @endif

    <!-- Real-time Guided Validation Alert Box -->
    <div id="stepValidationAlert" class="hidden p-4 rounded-xl bg-[#FEF2F2] border border-[#FECACA] text-xs text-[#B91C1C] flex items-start gap-3 shadow-xs transition duration-200">
        <div class="w-6 h-6 rounded-lg bg-[#DC2626] text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <div class="flex-1 min-w-0">
            <div class="font-bold text-sm text-[#991B1B]" id="stepValidationErrorTitle">Action Required</div>
            <p class="mt-0.5 leading-relaxed text-[#B91C1C]" id="stepValidationErrorMessage">Please provide the missing information highlighted below.</p>
        </div>
        <button type="button" onclick="document.getElementById('stepValidationAlert').classList.add('hidden')" class="text-[#B91C1C] hover:text-black font-bold p-1 text-base">&times;</button>
    </div>

    <!-- Main Multi-Step Form -->
    <form id="onboardForm" method="POST" action="{{ route('superadmin.tenants.store') }}" class="space-y-4">
        @csrf

        <!-- ================= STEP 1: Basic Store & Admin Information ================= -->
        <div id="step_section_1" class="step-pane space-y-4">
            <!-- Store Profile Card -->
            <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-4 border border-[#E6E6E6] rounded-xl bg-white">
                <div class="flex items-center gap-2 border-b border-[#E6E6E6] pb-3">
                    <span class="w-2 h-2 rounded-full bg-[#B7FF00]"></span>
                    <h2 class="text-xs font-bold text-[#111111] uppercase tracking-wider">
                        Business Store Identity
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-[#344054] mb-1">Business / Store Name *</label>
                        <input type="text" id="input_store_name" name="name" value="{{ old('name') }}" required
                            placeholder="e.g. Apex Electronics, Metro Mobile Hub"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-bold text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] transition">
                        <p class="text-[11px] text-[#DC2626] font-semibold mt-1 hidden" id="err_msg_store_name"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Subscription Plan *</label>
                        <select id="input_plan" name="plan" class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-bold text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                            <option value="trial">Free Trial (14 Days)</option>
                            <option value="starter">Starter Plan</option>
                            <option value="pro" selected>Pro Business</option>
                            <option value="enterprise">Enterprise Multi-Store</option>
                        </select>
                    </div>

                    <!-- Business Type Selection (Mandatory) -->
                    <div class="sm:col-span-3 pt-2">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-[#111111] uppercase tracking-wider">Business Type *</label>
                            <span class="text-[11px] text-[#666666]">Determines operational modules, inventory structures, and dynamic forms</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <!-- Option 1: Mobile Selling & Reselling -->
                            <label class="relative flex flex-col p-3.5 rounded-xl border border-[#E6E6E6] bg-[#F7F7F6] hover:bg-white cursor-pointer transition has-[:checked]:border-[#111111] has-[:checked]:bg-white has-[:checked]:ring-2 has-[:checked]:ring-[#111111]">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-[#EFF8FF] text-[#175CD3] flex items-center justify-center font-bold text-xs border border-[#B2DDFF]">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <span class="text-xs font-black text-[#111111]">Mobile Selling &amp; Reselling</span>
                                    </div>
                                    <input type="radio" name="business_type" value="selling" {{ old('business_type', 'selling') === 'selling' ? 'checked' : '' }} onchange="handleBusinessTypeChange(this.value)" required class="mt-0.5 text-[#111111] focus:ring-black">
                                </div>
                                <p class="text-[11px] text-[#666666] mt-2 leading-relaxed">
                                    Focuses on complete mobile phones with brand, model, storage, color, and dual IMEI 1/2 tracking.
                                </p>
                                <div class="mt-2 text-[10px] font-mono text-[#175CD3] bg-[#EFF8FF] px-2 py-0.5 rounded w-fit border border-[#B2DDFF]">
                                    Devices &bull; IMEIs &bull; Sales POS
                                </div>
                            </label>

                            <!-- Option 2: Mobile Repair -->
                            <label class="relative flex flex-col p-3.5 rounded-xl border border-[#E6E6E6] bg-[#F7F7F6] hover:bg-white cursor-pointer transition has-[:checked]:border-[#111111] has-[:checked]:bg-white has-[:checked]:ring-2 has-[:checked]:ring-[#111111]">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-[#FEF0C7] text-[#B54708] flex items-center justify-center font-bold text-xs border border-[#FEDF89]">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                                        </div>
                                        <span class="text-xs font-black text-[#111111]">Mobile Repair</span>
                                    </div>
                                    <input type="radio" name="business_type" value="repair" {{ old('business_type') === 'repair' ? 'checked' : '' }} onchange="handleBusinessTypeChange(this.value)" required class="mt-0.5 text-[#111111] focus:ring-black">
                                </div>
                                <p class="text-[11px] text-[#666666] mt-2 leading-relaxed">
                                    For repair workshops managing spare parts (screens, batteries, ICs), customer service jobs, and technicians.
                                </p>
                                <div class="mt-2 text-[10px] font-mono text-[#B54708] bg-[#FEF0C7] px-2 py-0.5 rounded w-fit border border-[#FEDF89]">
                                    Service Orders &bull; Spare Parts &bull; Technicians
                                </div>
                            </label>

                            <!-- Option 3: Both Selling & Repair -->
                            <label class="relative flex flex-col p-3.5 rounded-xl border border-[#E6E6E6] bg-[#F7F7F6] hover:bg-white cursor-pointer transition has-[:checked]:border-[#111111] has-[:checked]:bg-white has-[:checked]:ring-2 has-[:checked]:ring-[#111111]">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-[#ECFDF3] text-[#027A48] flex items-center justify-center font-bold text-xs border border-[#A6F4C5]">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        </div>
                                        <span class="text-xs font-black text-[#111111]">Both Selling &amp; Repair</span>
                                    </div>
                                    <input type="radio" name="business_type" value="both" {{ old('business_type') === 'both' ? 'checked' : '' }} onchange="handleBusinessTypeChange(this.value)" required class="mt-0.5 text-[#111111] focus:ring-black">
                                </div>
                                <p class="text-[11px] text-[#666666] mt-2 leading-relaxed">
                                    Full dual capabilities: separate device catalogue &amp; spare parts inventory, with unified billing and customer Khata.
                                </p>
                                <div class="mt-2 text-[10px] font-mono text-[#027A48] bg-[#ECFDF3] px-2 py-0.5 rounded w-fit border border-[#A6F4C5]">
                                    Complete Dual Suite &bull; Unified Ledger
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Store Official Email *</label>
                        <input type="email" id="input_email" name="email" value="{{ old('email') }}" required
                            placeholder="store@domain.com"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] transition">
                        <p class="text-[11px] text-[#DC2626] font-semibold mt-1 hidden" id="err_msg_email"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Store Phone *</label>
                        <input type="text" id="input_phone" name="phone" value="{{ old('phone') }}" required
                            placeholder="10 Digits"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] transition">
                        <p class="text-[11px] text-[#DC2626] font-semibold mt-1 hidden" id="err_msg_phone"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Store GSTIN / UIN</label>
                        <input type="text" name="gstin" value="{{ old('gstin') }}"
                            placeholder="e.g. 01ABCDE1234F1Z5"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-[#344054] mb-1">Store Address</label>
                        <input type="text" name="address" value="{{ old('address') }}"
                            placeholder="Shop Address & Market locality..."
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">City</label>
                        <input type="text" name="city" value="{{ old('city') }}" placeholder="City"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">UPI ID for Counter QR</label>
                        <input type="text" name="upi_id" value="{{ old('upi_id') }}"
                            placeholder="shop@okaxis"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>
                </div>
            </div>

            <!-- Dedicated Tenant Admin Card -->
            <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-4 border border-[#E6E6E6] rounded-xl bg-white">
                <div class="flex items-center gap-2 border-b border-[#E6E6E6] pb-3">
                    <span class="w-2 h-2 rounded-full bg-[#111111]"></span>
                    <h2 class="text-xs font-bold text-[#111111] uppercase tracking-wider">
                        Dedicated Store Admin Login Credentials
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Admin Full Name *</label>
                        <input type="text" id="input_admin_name" name="admin_name" value="{{ old('admin_name') }}" required
                            placeholder="e.g. Malik Haris"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] transition">
                        <p class="text-[11px] text-[#DC2626] font-semibold mt-1 hidden" id="err_msg_admin_name"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Admin Password *</label>
                        <div class="relative">
                            <input type="password" id="input_password" name="password" required placeholder="Minimum 6 characters"
                                class="w-full pl-3.5 pr-10 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] transition">
                            <x-password-toggle-btn target="input_password" />
                        </div>
                        <p class="text-[11px] text-[#DC2626] font-semibold mt-1 hidden" id="err_msg_password"></p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('superadmin.tenants.index') }}" class="btn-secondary min-h-[44px]">Cancel</a>
                <button type="button" onclick="goToStep(2)" class="btn-lime min-h-[44px] px-6">
                    <span>Continue to Permissions</span>
                </button>
            </div>
        </div>

        <!-- ================= STEP 2: Feature & Permission Configuration ================= -->
        <div id="step_section_2" class="step-pane hidden space-y-4">
            @include('superadmin.partials.permissions-form')

            <div class="flex items-center justify-between gap-3 pt-4 border-t border-[#E6E6E6]">
                <button type="button" onclick="goToStep(1)" class="btn-secondary min-h-[44px]">
                    Back to Details
                </button>
                <button type="button" onclick="goToStep(3)" class="btn-lime min-h-[44px] px-6">
                    <span>Continue to Form Customization</span>
                </button>
            </div>
        </div>

        <!-- ================= STEP 3: Form Field Customization ================= -->
        <div id="step_section_3" class="step-pane hidden space-y-4">
            @include('superadmin.partials.form-fields-config')

            <div class="flex items-center justify-between gap-3 pt-4 border-t border-[#E6E6E6]">
                <button type="button" onclick="goToStep(2)" class="btn-secondary min-h-[44px]">
                    Back to Permissions
                </button>
                <button type="button" onclick="goToStep(4)" class="btn-lime min-h-[44px] px-6">
                    <span>Review & Confirmation</span>
                </button>
            </div>
        </div>

        <!-- ================= STEP 4: Review & Launch ================= -->
        <div id="step_section_4" class="step-pane hidden space-y-4">
            <div class="card-surface p-6 shadow-2xs border border-[#E6E6E6] rounded-xl bg-white space-y-5">
                <div class="flex items-center gap-2 border-b border-[#E6E6E6] pb-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#079455] animate-pulse"></span>
                    <h2 class="text-sm font-bold text-[#111111] uppercase tracking-wider">
                        Review Store Configuration Before Creation
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Store Summary -->
                    <div class="p-4 rounded-xl bg-[#F7F7F6] border border-[#E6E6E6] space-y-2">
                        <span class="text-[10px] font-bold text-[#666666] uppercase tracking-wider block">Store Information</span>
                        <div class="text-base font-black text-[#111111]" id="rev_store_name">Store Name</div>
                        <div class="text-xs text-[#444444] space-y-1">
                            <p>Official Email: <strong id="rev_email">email@store.com</strong></p>
                            <p>Phone: <strong id="rev_phone">Phone</strong></p>
                            <p>Plan: <strong id="rev_plan" class="uppercase text-[#079455]">PRO</strong></p>
                            <p>Business Type: <strong id="rev_business_type" class="text-[#111111] font-bold">Mobile Selling &amp; Reselling</strong></p>
                        </div>
                    </div>

                    <!-- Admin Summary -->
                    <div class="p-4 rounded-xl bg-[#F7F7F6] border border-[#E6E6E6] space-y-2">
                        <span class="text-[10px] font-bold text-[#666666] uppercase tracking-wider block">Admin User Credentials</span>
                        <div class="text-base font-black text-[#111111]" id="rev_admin_name">Admin Name</div>
                        <div class="text-xs text-[#444444] space-y-1">
                            <p>Login ID: <strong id="rev_admin_email">email@store.com</strong></p>
                            <p>Role: <strong class="text-[#111111]">Store Administrator (Tenant Admin)</strong></p>
                            <p>Password: <strong class="text-[#079455]">&bull;&bull;&bull;&bull;&bull;&bull; (Configured)</strong></p>
                        </div>
                    </div>
                </div>

                <!-- Governance Summary Status -->
                <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] space-y-1.5 text-xs text-[#334155]">
                    <div class="flex items-center gap-2 font-bold text-[#071426]">
                        <svg class="w-4 h-4 text-[#079455]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span>Granular Permissions & Form Fields Attached</span>
                    </div>
                    <p class="text-[11px] text-[#475569]">
                        All selected module permissions and custom form field visibilities will be saved atomically with this store. You can modify these settings anytime later from the Super Admin Console.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-between gap-3 pt-2">
                <button type="button" onclick="goToStep(3)" class="btn-secondary min-h-[44px]">
                    Back to Form Fields
                </button>
                <button type="submit" id="btnSubmitOnboard" class="btn-lime min-h-[44px] px-8 text-sm font-bold shadow-xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    <span>Create Tenant &amp; Launch Business</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    let currentStep = 1;

    function clearAllErrors() {
        document.querySelectorAll('.input-validation-error').forEach(el => {
            el.classList.remove('input-validation-error', 'border-[#DC2626]', 'ring-2', 'ring-[#DC2626]/20', 'bg-[#FEF2F2]');
        });
        document.querySelectorAll('[id^="err_msg_"]').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });
        const alertBox = document.getElementById('stepValidationAlert');
        if (alertBox) alertBox.classList.add('hidden');
    }

    function showFieldError(inputId, errSpanId, message, fieldLabel) {
        const input = document.getElementById(inputId);
        const errSpan = document.getElementById(errSpanId);
        const alertBox = document.getElementById('stepValidationAlert');
        const titleEl = document.getElementById('stepValidationErrorTitle');
        const msgEl = document.getElementById('stepValidationErrorMessage');

        if (alertBox && msgEl) {
            if (titleEl) titleEl.textContent = fieldLabel ? `Missing / Invalid: ${fieldLabel}` : 'Required Field Missing';
            msgEl.textContent = message;
            alertBox.classList.remove('hidden');
        }

        if (errSpan) {
            errSpan.textContent = message;
            errSpan.classList.remove('hidden');
        }

        if (input) {
            input.classList.add('input-validation-error', 'border-[#DC2626]', 'ring-2', 'ring-[#DC2626]/20', 'bg-[#FEF2F2]');
            input.scrollIntoView({ behavior: 'smooth', block: 'center' });
            input.focus();

            const clearHandler = function() {
                input.classList.remove('input-validation-error', 'border-[#DC2626]', 'ring-2', 'ring-[#DC2626]/20', 'bg-[#FEF2F2]');
                if (errSpan) errSpan.classList.add('hidden');
                if (alertBox) alertBox.classList.add('hidden');
                input.removeEventListener('input', clearHandler);
                input.removeEventListener('change', clearHandler);
            };
            input.addEventListener('input', clearHandler);
            input.addEventListener('change', clearHandler);
        }
    }

    function validateStep1() {
        clearAllErrors();

        // 1. Store Name
        const nameInput = document.getElementById('input_store_name');
        if (!nameInput || !nameInput.value.trim()) {
            showFieldError('input_store_name', 'err_msg_store_name', 'Please enter the Business or Store Name to identify this store.', 'Store Name');
            return false;
        }

        // 2. Business Type
        const bTypeChecked = document.querySelector('input[name="business_type"]:checked');
        if (!bTypeChecked) {
            const alertBox = document.getElementById('stepValidationAlert');
            const msgEl = document.getElementById('stepValidationErrorMessage');
            if (alertBox && msgEl) {
                msgEl.textContent = 'Please choose a Business Type (Mobile Selling, Mobile Repair, or Both Selling & Repair).';
                alertBox.classList.remove('hidden');
            }
            return false;
        }

        // 3. Official Email
        const emailInput = document.getElementById('input_email');
        const emailVal = emailInput ? emailInput.value.trim() : '';
        if (!emailVal) {
            showFieldError('input_email', 'err_msg_email', 'Please enter the Store Official Email address.', 'Store Official Email');
            return false;
        }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailVal)) {
            showFieldError('input_email', 'err_msg_email', 'Please enter a valid email format (e.g. store@domain.com).', 'Store Official Email');
            return false;
        }

        // 4. Store Phone
        const phoneInput = document.getElementById('input_phone');
        const phoneVal = phoneInput ? phoneInput.value.trim().replace(/[^0-9]/g, '') : '';
        if (!phoneVal) {
            showFieldError('input_phone', 'err_msg_phone', 'Please enter the Store Phone Number for counter contact & receipts.', 'Store Phone');
            return false;
        }
        if (phoneVal.length < 10) {
            showFieldError('input_phone', 'err_msg_phone', 'Phone number must contain at least 10 numeric digits.', 'Store Phone');
            return false;
        }

        // 5. Admin Full Name
        const adminNameInput = document.getElementById('input_admin_name');
        if (!adminNameInput || !adminNameInput.value.trim()) {
            showFieldError('input_admin_name', 'err_msg_admin_name', 'Please enter the Administrator Full Name for initial login.', 'Admin Full Name');
            return false;
        }

        // 6. Admin Password
        const passInput = document.getElementById('input_password');
        if (!passInput || !passInput.value) {
            showFieldError('input_password', 'err_msg_password', 'Please set an initial login password for the store administrator.', 'Admin Password');
            return false;
        }
        if (passInput.value.length < 6) {
            showFieldError('input_password', 'err_msg_password', 'Password must be at least 6 characters in length.', 'Admin Password');
            return false;
        }

        return true;
    }

    function handleBusinessTypeChange(bType) {
        if (!bType) return;

        // Dynamically filter permissions in Step 2
        if (typeof filterPermissionsByBusinessType === 'function') {
            filterPermissionsByBusinessType(bType);
        }

        // Dynamically filter form field customization tabs in Step 3
        if (typeof filterFormTabsByBusinessType === 'function') {
            filterFormTabsByBusinessType(bType);
        }

        // Preset default permissions appropriate for the chosen business type
        if (bType === 'selling') {
            document.querySelectorAll('input[name^="permissions[items]"]').forEach(cb => cb.checked = true);
            document.querySelectorAll('input[name^="permissions[repairs]"]').forEach(cb => cb.checked = false);
            document.querySelectorAll('input[name^="permissions[spare_parts]"]').forEach(cb => cb.checked = false);
        } else if (bType === 'repair') {
            document.querySelectorAll('input[name^="permissions[items]"]').forEach(cb => cb.checked = false);
            document.querySelectorAll('input[name^="permissions[repairs]"]').forEach(cb => cb.checked = true);
            document.querySelectorAll('input[name^="permissions[spare_parts]"]').forEach(cb => cb.checked = true);
        } else if (bType === 'both') {
            document.querySelectorAll('input[name^="permissions[items]"]').forEach(cb => cb.checked = true);
            document.querySelectorAll('input[name^="permissions[repairs]"]').forEach(cb => cb.checked = true);
            document.querySelectorAll('input[name^="permissions[spare_parts]"]').forEach(cb => cb.checked = true);
        }

        if (typeof updateCounters === 'function') {
            updateCounters();
        }
    }

    function goToStep(step) {
        // Enforce Step 1 validation when moving forward
        if (step > 1 && !validateStep1()) {
            step = 1;
        }

        currentStep = step;

        // Hide all panes
        document.querySelectorAll('.step-pane').forEach(el => el.classList.add('hidden'));

        // Reset all pills
        for (let i = 1; i <= 4; i++) {
            const pill = document.getElementById('step_pill_' + i);
            if (pill) {
                if (i === step) {
                    pill.classList.remove('text-[#666666]');
                    pill.classList.add('bg-[#111111]', 'text-[#B7FF00]');
                    const badge = pill.querySelector('span');
                    if (badge) {
                        badge.classList.remove('bg-[#E6E6E6]', 'text-[#333333]');
                        badge.classList.add('bg-[#B7FF00]', 'text-[#101010]');
                    }
                } else if (i < step) {
                    pill.classList.remove('bg-[#111111]', 'text-[#B7FF00]');
                    pill.classList.add('text-[#079455]', 'font-bold');
                    const badge = pill.querySelector('span');
                    if (badge) {
                        badge.classList.remove('bg-[#B7FF00]', 'text-[#101010]', 'bg-[#E6E6E6]', 'text-[#333333]');
                        badge.classList.add('bg-[#ECFDF3]', 'text-[#027A48]');
                    }
                } else {
                    pill.classList.remove('bg-[#111111]', 'text-[#B7FF00]', 'text-[#079455]');
                    pill.classList.add('text-[#666666]');
                    const badge = pill.querySelector('span');
                    if (badge) {
                        badge.classList.remove('bg-[#B7FF00]', 'text-[#101010]', 'bg-[#ECFDF3]', 'text-[#027A48]');
                        badge.classList.add('bg-[#E6E6E6]', 'text-[#333333]');
                    }
                }
            }
        }

        // Show target pane
        const activePane = document.getElementById('step_section_' + step);
        if (activePane) activePane.classList.remove('hidden');

        // Dynamic synchronization upon opening Step 2 or 3
        const selectedBType = document.querySelector('input[name="business_type"]:checked')?.value || 'selling';
        if (step === 2 && typeof filterPermissionsByBusinessType === 'function') {
            filterPermissionsByBusinessType(selectedBType);
        } else if (step === 3 && typeof filterFormTabsByBusinessType === 'function') {
            filterFormTabsByBusinessType(selectedBType);
        } else if (step === 4) {
            // Populate step 4 review
            document.getElementById('rev_store_name').textContent = document.getElementById('input_store_name').value || 'Store Name';
            document.getElementById('rev_email').textContent = document.getElementById('input_email').value || 'email@store.com';
            document.getElementById('rev_phone').textContent = document.getElementById('input_phone').value || '10 Digits';
            document.getElementById('rev_plan').textContent = document.getElementById('input_plan').value || 'pro';
            document.getElementById('rev_admin_name').textContent = document.getElementById('input_admin_name').value || 'Malik Haris';
            document.getElementById('rev_admin_email').textContent = document.getElementById('input_email').value || 'email@store.com';

            const bTypeLabels = {
                'selling': 'Mobile Selling & Reselling',
                'repair': 'Mobile Repair',
                'both': 'Both Selling & Repair'
            };
            const revBType = document.getElementById('rev_business_type');
            if (revBType) {
                revBType.textContent = bTypeLabels[selectedBType] || 'Mobile Selling & Reselling';
            }
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Attach form submit handler with comprehensive pre-flight validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('onboardForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!validateStep1()) {
                    e.preventDefault();
                    goToStep(1);
                    return false;
                }

                const btn = document.getElementById('btnSubmitOnboard');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = `
                        <svg class="w-4 h-4 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Creating Business &amp; Launching...</span>
                    `;
                }
            });
        }

        // Initialize business type filters based on checked option
        const initialBType = document.querySelector('input[name="business_type"]:checked')?.value || 'selling';
        handleBusinessTypeChange(initialBType);
    });
</script>
@endsection
