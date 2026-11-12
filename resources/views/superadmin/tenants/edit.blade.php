{{-- Hallmark · macrostructure: Edit Tenant Form · theme: Tactile Sage & Deep Rail · genre: modern-minimal
 * pre-emit critique: P5 H5 E5 S5 R5 V5
 * slop test: pass
--}}
@extends('layouts.app')

@section('title', 'Edit ' . $tenant->name . ' · Super Admin')
@section('page-title', 'Edit Business Store')

@section('content')
<div class="max-w-4xl mx-auto space-y-4">

    <!-- Top Action Row -->
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-[#111111] tracking-tight" style="overflow-wrap: anywhere; min-width: 0;">Edit {{ $tenant->name }}</h1>
            <p class="text-xs sm:text-sm text-[#666666]">Modify subscription tier, contact information, and store tax identity</p>
        </div>
        <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="btn-secondary min-h-[38px] text-xs inline-flex items-center gap-1.5">
            &larr; Back to Details
        </a>
    </div>

    <form method="POST" action="{{ route('superadmin.tenants.update', $tenant) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-4">
            <div class="flex items-center gap-2 border-b border-[#E6E6E6] pb-3">
                <span class="w-2 h-2 rounded-full bg-[#B7FF00]"></span>
                <h2 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Business Store Profile</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-[#344054] mb-1">Business Name *</label>
                    <input type="text" name="name" value="{{ old('name', $tenant->name) }}" required
                        class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-bold text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Subscription Plan *</label>
                    <select name="plan" required class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-bold text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                        <option value="trial" {{ $tenant->plan === 'trial' ? 'selected' : '' }}>Trial (Free)</option>
                        <option value="starter" {{ $tenant->plan === 'starter' ? 'selected' : '' }}>Starter Plan</option>
                        <option value="pro" {{ $tenant->plan === 'pro' ? 'selected' : '' }}>Pro Business</option>
                        <option value="enterprise" {{ $tenant->plan === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Store Email *</label>
                    <input type="email" name="email" value="{{ old('email', $tenant->email) }}" required
                        class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Store Phone *</label>
                    <input type="text" name="phone" value="{{ old('phone', $tenant->phone) }}" required
                        class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Expiry Date</label>
                    <input type="date" name="expires_at" value="{{ old('expires_at', $tenant->expires_at ? $tenant->expires_at->format('Y-m-d') : '') }}"
                        class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Invoice Prefix</label>
                    <input type="text" name="invoice_prefix" value="{{ old('invoice_prefix', $tenant->invoice_prefix) }}"
                        class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">GSTIN Number</label>
                    <input type="text" name="gstin" value="{{ old('gstin', $tenant->gstin) }}"
                        class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">UPI ID for Counter QR</label>
                    <input type="text" name="upi_id" value="{{ old('upi_id', $tenant->upi_id) }}"
                        class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-[#344054] mb-1">Address</label>
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

                <div class="sm:col-span-3 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ $tenant->is_active ? 'checked' : '' }} class="w-4 h-4 rounded text-[#111111] border-[#E6E6E6] focus:ring-[#111111]">
                        <span class="text-xs font-bold text-[#111111]">Business Store is Active & Accessible</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="btn-secondary min-h-[44px]">
                Cancel
            </a>
            <button type="submit" class="btn-lime min-h-[44px]">
                <span class="font-bold">Save Changes </span>
            </button>
        </div>
    </form>
</div>
@endsection

