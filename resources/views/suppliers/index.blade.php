{{-- Hallmark · macrostructure: Supplier Roster Hub · theme: Tactile Sage & Deep Rail · genre: modern-minimal
 * pre-emit critique: P5 H5 E5 S5 R5 V5
 * slop test: pass
--}}
@extends('layouts.app')

@php
    $tenant = $tenant ?? \App\Services\TenantContext::get();
    $user = auth()->user();
@endphp

@section('title', 'Suppliers & Inward Vendors')
@section('page-title', 'Suppliers')

@section('content')
<div class="space-y-4 w-full">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-[#111111] tracking-tight" style="overflow-wrap: anywhere; min-width: 0;">Suppliers & Inward Vendors</h1>
            <p class="text-xs sm:text-sm text-[#666666]">Distributors, wholesalers, and individuals who supply inventory devices</p>
        </div>

        @if($user?->hasPermission('suppliers.create'))
            <button type="button" onclick="document.getElementById('addSupplierModal').classList.remove('hidden')" class="btn-lime min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span class="whitespace-nowrap font-bold">Add Supplier</span>
            </button>
        @endif
    </div>

    <!-- Search Toolbar -->
    <div class="card-surface p-3.5 shadow-2xs">
        <form method="GET" action="{{ route('suppliers.index') }}" class="flex flex-col sm:flex-row gap-2">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search supplier name, company, or phone..."
                    class="w-full pl-9 pr-4 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] placeholder-[#929292] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] min-h-[44px]">
                <svg class="w-4 h-4 text-[#929292] absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-[#111111] hover:bg-black text-white rounded-xl text-xs sm:text-sm font-bold min-h-[44px] transition">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('suppliers.index') }}" class="btn-secondary min-h-[44px]">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Suppliers Grid -->
    @if($suppliers->isEmpty())
        <div class="card-surface p-12 text-center shadow-2xs">
            <div class="w-12 h-12 rounded-xl bg-[#F7F7F6] border border-[#E6E6E6] text-[#666666] flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <h3 class="text-sm font-bold text-[#111111]">No suppliers found</h3>
            <p class="text-xs text-[#666666] mt-1 mb-4">Add your first supplier or distributor to record device inward origins.</p>
            <button type="button" onclick="document.getElementById('addSupplierModal').classList.remove('hidden')" class="btn-lime min-h-[40px] text-xs inline-flex items-center gap-1.5 font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Register Supplier</span>
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($suppliers as $s)
                <div class="card-surface p-4 sm:p-5 shadow-2xs hover:border-[#D6D6D6] transition flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-start justify-between gap-2">
                            <div class="w-10 h-10 rounded-xl bg-[#F7F7F6] border border-[#E6E6E6] text-[#111111] flex items-center justify-center font-black text-sm uppercase">
                                {{ substr($s->name, 0, 1) }}
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#F2F4F7] text-[#344054] border border-[#EAECF0] font-mono">
                                {{ $s->items_count }} items inward
                            </span>
                        </div>

                        <div class="mt-3">
                            <h3 class="text-base font-bold text-[#111111]" style="overflow-wrap: anywhere; min-width: 0;">{{ $s->name }}</h3>
                            @if($s->company_name)<p class="text-xs text-[#111111] font-semibold mt-0.5">{{ $s->company_name }}</p>@endif
                            <p class="text-xs font-mono text-[#666666] mt-1">{{ $s->phone ?? 'No phone recorded' }}</p>
                            @if($s->gstin)<p class="text-[11px] font-mono text-[#929292] mt-0.5">GST: {{ $s->gstin }}</p>@endif
                            @if($s->address)<p class="text-[11px] text-[#666666] mt-1 line-clamp-1">{{ $s->address }}</p>@endif
                        </div>
                    </div>

                    <div class="pt-3 border-t border-[#E6E6E6] flex items-center justify-between text-xs">
                        <span class="text-[#929292] font-mono text-[10px]">{{ $s->id_proof_number ?: 'General Supplier' }}</span>
                        <a href="{{ route('items.index', ['search' => $s->name]) }}" class="btn-secondary text-xs px-3 py-1.5 min-h-[36px] inline-flex items-center gap-1">
                            <span>View Stock</span>
                            
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        @if($suppliers->hasPages())
            <div class="card-surface p-3 shadow-2xs">
                {{ $suppliers->links() }}
            </div>
        @endif
    @endif

    <!-- Add Supplier Modal -->
    <div id="addSupplierModal" class="modal-backdrop hidden" onclick="if(event.target === this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-[#E6E6E6] space-y-4 my-auto max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#B7FF00]"></span>
                    <h3 class="text-sm font-bold text-[#111111]">Register New Supplier</h3>
                </div>
                <button type="button" onclick="document.getElementById('addSupplierModal').classList.add('hidden')" class="text-[#929292] hover:text-[#111111] p-1 text-lg font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Contact / Supplier Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Tariq Ahmad"
                        class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                </div>

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'supplier', 'company_name'))
                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Company / Wholesaler Name</label>
                        <input type="text" name="company_name" placeholder="e.g. Valley Mobile Distributors"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'supplier', 'phone'))
                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Phone Number {{ \App\Services\DynamicFormConfig::isFieldRequired($tenant, 'supplier', 'phone') ? '*' : '' }}</label>
                        <input type="text" name="phone" {{ \App\Services\DynamicFormConfig::isFieldRequired($tenant, 'supplier', 'phone') ? 'required' : '' }} placeholder="10 Digits"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'supplier', 'gstin'))
                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">GSTIN (If Registered)</label>
                        <input type="text" name="gstin" placeholder="01XXXXX0000X1ZX"
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'supplier', 'address'))
                    <div>
                        <label class="block text-xs font-bold text-[#344054] mb-1">Supplier Address</label>
                        <textarea name="address" rows="2" placeholder="Distributor address / market location..."
                            class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]"></textarea>
                    </div>
                @endif

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#E6E6E6]">
                    <button type="button" onclick="document.getElementById('addSupplierModal').classList.add('hidden')" class="btn-secondary min-h-[44px]">
                        Cancel
                    </button>
                    <button type="submit" class="btn-lime min-h-[44px]">
                        Save Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
