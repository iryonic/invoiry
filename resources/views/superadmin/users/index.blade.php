{{-- Hallmark · macrostructure: Global User Accounts Directory · theme: Tactile Sage & Deep Rail · genre: modern-minimal
 * pre-emit critique: P5 H5 E5 S5 R5 V5
 * slop test: pass
--}}
@extends('layouts.app')

@section('title', 'Global Users & RBAC · Super Admin')
@section('page-title', 'User Directory')

@section('content')
<div class="space-y-4 w-full">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-[#F2F4F7] text-[#344054] rounded-md text-xs font-bold mb-1 border border-[#EAECF0]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#111111]"></span>
                <span>Role-Based Access Control</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-[#111111] tracking-tight" style="overflow-wrap: anywhere; min-width: 0;">Global Users & Staff Directory</h1>
            <p class="text-xs sm:text-sm text-[#666666]">Manage administrator privileges, store cashiers, and credential resets across the platform</p>
        </div>

        <button type="button" onclick="document.getElementById('addUserModal').classList.remove('hidden')" class="btn-lime min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            <span class="whitespace-nowrap font-bold">Create User Account</span>
        </button>
    </div>

    <!-- Search & Filter Toolbar -->
    <div class="card-surface p-3.5 shadow-2xs">
        <form method="GET" action="{{ route('superadmin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2">
            <div class="sm:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by name, email, or phone..."
                    class="w-full pl-9 pr-4 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black min-h-[38px]">
                <svg class="w-4 h-4 text-[#929292] absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <div>
                <select name="role" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-semibold text-[#111111] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black min-h-[38px]">
                    <option value="all">All Roles</option>
                    <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="tenant_admin" {{ request('role') === 'tenant_admin' ? 'selected' : '' }}>Store Admin</option>
                    <option value="cashier" {{ request('role') === 'cashier' ? 'selected' : '' }}>Cashier</option>
                </select>
            </div>

            <div>
                <select name="tenant_id" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-semibold text-[#111111] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black min-h-[38px]">
                    <option value="all">All Business Stores</option>
                    @foreach($tenants as $t)
                        <option value="{{ $t->id }}" {{ request('tenant_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2 bg-[#111111] hover:bg-black text-white rounded-lg text-xs sm:text-sm font-bold min-h-[38px] transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'role', 'tenant_id']))
                    <a href="{{ route('superadmin.users.index') }}" class="btn-secondary min-h-[38px] text-xs">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table Container -->
    <div class="card-surface shadow-2xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-[#E6E6E6] flex items-center justify-between">
            <h2 class="text-xs font-bold text-[#111111] uppercase tracking-wider">All User Accounts ({{ $users->total() }})</h2>
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F7F7F6] text-[#666666] uppercase tracking-wider font-bold border-b border-[#E6E6E6]">
                    <tr>
                        <th class="py-3 px-3 w-10 text-center">
                            <input type="checkbox" data-bulk-select-all class="rounded border-[#D0D5DD] text-black focus:ring-black">
                        </th>
                        <th class="py-3 px-4">User</th>
                        <th class="py-3 px-4">Role</th>
                        <th class="py-3 px-4">Business Store</th>
                        <th class="py-3 px-4">Phone</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E6E6E6] text-[#344054]">
                    @forelse($users as $u)
                        <tr class="hover:bg-[#F7F7F6]/60 transition">
                            <td class="py-3.5 px-3 text-center">
                                <input type="checkbox" data-bulk-item value="{{ $u->id }}" {{ $u->id === auth()->id() ? 'disabled' : '' }} class="rounded border-[#D0D5DD] text-black focus:ring-black">
                            </td>
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
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @if($u->role === 'super_admin')
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#111111] text-[#B7FF00]">Super Admin</span>
                                    @elseif($u->role === 'tenant_admin')
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EFF8FF] text-[#175CD3] border border-[#B2DDFF]">Store Admin</span>
                                    @else
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#F2F4F7] text-[#344054] border border-[#EAECF0]">Cashier</span>
                                    @endif

                                    @if(is_array($u->permissions) && !empty($u->permissions))
                                        <span class="inline-flex px-1.5 py-0.2 rounded text-[9px] font-bold bg-[#ECFDF3] text-[#027A48] border border-[#A6F4C5]" title="Custom RBAC permissions configured">Custom RBAC</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($u->tenant)
                                    <a href="{{ route('superadmin.tenants.show', $u->tenant) }}" class="font-semibold text-[#111111] hover:underline">
                                        {{ $u->tenant->name }}
                                    </a>
                                @else
                                    <span class="text-[#929292] italic">Global (No Tenant)</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono font-medium text-[#111111]">
                                {{ $u->phone ?: 'None' }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($u->is_active)
                                    <span class="badge-paid">Active</span>
                                @else
                                    <span class="badge-unpaid">Disabled</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Edit Permissions -->
                                    <a href="{{ route('superadmin.users.permissions', $u) }}" class="p-1.5 text-[#079455] hover:text-[#027A48] rounded hover:bg-[#ECFDF3]" title="Configure Permissions">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    </a>

                                    <!-- Edit User Account Details -->
                                    <a href="{{ route('superadmin.users.edit', $u) }}" class="p-1.5 text-[#344054] hover:text-[#111111] rounded hover:bg-[#F2F4F7]" title="Edit User Account">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>

                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('superadmin.users.status', $u) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn-secondary text-[11px] px-2.5 py-1 min-h-[30px]" title="Toggle Active">
                                                {{ $u->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>

                                        <form action="{{ route('superadmin.users.destroy', $u) }}" method="POST" onsubmit="return confirm('Delete user {{ $u->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-[#DC2626] hover:bg-[#FEF2F2] rounded" title="Delete User">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-xs text-[#929292]">
                                No users found matching current filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Select All Bar -->
        <div class="flex items-center justify-between p-3 bg-white border-b border-[#E6E6E6] text-xs md:hidden">
            <label class="flex items-center gap-2.5 font-bold text-[#111111] cursor-pointer">
                <input type="checkbox" data-bulk-select-all class="rounded border-[#D0D5DD] text-black focus:ring-black w-4 h-4">
                <span>Select All on Page</span>
            </label>
            <span class="text-[11px] text-[#666666] font-mono">{{ $users->total() }} accounts</span>
        </div>

        <!-- Mobile Stacked View -->
        <div class="md:hidden divide-y divide-[#E6E6E6]">
            @forelse($users as $u)
                <div class="p-4 space-y-2.5 transition" data-card-item>
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-start gap-2.5">
                            <label class="cursor-pointer p-1 -m-1 inline-flex items-center shrink-0">
                                <input type="checkbox" data-bulk-item value="{{ $u->id }}" {{ $u->id === auth()->id() ? 'disabled' : '' }} class="rounded border-[#D0D5DD] text-black focus:ring-black w-4 h-4 mt-0.5">
                            </label>
                            <div>
                                <div class="font-bold text-[#111111] text-sm flex items-center gap-1.5">
                                    <span>{{ $u->name }}</span>
                                    @if($u->id === auth()->id())
                                        <span class="text-[9px] px-1 py-0.2 rounded bg-[#B7FF00]/30 text-[#101010] font-bold">You</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-[#929292] font-mono">{{ $u->email }}</div>
                            </div>
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
                        <span class="capitalize font-mono">{{ str_replace('_', ' ', $u->role) }}</span>
                        <span>{{ $u->tenant ? $u->tenant->name : 'Global Platform' }}</span>
                    </div>

                    <div class="pt-2 border-t border-[#E6E6E6] flex items-center justify-end gap-1.5 flex-wrap">
                        <a href="{{ route('superadmin.users.permissions', $u) }}" class="btn-secondary text-xs px-2.5 py-1 min-h-[32px] inline-flex items-center gap-1 text-[#079455]">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            <span>Permissions</span>
                        </a>

                        <a href="{{ route('superadmin.users.edit', $u) }}" class="btn-secondary text-xs px-2.5 py-1 min-h-[32px] inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            <span>Edit</span>
                        </a>

                        @if($u->id !== auth()->id())
                            <form action="{{ route('superadmin.users.status', $u) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-secondary text-xs px-2.5 py-1 min-h-[32px]">
                                    {{ $u->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            <form action="{{ route('superadmin.users.destroy', $u) }}" method="POST" onsubmit="return confirm('Delete user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1 bg-[#FEF2F2] text-[#DC2626] font-semibold rounded-lg text-xs min-h-[32px]">
                                    Delete
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-xs text-[#929292]">
                    No users found matching current filters.
                </div>
            @endforelse
        </div>

        @if($users->hasPages())
            <div class="p-3 border-t border-[#E6E6E6]">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Reusable Bulk Action Bar for Users -->
    <x-bulk-action-bar :action="route('superadmin.users.bulk')" id="bulk-users-form">
        <button type="button" data-bulk-action="activate" class="px-3.5 py-2 rounded-xl bg-[#079455] hover:bg-[#067644] text-white font-bold text-xs transition shadow-xs active:scale-95 shrink-0">
            Activate
        </button>
        <button type="button" data-bulk-action="suspend" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-black font-bold text-xs transition shadow-xs active:scale-95 shrink-0">
            Suspend
        </button>
        <button type="button" data-bulk-action="delete" data-confirm="Are you sure you want to permanently delete :count selected user account(s)?" data-confirm-title="Delete Selected Users" data-confirm-variant="danger" class="px-3.5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs transition shadow-xs active:scale-95 shrink-0">
            Delete
        </button>
    </x-bulk-action-bar>

    <!-- Create User Modal -->
    <div id="addUserModal" class="modal-backdrop hidden" onclick="if(event.target === this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-[#E6E6E6] space-y-4 my-auto max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#B7FF00]"></span>
                    <h3 class="text-sm font-bold text-[#111111]">Create Platform User</h3>
                </div>
                <button type="button" onclick="document.getElementById('addUserModal').classList.add('hidden')" class="text-[#929292] hover:text-[#111111] p-1 text-lg font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('superadmin.users.store') }}" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Full Name *</label>
                    <input type="text" name="name" required placeholder="User name"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Email Address *</label>
                    <input type="email" name="email" required placeholder="user@domain.com"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Phone Number</label>
                    <input type="text" name="phone" placeholder="10 Digits"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm text-[#111111] font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Role *</label>
                    <select name="role" required class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-bold text-[#111111] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black min-h-[38px]">
                        <option value="cashier" selected>Store Cashier (Counter billing only)</option>
                        <option value="tenant_admin">Store Admin (Full store manager)</option>
                        <option value="super_admin">Super Admin (Platform operator)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Assign to Business Store</label>
                    <select name="tenant_id" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-semibold text-[#111111] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black min-h-[38px]">
                        <option value="">None / Super Admin Only</option>
                        @foreach($tenants as $t)
                            <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->slug }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Password *</label>
                    <div class="relative">
                        <input type="password" name="password" required placeholder="Minimum 6 characters"
                            class="w-full pl-3 pr-10 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                        <x-password-toggle-btn />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#E6E6E6]">
                    <button type="button" onclick="document.getElementById('addUserModal').classList.add('hidden')" class="btn-secondary min-h-[44px]">
                        Cancel
                    </button>
                    <button type="submit" class="btn-lime min-h-[44px]">
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

