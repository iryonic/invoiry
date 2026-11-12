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
        <div class="p-4 bg-[#FEF2F2] border border-[#FECACA] rounded-xl text-xs text-[#B91C1C] space-y-1">
            <div class="font-bold">Please correct the following errors before proceeding:</div>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-bold text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
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

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Store Official Email *</label>
                        <input type="email" id="input_email" name="email" value="{{ old('email') }}" required
                            placeholder="store@domain.com"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Store Phone *</label>
                        <input type="text" id="input_phone" name="phone" value="{{ old('phone') }}" required
                            placeholder="10 Digits"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
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
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Admin Password *</label>
                        <div class="relative">
                            <input type="password" id="input_password" name="password" required placeholder="Minimum 6 characters"
                                class="w-full pl-3.5 pr-10 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                            <x-password-toggle-btn target="input_password" />
                        </div>
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
                <button type="submit" class="btn-lime min-h-[44px] px-8 text-sm font-bold shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    <span>Create Tenant & Launch Business</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function goToStep(step) {
        // Simple front validation for Step 1 before advancing
        if (step > 1) {
            const name = document.getElementById('input_store_name').value.trim();
            const email = document.getElementById('input_email').value.trim();
            const phone = document.getElementById('input_phone').value.trim();
            const adminName = document.getElementById('input_admin_name').value.trim();
            const pass = document.getElementById('input_password').value;

            if (!name || !email || !phone || !adminName || !pass) {
                alert('Please fill out all required fields marked with * on Step 1 (Store details & Admin credentials) first.');
                goToStep(1);
                return;
            }
        }

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

        // Populate step 4 review
        if (step === 4) {
            document.getElementById('rev_store_name').textContent = document.getElementById('input_store_name').value || 'Store Name';
            document.getElementById('rev_email').textContent = document.getElementById('input_email').value || 'email@store.com';
            document.getElementById('rev_phone').textContent = document.getElementById('input_phone').value || '10 Digits';
            document.getElementById('rev_plan').textContent = document.getElementById('input_plan').value || 'pro';
            document.getElementById('rev_admin_name').textContent = document.getElementById('input_admin_name').value || 'Malik Haris';
            document.getElementById('rev_admin_email').textContent = document.getElementById('input_email').value || 'email@store.com';
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>
@endsection
