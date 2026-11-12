{{-- Hallmark · macrostructure: Super Admin User Profile Editor · theme: Tactile Sage & Deep Rail · genre: modern-minimal
 * pre-emit critique: P5 H5 E5 S5 R5 V5
 * slop test: pass
--}}
@extends('layouts.app')

@section('title', 'Edit User · ' . $user->name)
@section('page-title', 'User Profile Management')
@section('page-subtitle', 'Super Admin Console')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-[#F2F4F7] text-[#344054] mb-1 border border-[#EAECF0]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#111111]"></span>
                <span class="capitalize">{{ str_replace('_', ' ', $user->role) }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-[#111111] tracking-tight">Edit Account: {{ $user->name }}</h1>
            <p class="text-xs sm:text-sm text-[#666666]">Update account credentials, store assignment, and access privileges</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('superadmin.users.index') }}" class="btn-secondary min-h-[38px] text-xs inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Users Directory</span>
            </a>
            <a href="{{ route('superadmin.users.permissions', $user) }}" class="btn-lime min-h-[38px] text-xs inline-flex items-center gap-1.5 font-bold shadow-2xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                <span>Manage Permissions</span>
            </a>
        </div>
    </div>

    <!-- Edit User Form Card -->
    <div class="card-surface p-5 sm:p-6 shadow-2xs border border-[#E6E6E6] rounded-2xl bg-white space-y-6">
        <form method="POST" action="{{ route('superadmin.users.update', $user) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <input type="hidden" name="redirect_to" value="index">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black min-h-[38px]">
                    @error('name')
                        <p class="text-[11px] text-[#DC2626] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Email Address *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black min-h-[38px]">
                    @error('email')
                        <p class="text-[11px] text-[#DC2626] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="e.g. 9876543210"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black min-h-[38px]">
                    @error('phone')
                        <p class="text-[11px] text-[#DC2626] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Platform Role *</label>
                    <select name="role" required class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-bold text-[#111111] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black min-h-[38px]">
                        <option value="cashier" {{ old('role', $user->role) === 'cashier' ? 'selected' : '' }}>Store Cashier (Counter billing & invoicing)</option>
                        <option value="tenant_admin" {{ old('role', $user->role) === 'tenant_admin' ? 'selected' : '' }}>Store Admin (Full store manager)</option>
                        <option value="super_admin" {{ old('role', $user->role) === 'super_admin' ? 'selected' : '' }}>Super Admin (Platform operator)</option>
                    </select>
                    @error('role')
                        <p class="text-[11px] text-[#DC2626] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-[#344054] mb-1">Assigned Business Store</label>
                    <select name="tenant_id" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-semibold text-[#111111] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black min-h-[38px]">
                        <option value="">None / Super Admin Global Access</option>
                        @foreach($tenants as $t)
                            <option value="{{ $t->id }}" {{ (string) old('tenant_id', $user->tenant_id) === (string) $t->id ? 'selected' : '' }}>
                                {{ $t->name }} ({{ $t->slug }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-[#666666] mt-1">Cashiers and Store Admins must be attached to a specific store.</p>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-[#344054] mb-1">Reset Password</label>
                    <div class="relative">
                        <input type="password" name="password" placeholder="Leave blank to keep existing password"
                            class="w-full pl-3 pr-10 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black min-h-[38px]">
                        <x-password-toggle-btn />
                    </div>
                    <p class="text-[11px] text-[#666666] mt-1">Minimum 6 characters. Leave empty if password should remain unchanged.</p>
                    @error('password')
                        <p class="text-[11px] text-[#DC2626] mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Active Status Toggle -->
            <div class="p-3.5 rounded-xl bg-[#F7F7F6] border border-[#E6E6E6]">
                <x-toggle-switch
                    name="is_active"
                    id="user_is_active"
                    value="1"
                    :checked="old('is_active', $user->is_active)"
                    label="Account Active & Login Permitted"
                    description="When disabled, this user is immediately blocked from logging in or making authenticated requests."
                />
            </div>

            <!-- Granular Permissions Callout -->
            <div class="p-4 rounded-xl bg-[#050505] text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="space-y-0.5">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#B7FF00]"></span>
                        <h4 class="text-xs sm:text-sm font-bold text-[#F7F7F6]">Role-Based Access Control (RBAC)</h4>
                    </div>
                    <p class="text-[11px] text-[#929292]">
                        @if(is_array($user->permissions) && !empty($user->permissions))
                            <span class="text-[#B7FF00] font-semibold">Custom permissions active</span> &bull; Overriding default store permissions for this user.
                        @else
                            Currently inheriting standard permissions from assigned store.
                        @endif
                    </p>
                </div>
                <a href="{{ route('superadmin.users.permissions', $user) }}" class="btn-lime min-h-[36px] text-xs px-4 font-bold flex-shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <span>Configure Permissions</span>
                </a>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-[#E6E6E6]">
                <a href="{{ route('superadmin.users.index') }}" class="btn-secondary min-h-[42px] text-xs">
                    Cancel
                </a>
                <button type="submit" class="btn-lime min-h-[42px] px-6 text-xs font-bold shadow-xs">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

