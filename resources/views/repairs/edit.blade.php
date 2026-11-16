@extends('layouts.app')

@section('title', 'Edit Repair Job #' . $repairJob->job_number . ' · ' . ($tenant->name ?? 'Mobile Repair'))
@section('page-title', 'Edit Repair Job #' . $repairJob->job_number)
@section('page-subtitle', 'Update device identifiers, reported problem, notes, and priority')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header & Back Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('repairs.show', $repairJob) }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-[#666666] hover:text-[#111111] transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Back to Job Workshop</span>
        </a>

        <div class="text-xs text-[#888888]">
            Customer: <strong class="text-[#111111]">{{ $repairJob->customer->name ?? 'Walk-in' }}</strong>
        </div>
    </div>

    <!-- Edit Form -->
    <form method="POST" action="{{ route('repairs.update', $repairJob) }}" class="space-y-6">
        @csrf
        @method('PUT')

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

        <!-- Device Information -->
        <div class="card-surface p-6 space-y-5">
            <h3 class="text-base font-bold text-[#111111] border-b border-[#EBEBEB] pb-3">Device Information</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                        Device Brand <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="device_brand" value="{{ old('device_brand', $repairJob->device_brand) }}" required class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                        Device Model <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="device_model" value="{{ old('device_model', $repairJob->device_model) }}" required class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                        IMEI Number
                    </label>
                    <input type="text" name="imei" value="{{ old('imei', $repairJob->imei) }}" maxlength="50" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm font-mono text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                        Serial Number / S/N
                    </label>
                    <input type="text" name="serial_number" value="{{ old('serial_number', $repairJob->serial_number) }}" maxlength="100" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm font-mono text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                        Device Colour
                    </label>
                    <input type="text" name="device_color" value="{{ old('device_color', $repairJob->device_color) }}" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                        Screen Lock / Passcode / Pattern
                    </label>
                    <input type="text" name="device_password" value="{{ old('device_password', $repairJob->device_password) }}" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                        Physical Condition at Intake
                    </label>
                    <textarea name="device_condition" rows="3" class="w-full py-2 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-xs sm:text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition">{{ old('device_condition', $repairJob->device_condition) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                        Accessories Received
                    </label>
                    <textarea name="accessories_received" rows="3" class="w-full py-2 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-xs sm:text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition">{{ old('accessories_received', $repairJob->accessories_received) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Problem, Diagnosis & Technician -->
        <div class="card-surface p-6 space-y-5">
            <h3 class="text-base font-bold text-[#111111] border-b border-[#EBEBEB] pb-3">Problem &amp; Assignment</h3>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                    Customer Reported Problem <span class="text-red-500">*</span>
                </label>
                <textarea name="reported_problem" rows="3" required class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition">{{ old('reported_problem', $repairJob->reported_problem) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                        Diagnosis Notes
                    </label>
                    <textarea name="diagnosis_notes" rows="2" class="w-full py-2 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-xs sm:text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition">{{ old('diagnosis_notes', $repairJob->diagnosis_notes) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                        Technician Workbench Notes
                    </label>
                    <textarea name="technician_notes" rows="2" class="w-full py-2 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-xs sm:text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition">{{ old('technician_notes', $repairJob->technician_notes) }}</textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">
                        Assign Technician
                    </label>
                    <select name="assigned_to" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                        <option value="">-- Unassigned (Queue) --</option>
                        @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}" {{ old('assigned_to', $repairJob->assigned_to) == $tech->id ? 'selected' : '' }}>
                                {{ $tech->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">Priority</label>
                    <select name="priority" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                        <option value="normal" {{ old('priority', $repairJob->priority) === 'normal' ? 'selected' : '' }}>Normal Priority</option>
                        <option value="high" {{ old('priority', $repairJob->priority) === 'high' ? 'selected' : '' }}>High Priority</option>
                        <option value="urgent" {{ old('priority', $repairJob->priority) === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="low" {{ old('priority', $repairJob->priority) === 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">Estimated Cost (₹)</label>
                    <input type="number" step="0.01" min="0" name="estimated_cost" value="{{ old('estimated_cost', $repairJob->estimated_cost) }}" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm font-mono text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">Expected Completion Date</label>
                    <input type="date" name="expected_completion_date" value="{{ old('expected_completion_date', $repairJob->expected_completion_date ? \Carbon\Carbon::parse($repairJob->expected_completion_date)->toDateString() : '') }}" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#666666] mb-1">Warranty Days</label>
                    <input type="number" min="0" name="warranty_days" value="{{ old('warranty_days', $repairJob->warranty_days) }}" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                </div>
            </div>
        </div>

        <!-- Submit & Actions -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('repairs.show', $repairJob) }}" class="btn-secondary px-5 py-2.5 rounded-lg text-xs sm:text-sm font-semibold min-h-[44px] flex items-center justify-center">
                Cancel
            </a>
            <button type="submit" class="btn-lime px-6 py-2.5 rounded-lg text-xs sm:text-sm font-bold shadow-xs min-h-[44px] flex items-center gap-2">
                <span>Save Changes</span>
            </button>
        </div>
    </form>
</div>
@endsection

