{{-- Hallmark · macrostructure: Customer Ledger Roster · theme: Tactile Sage & Deep Rail · genre: modern-minimal
 * pre-emit critique: P5 H5 E5 S5 R5 V5
 * slop test: pass
--}}
@extends('layouts.app')

@php
    $tenant = $tenant ?? \App\Services\TenantContext::get();
    $user = auth()->user();
@endphp

@section('title', 'Customer Directory & Khata')
@section('page-title', 'Customer Khata')

@section('content')
<div class="space-y-4 w-full">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-[#111111] tracking-tight" style="overflow-wrap: anywhere; min-width: 0;">Customer Directory & Khata</h1>
            <p class="text-xs sm:text-sm text-[#666666]">Contact records, purchase history, and outstanding credit khata</p>
        </div>

        <button type="button" onclick="document.getElementById('addCustomerModal').classList.remove('hidden')" class="btn-lime min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            <span class="whitespace-nowrap font-bold">Add Customer</span>
        </button>
    </div>

    <!-- Search Toolbar -->
    <div class="card-surface p-3.5 shadow-2xs">
        <form method="GET" action="{{ route('customers.index') }}" class="flex flex-col sm:flex-row gap-2">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by customer name, phone number, or ID..."
                    class="w-full pl-9 pr-4 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] placeholder-[#929292] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] min-h-[44px]">
                <svg class="w-4 h-4 text-[#929292] absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                <button type="submit" class="flex-1 sm:flex-initial px-5 py-2.5 bg-[#111111] hover:bg-black text-white rounded-xl text-xs sm:text-sm font-bold min-h-[44px] transition">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('customers.index') }}" class="btn-secondary min-h-[44px]">Reset</a>
                @endif
                <label class="inline-flex items-center gap-2 text-xs font-semibold text-[#666666] cursor-pointer bg-[#F7F7F6] px-3 py-2.5 rounded-xl border border-[#E6E6E6] min-h-[44px]">
                    <input type="checkbox" data-bulk-select-all class="rounded border-[#D0D5DD] text-black focus:ring-black w-4 h-4">
                    <span>Select Page</span>
                </label>
            </div>
        </form>
    </div>

    <!-- Customers Grid -->
    @if($customers->isEmpty())
        <div class="card-surface p-12 text-center shadow-2xs">
            <div class="w-12 h-12 rounded-xl bg-[#F7F7F6] border border-[#E6E6E6] text-[#666666] flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <h3 class="text-sm font-bold text-[#111111]">No customers found</h3>
            <p class="text-xs text-[#666666] mt-1 mb-4">Add your first customer contact or sell a device to register automatically.</p>
            <button type="button" onclick="document.getElementById('addCustomerModal').classList.remove('hidden')" class="btn-lime min-h-[40px] text-xs inline-flex items-center gap-1.5 font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Register Customer</span>
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($customers as $c)
                @php
                    $pending = $c->pendingBalance();
                    $spent = $c->totalSpent();
                @endphp
                <div class="card-surface p-4 sm:p-5 shadow-2xs hover:border-[#D6D6D6] transition flex flex-col justify-between space-y-4" data-card-item>
                    <div>
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2.5">
                                <label class="cursor-pointer p-1 -m-1 inline-flex items-center">
                                    <input type="checkbox" data-bulk-item value="{{ $c->id }}" class="rounded border-[#D0D5DD] text-black focus:ring-black w-4 h-4 cursor-pointer">
                                </label>
                                <div class="w-10 h-10 rounded-xl bg-[#F7F7F6] border border-[#E6E6E6] text-[#111111] flex items-center justify-center font-black text-sm uppercase">
                                    {{ substr($c->name, 0, 1) }}
                                </div>
                            </div>
                            @if($pending > 0)
                                <span class="badge-due">
                                    DUE ₹{{ number_format($pending, 0) }}
                                </span>
                            @else
                                <span class="badge-paid">
                                    Settled
                                </span>
                            @endif
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('customers.show', $c) }}" class="text-base font-bold text-[#111111] hover:text-[#000000] hover:underline" style="overflow-wrap: anywhere; min-width: 0;">
                                {{ $c->name }}
                            </a>
                            <p class="text-xs font-mono text-[#666666] mt-0.5">{{ $c->phone }}</p>
                            @if($c->email)<p class="text-[11px] text-[#929292] truncate mt-0.5">{{ $c->email }}</p>@endif
                            @if($c->address)<p class="text-[11px] text-[#666666] mt-1 line-clamp-1">{{ $c->address }}</p>@endif
                        </div>
                    </div>

                    <div class="pt-3 border-t border-[#E6E6E6] flex items-center justify-between text-xs">
                        <div>
                            <span class="text-[10px] text-[#929292] block uppercase tracking-wider font-semibold">Total Purchases</span>
                            <span class="font-bold text-[#111111] font-mono">₹{{ number_format($spent, 0) }} <span class="text-[10px] font-normal text-[#666666]">({{ $c->invoices->count() }} bills)</span></span>
                        </div>
                        <a href="{{ route('customers.show', $c) }}" class="btn-secondary text-xs px-3 py-1.5 min-h-[36px] inline-flex items-center gap-1">
                            <span>Ledger</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        @if($customers->hasPages())
            <div class="card-surface p-3 shadow-2xs">
                {{ $customers->links() }}
            </div>
        @endif
    @endif

    <!-- Reusable Bulk Action Bar for Customers -->
    <x-bulk-action-bar :action="route('customers.bulk')" id="bulk-customers-form">
        @if(auth()->user()->hasPermission('customers.delete'))
            <button type="button" data-bulk-action="delete" data-confirm="Are you sure you want to delete :count selected customer record(s)? Note: Customers with billing history are safely protected from deletion." data-confirm-title="Delete Selected Customers" data-confirm-variant="danger" class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs transition shadow-xs active:scale-95 shrink-0">
                Delete Selected
            </button>
        @endif
    </x-bulk-action-bar>

    <!-- Add Customer Modal -->
    <div id="addCustomerModal" class="modal-backdrop hidden" onclick="if(event.target === this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-[#E6E6E6] space-y-4 my-auto max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#B7FF00]"></span>
                    <h3 class="text-sm font-bold text-[#111111]">Register New Customer</h3>
                </div>
                <button type="button" onclick="document.getElementById('addCustomerModal').classList.add('hidden')" class="text-[#929292] hover:text-[#111111] p-1 text-lg font-bold">
                    &times;
                </button>
            </div>

            <form method="POST" action="{{ route('customers.store') }}" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Customer Name *</label>
                    <input type="text" name="name" required placeholder="Full Name"
                        class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                </div>

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'customer', 'phone'))
                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Phone Number {{ \App\Services\DynamicFormConfig::isFieldRequired($tenant, 'customer', 'phone') ? '*' : '' }}</label>
                        <input type="text" name="phone" {{ \App\Services\DynamicFormConfig::isFieldRequired($tenant, 'customer', 'phone') ? 'required' : '' }} placeholder="10 Digits Mobile"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'customer', 'email'))
                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Email (Optional)</label>
                        <input type="email" name="email" placeholder="customer@mail.com"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'customer', 'address'))
                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Address</label>
                        <textarea name="address" rows="2" placeholder="Customer locality / town..."
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]"></textarea>
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'customer', 'state'))
                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">State / Province</label>
                        <input type="text" name="state" placeholder="e.g. Jammu & Kashmir"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'customer', 'gstin'))
                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Customer GSTIN (For B2B)</label>
                        <input type="text" name="gstin" placeholder="01AAAAA0000A1Z5"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>
                @endif

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#E6E6E6]">
                    <button type="button" onclick="document.getElementById('addCustomerModal').classList.add('hidden')" class="btn-secondary min-h-[44px]">
                        Cancel
                    </button>
                    <button type="submit" class="btn-lime min-h-[44px]">
                        Save Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
