{{-- Hallmark · macrostructure: Staff & RBAC Management · theme: Tactile Sage & Deep Rail · genre: modern-minimal
 * pre-emit critique: P5 H5 E5 S5 R5 V5
 * slop test: pass
--}}
@extends('layouts.app')

@php
    $tenant = $tenant ?? \App\Services\TenantContext::get();
    $user = auth()->user();
@endphp

@section('title', 'Team & Staff Management')
@section('page-title', 'Staff & Roles')

@section('content')
<div class="space-y-4 w-full">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-[#F2F4F7] text-[#344054] rounded-md text-xs font-bold mb-1 border border-[#EAECF0]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#079455]"></span>
                <span>Role-Based Access Control</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-[#111111] tracking-tight" style="overflow-wrap: anywhere; min-width: 0;">Store Staff & Permissions</h1>
            <p class="text-xs sm:text-sm text-[#666666]">Manage login access, cashier terminals, and administrative roles for {{ $tenant->name }}</p>
        </div>

        <button type="button" onclick="document.getElementById('addStaffModal').classList.remove('hidden')" class="btn-lime min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            <span class="whitespace-nowrap font-bold">Add Staff Member</span>
        </button>
    </div>

    <!-- Role Explanation Banner -->
    <div class="grid grid-cols-1 {{ $tenant?->supportsRepair() ? 'sm:grid-cols-3' : 'sm:grid-cols-2' }} gap-3">
        <div class="card-surface p-4 shadow-2xs flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-[#050505] text-[#B7FF00] flex items-center justify-center font-bold text-xs shrink-0">
                SA
            </div>
            <div>
                <h4 class="text-xs font-bold text-[#111111]">Store Admin (Owner / Manager)</h4>
                <p class="text-[11px] text-[#666666] mt-0.5">Full store authority: POS billing, inventory purchasing & cost prices, customer khata, vendor tracking, staff management, and shop settings.</p>
            </div>
        </div>

        <div class="card-surface p-4 shadow-2xs flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-[#F2F4F7] text-[#344054] flex items-center justify-center font-bold text-xs shrink-0 border border-[#E6E6E6]">
                CS
            </div>
            <div>
                <h4 class="text-xs font-bold text-[#111111]">Store Cashier (Front-Desk Billing)</h4>
                <p class="text-[11px] text-[#666666] mt-0.5">Counter billing only: Generate customer invoices, view available device inventory, record customer settlements. Restricted from shop settings & cost prices.</p>
            </div>
        </div>

        @if($tenant?->supportsRepair())
            <div class="card-surface p-4 shadow-2xs flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-[#E0F2FE] text-[#0369A1] flex items-center justify-center font-bold text-xs shrink-0 border border-[#BAE6FD]">
                    TC
                </div>
                <div>
                    <h4 class="text-xs font-bold text-[#111111]">Technician (Workbench Engineer)</h4>
                    <p class="text-[11px] text-[#666666] mt-0.5">Workshop management: Perform diagnosis, update repair statuses, install spare parts, log repair notes, and generate job sheets.</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Staff Members List -->
    <div class="card-surface shadow-2xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-[#E6E6E6] flex items-center justify-between">
            <h2 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Active Staff Members ({{ $staff->total() }})</h2>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F7F7F6] text-[#666666] uppercase tracking-wider font-bold border-b border-[#E6E6E6]">
                    <tr>
                        <th class="py-3 px-4">Staff Member</th>
                        <th class="py-3 px-4">Role & Privileges</th>
                        <th class="py-3 px-4">Contact</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E6E6E6] text-[#344054]">
                    @foreach($staff as $u)
                        <tr class="hover:bg-[#F7F7F6]/60 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-[#111111] text-sm flex items-center gap-2">
                                    <span>{{ $u->name }}</span>
                                    @if($u->id === auth()->id())
                                        <span class="text-[10px] px-1.5 py-0.2 rounded bg-[#B7FF00]/30 text-[#101010] font-bold">You</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-[#929292] font-mono">{{ $u->email }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($u->role === 'tenant_admin')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#111111] text-[#B7FF00]">
                                        <span>Store Admin</span>
                                    </span>
                                @elseif($u->role === 'technician')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#E0F2FE] text-[#0369A1] border border-[#BAE6FD]">
                                        <span>Technician</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#F2F4F7] text-[#344054] border border-[#EAECF0]">
                                        <span>Cashier</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono font-medium text-[#111111]">
                                {{ $u->phone ?: 'No phone' }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($u->is_active)
                                    <span class="badge-paid">Active</span>
                                @else
                                    <span class="badge-unpaid">Disabled</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('staff.status', $u) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn-secondary text-[11px] px-2.5 py-1 min-h-[32px]">
                                                {{ $u->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>

                                        <form action="{{ route('staff.destroy', $u) }}" method="POST" onsubmit="return confirm('Remove {{ $u->name }} from staff?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-[#DC2626] hover:bg-[#FEF2F2] rounded-lg transition min-h-[32px]" title="Delete staff member">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[11px] text-[#929292] italic">Primary Account</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-[#E6E6E6]">
            @foreach($staff as $u)
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h3 class="text-sm font-bold text-[#111111] flex items-center gap-1.5">
                                <span>{{ $u->name }}</span>
                                @if($u->id === auth()->id())
                                    <span class="text-[10px] px-1.5 py-0.2 rounded bg-[#B7FF00]/30 text-[#101010] font-bold">You</span>
                                @endif
                            </h3>
                            <div class="text-xs text-[#929292] font-mono mt-0.5">{{ $u->email }}</div>
                        </div>
                        <div>
                            @if($u->is_active)
                                <span class="badge-paid">Active</span>
                            @else
                                <span class="badge-unpaid">Disabled</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs text-[#666666]">
                        <div>
                            <span class="text-[10px] text-[#929292] uppercase block">Assigned Role</span>
                            <span class="font-bold text-[#111111] capitalize">{{ str_replace('_', ' ', $u->role) }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-[#929292] uppercase block">Phone</span>
                            <span class="font-mono text-[#111111] font-semibold">{{ $u->phone ?: 'None' }}</span>
                        </div>
                    </div>

                    @if($u->id !== auth()->id())
                        <div class="pt-2 border-t border-[#E6E6E6] flex items-center justify-end gap-2">
                            <form action="{{ route('staff.status', $u) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-secondary text-[11px] px-3 py-1.5 min-h-[36px]">
                                    {{ $u->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            <form action="{{ route('staff.destroy', $u) }}" method="POST" onsubmit="return confirm('Remove staff member?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-[#FEF2F2] text-[#DC2626] font-semibold rounded-lg text-xs min-h-[36px]">
                                    Delete
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        @if($staff->hasPages())
            <div class="p-3 border-t border-[#E6E6E6]">
                {{ $staff->links() }}
            </div>
        @endif
    </div>

    <!-- Add Staff Modal -->
    <div id="addStaffModal" class="modal-backdrop hidden" onclick="if(event.target === this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-[#E6E6E6] space-y-4 my-auto max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#B7FF00]"></span>
                    <h3 class="text-sm font-bold text-[#111111]">Register New Staff Member</h3>
                </div>
                <button type="button" onclick="document.getElementById('addStaffModal').classList.add('hidden')" class="text-[#929292] hover:text-[#111111] p-1 text-lg font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('staff.store') }}" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Full Name *</label>
                    <input type="text" name="name" required placeholder="Staff member name"
                        class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Email (Login Username) *</label>
                    <input type="email" name="email" required placeholder="staff@storedomain.com"
                        class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                </div>

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'staff', 'phone'))
                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Phone Number {{ \App\Services\DynamicFormConfig::isFieldRequired($tenant, 'staff', 'phone') ? '*' : '' }}</label>
                        <input type="text" name="phone" {{ \App\Services\DynamicFormConfig::isFieldRequired($tenant, 'staff', 'phone') ? 'required' : '' }} placeholder="10 Digits Mobile"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Role & Permissions *</label>
                    <select name="role" required class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-bold text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                        <option value="cashier" selected>Store Cashier (Counter Billing & Invoices only)</option>
                        @if($tenant?->supportsRepair())
                            <option value="technician">Technician (Workbench & Repairs)</option>
                        @endif
                        <option value="tenant_admin">Store Admin (Full Manager Access)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Login Password *</label>
                    <div class="relative">
                        <input type="password" name="password" required placeholder="Minimum 6 characters"
                            class="w-full pl-3.5 pr-10 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                        <x-password-toggle-btn />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#E6E6E6]">
                    <button type="button" onclick="document.getElementById('addStaffModal').classList.add('hidden')" class="btn-secondary min-h-[44px]">
                        Cancel
                    </button>
                    <button type="submit" class="btn-lime min-h-[44px]">
                        Save Staff Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

