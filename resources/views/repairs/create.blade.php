@extends('layouts.app')

@section('title', 'New Repair Request · ' . ($tenant->name ?? 'Mobile Repair'))
@section('page-title', 'Register Repair Request')
@section('page-subtitle', 'Intake customer device, log reported issues, physical condition, and assign technician')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header & Back Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('repairs.index') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-[#666666] hover:text-[#111111] transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Back to Repair Orders</span>
        </a>

        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#E0F2FE] text-[#0369A1] border border-[#BAE6FD]">
            <span>Next Job: #{{ $tenant->repair_job_prefix ?? 'REP-' }}{{ str_pad($tenant->repair_job_counter ?? 1, 5, '0', STR_PAD_LEFT) }}</span>
        </div>
    </div>

    <!-- Check-In Form -->
    <form method="POST" action="{{ route('repairs.store') }}" id="repairForm" class="space-y-6">
        @csrf

        @if($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-xs sm:text-sm">
                <div class="font-bold mb-1">Please correct the following errors:</div>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- 1. Customer Section -->
        <div class="card-surface p-6 space-y-5">
            <div class="flex items-center justify-between border-b border-[#EBEBEB] pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-[#111111] text-white flex items-center justify-center text-xs font-bold">1</span>
                    <h3 class="text-base font-bold text-[#111111]">Customer Details</h3>
                </div>
                <button type="button" id="toggleCustomerMode" class="text-xs font-bold text-[#0369A1] hover:underline cursor-pointer">
                    + Register New Customer Instead
                </button>
            </div>

            <!-- Existing Customer Selection -->
            <div id="existingCustomerSection" class="space-y-3">
                <label class="block text-xs font-bold uppercase tracking-wider text-[#666666]">
                    Select Existing Customer <span class="text-red-500">*</span>
                </label>
                <select name="customer_id" id="customer_id" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                    <option value="">-- Choose registered customer --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ (old('customer_id', $selectedCustomerId) == $c->id) ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->phone }}) {{ $c->city ? '· ' . $c->city : '' }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-[#888888]">
                    Can't find customer? Click "Register New Customer Instead" above.
                </p>
            </div>

            <!-- New Customer Inline Form (Hidden by default unless toggled or error) -->
            <div id="newCustomerSection" class="hidden space-y-4 pt-2">
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800">
                    Entering customer details here will create a new customer in your store database automatically.
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                            Customer Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="new_customer_name" value="{{ old('new_customer_name') }}" placeholder="e.g. Rahul Sharma" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                            Customer Phone <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="new_customer_phone" value="{{ old('new_customer_phone') }}" placeholder="e.g. 9876543210" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">Email Address</label>
                        <input type="email" name="new_customer_email" value="{{ old('new_customer_email') }}" placeholder="e.g. rahul@example.com" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">Address / Landmark</label>
                        <input type="text" name="new_customer_address" value="{{ old('new_customer_address') }}" placeholder="e.g. Shop 4, Main Market" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Device Information Section -->
        <div class="card-surface p-6 space-y-5">
            <div class="flex items-center gap-2 border-b border-[#EBEBEB] pb-3">
                <span class="w-7 h-7 rounded-full bg-[#111111] text-white flex items-center justify-center text-xs font-bold">2</span>
                <h3 class="text-base font-bold text-[#111111]">Device &amp; Physical Specifications</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                        Device Brand <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="device_brand" value="{{ old('device_brand') }}" required placeholder="e.g. Apple, Samsung, Xiaomi, OnePlus" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                        Device Model <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="device_model" value="{{ old('device_model') }}" required placeholder="e.g. iPhone 14 Pro, Galaxy S23, Redmi Note 12" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                        IMEI Number <span class="text-[11px] font-normal text-[#888888]">(Dial *#06#)</span>
                    </label>
                    <input type="text" name="imei" value="{{ old('imei') }}" maxlength="50" placeholder="15-digit IMEI (optional)" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm font-mono text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                        Serial Number / S/N
                    </label>
                    <input type="text" name="serial_number" value="{{ old('serial_number') }}" maxlength="100" placeholder="Hardware Serial (optional)" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm font-mono text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                        Device Colour
                    </label>
                    <input type="text" name="device_color" value="{{ old('device_color') }}" placeholder="e.g. Space Black, Midnight Green" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                        Screen Lock / Passcode / Pattern
                    </label>
                    <input type="text" name="device_password" value="{{ old('device_password') }}" placeholder="PIN/Pattern or 'None' (for testing after repair)" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                </div>
            </div>

            <!-- Physical Condition & Accessories -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                        Physical Condition at Intake
                    </label>
                    <textarea name="device_condition" rows="3" placeholder="e.g. Front glass shattered, scratches on back cover, camera lens intact, power turns ON with vibration." class="w-full py-2 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-xs sm:text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition">{{ old('device_condition') }}</textarea>
                    <p class="text-[11px] text-[#888888] mt-0.5">Critical for protection against pre-existing damage disputes.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                        Accessories Received with Device
                    </label>
                    <textarea name="accessories_received" rows="3" placeholder="e.g. SIM Tray present, No SIM card, Silicone case, Original Box (No charger)." class="w-full py-2 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-xs sm:text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition">{{ old('accessories_received') }}</textarea>
                    <p class="text-[11px] text-[#888888] mt-0.5">Mention SIM, Memory Card, Case, Adapter etc.</p>
                </div>
            </div>
        </div>

        <!-- 3. Problem Diagnosis & Technician Assignment -->
        <div class="card-surface p-6 space-y-5">
            <div class="flex items-center gap-2 border-b border-[#EBEBEB] pb-3">
                <span class="w-7 h-7 rounded-full bg-[#111111] text-white flex items-center justify-center text-xs font-bold">3</span>
                <h3 class="text-base font-bold text-[#111111]">Reported Problem &amp; Assignment</h3>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                    Customer Reported Problem <span class="text-red-500">*</span>
                </label>
                <textarea name="reported_problem" rows="3" required placeholder="Describe what the customer reported (e.g. Phone does not charge, display blank after drop, battery draining rapidly in 2 hours)..." class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition">{{ old('reported_problem') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                        Initial Diagnosis Notes (Front-Desk)
                    </label>
                    <textarea name="diagnosis_notes" rows="2" placeholder="e.g. Display backlight failed, charging port dirty or loose connector pin." class="w-full py-2 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-xs sm:text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition">{{ old('diagnosis_notes') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                        Assign Technician / Workbench
                    </label>
                    <select name="assigned_to" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                        <option value="">-- Leave Unassigned (Queue) --</option>
                        @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}" {{ old('assigned_to') == $tech->id ? 'selected' : '' }}>
                                {{ $tech->name }} ({{ ucfirst(str_replace('_', ' ', $tech->role)) }})
                            </option>
                        @endforeach
                    </select>

                    <div class="mt-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">Priority Level</label>
                        <select name="priority" class="w-full py-2 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition">
                            <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normal Priority</option>
                            <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High Priority</option>
                            <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent / Express Service</option>
                            <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low Priority</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Estimates, Warranty & Advance Deposit -->
        <div class="card-surface p-6 space-y-5">
            <div class="flex items-center gap-2 border-b border-[#EBEBEB] pb-3">
                <span class="w-7 h-7 rounded-full bg-[#111111] text-white flex items-center justify-center text-xs font-bold">4</span>
                <h3 class="text-base font-bold text-[#111111]">Cost Estimate &amp; Advance Deposit</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                        Estimated Total Cost (₹)
                    </label>
                    <input type="number" step="0.01" min="0" name="estimated_cost" value="{{ old('estimated_cost', '0.00') }}" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm font-mono text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                    <p class="text-[11px] text-[#888888] mt-0.5">Approximate quote given to customer.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                        Expected Delivery Date
                    </label>
                    <input type="date" name="expected_completion_date" value="{{ old('expected_completion_date', now()->addDays(2)->toDateString()) }}" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                        Service Warranty (Days)
                    </label>
                    <input type="number" min="0" name="warranty_days" value="{{ old('warranty_days', 30) }}" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                </div>
            </div>

            <div class="p-4 bg-[#F8F9FA] rounded-xl border border-[#E9ECEF] grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                        Advance Deposit Paid Today (₹)
                    </label>
                    <input type="number" step="0.01" min="0" name="advance_paid" value="{{ old('advance_paid', '0.00') }}" placeholder="0.00" class="w-full py-2.5 px-3 bg-white border border-[#D0D5DD] rounded-lg text-sm font-mono font-bold text-[#079455] focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                    <p class="text-[11px] text-[#666666] mt-0.5">Leave 0 if no advance is collected now.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                        Deposit Payment Method
                    </label>
                    <select name="payment_method" class="w-full py-2.5 px-3 bg-white border border-[#D0D5DD] rounded-lg text-sm text-[#111111] focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                        <option value="cash">Cash</option>
                        <option value="upi">UPI / QR Code</option>
                        <option value="card">Debit / Credit Card</option>
                        <option value="bank_transfer">Bank Transfer / NEFT</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Submit & Actions -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('repairs.index') }}" class="btn-secondary px-5 py-2.5 rounded-lg text-xs sm:text-sm font-semibold min-h-[44px] flex items-center justify-center">
                Cancel
            </a>
            <button type="submit" class="btn-lime px-6 py-2.5 rounded-lg text-xs sm:text-sm font-bold shadow-xs min-h-[44px] flex items-center gap-2">
                <svg class="w-4 h-4 text-[#101010]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                <span>Check In Device &amp; Generate Job Sheet</span>
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('toggleCustomerMode');
        const existingSec = document.getElementById('existingCustomerSection');
        const newSec = document.getElementById('newCustomerSection');
        const custSelect = document.getElementById('customer_id');

        let isNewMode = false;

        toggleBtn.addEventListener('click', function () {
            isNewMode = !isNewMode;
            if (isNewMode) {
                existingSec.classList.add('hidden');
                newSec.classList.remove('hidden');
                custSelect.value = '';
                toggleBtn.textContent = '← Select Existing Customer';
            } else {
                newSec.classList.add('hidden');
                existingSec.classList.remove('hidden');
                toggleBtn.textContent = '+ Register New Customer Instead';
            }
        });

        @if(old('new_customer_name') || old('new_customer_phone'))
            toggleBtn.click();
        @endif
    });
</script>
@endsection

