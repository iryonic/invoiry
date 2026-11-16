{{-- Hallmark · macrostructure: Customer Khata Ledger · theme: Tactile Sage & Deep Rail · genre: modern-minimal
 * pre-emit critique: P5 H5 E5 S5 R5 V5
 * slop test: pass
 --}}
@extends('layouts.app')

@php
    $tenant = $tenant ?? \App\Services\TenantContext::get();
    $pending = $customer->pendingBalance();
    $totalSpent = $customer->totalSpent();
    $totalPaid = $customer->totalPaid();
    $cleanPhone = preg_replace('/[^0-9]/', '', $customer->phone ?? '');
@endphp

@section('title', 'Ledger · ' . $customer->name)
@section('page-title', 'Customer Khata')
@section('page-subtitle', 'Account balance, transaction history, and credit settlement')

@section('content')
<div class="max-w-6xl mx-auto space-y-5 w-full">

    <!-- Top Action & Navigation Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <a href="{{ route('customers.index') }}" class="btn-secondary min-h-[42px] text-xs inline-flex items-center gap-1.5 self-start sm:self-auto">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Back to Khata Directory</span>
        </a>

        <!-- Action Buttons Cluster (Responsive & Touch-Friendly) -->
        <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
            @if($customer->phone && $cleanPhone)
                <a href="tel:{{ $customer->phone }}" title="Call Customer" class="btn-secondary min-h-[42px] px-3 text-xs inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#111111]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    <span class="hidden md:inline">Call</span>
                </a>

                @if($pending > 0)
                    <a href="https://wa.me/91{{ $cleanPhone }}?text={{ urlencode('Dear ' . $customer->name . ', a balance of ₹' . number_format($pending, 0) . ' is pending on your account at ' . ($tenant->name ?? 'our store') . '. Please settle at your earliest convenience. Thank you!') }}"
                       target="_blank" rel="noopener noreferrer" title="WhatsApp Due Reminder"
                       class="btn-secondary min-h-[42px] px-3 text-xs inline-flex items-center gap-1.5 text-[#079455] hover:text-[#067647]">
                        <svg class="w-4 h-4 text-[#079455]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.54 1.771.82 2.791.82 3.181 0 5.767-2.586 5.768-5.766 0-3.18-2.587-5.766-5.768-5.766zm3.385 8.165c-.14.394-.717.728-1.025.772-.307.043-.708.062-2.315-.602-1.606-.664-2.613-2.28-2.693-2.387-.08-.107-.648-.862-.648-1.644 0-.782.411-1.168.558-1.328.146-.16.321-.2.428-.2.107 0 .214.001.308.005.101.005.236-.038.369.281.14.335.481 1.173.522 1.258.041.085.068.185.012.298-.056.113-.085.185-.168.284-.084.099-.177.221-.253.298-.085.085-.174.177-.075.347.099.17.439.724.942 1.172.648.577 1.194.756 1.364.841.17.085.271.071.371-.043.101-.114.428-.499.542-.67.114-.171.229-.143.386-.085.157.057.994.469 1.165.554.171.085.285.128.328.199.043.071.043.413-.097.807z"/>
                        </svg>
                        <span class="hidden md:inline">WhatsApp Due</span>
                    </a>
                @endif
            @endif

            <button type="button" onclick="document.getElementById('editCustomerModal').classList.remove('hidden')" class="btn-secondary min-h-[42px] text-xs inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-[#111111]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                <span>Edit Profile</span>
            </button>

            @if($pending > 0)
                <button type="button" onclick="document.getElementById('recordKhataModal').classList.remove('hidden')" class="btn-lime min-h-[42px] text-xs inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#101010]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="font-bold">Settle Khata</span>
                </button>
            @endif

            @if($tenant?->supportsRepair())
                <a href="{{ route('repairs.create', ['customer_id' => $customer->id]) }}" class="btn-secondary min-h-[42px] text-xs inline-flex items-center gap-1.5 bg-[#0369A1] text-white hover:bg-[#0284C7] border-transparent">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="font-bold">New Repair</span>
                </a>
            @endif

            @if($tenant?->supportsSelling())
                <a href="{{ route('invoices.create', ['customer_id' => $customer->id]) }}" class="btn-secondary min-h-[42px] text-xs inline-flex items-center gap-1.5 bg-[#050505] text-white hover:bg-black hover:text-[#B7FF00] border-black">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    <span class="font-bold">New Bill</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Status Banners -->
    @if(session('success'))
        <div class="p-3.5 bg-[#ECFDF3] border border-[#D1FADF] text-[#079455] rounded-xl text-xs font-semibold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-[#079455]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-[#079455] hover:opacity-75 text-sm">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-3.5 bg-[#FEF2F2] border border-[#FECDCA] text-[#B42318] rounded-xl text-xs font-semibold">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Customer Overview Hero Banner (Responsive Layout) -->
    <div class="card-surface p-5 sm:p-6 shadow-2xs flex flex-col lg:flex-row lg:items-center justify-between gap-6">

        <!-- Identity & Contact Block -->
        <div class="flex items-start gap-4 min-w-0 flex-1">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#050505] text-[#B7FF00] flex items-center justify-center font-black text-2xl uppercase shrink-0 shadow-xs border border-black/10">
                {{ substr($customer->name, 0, 1) }}
            </div>

            <div class="space-y-1.5 min-w-0 flex-1">
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h1 class="text-xl sm:text-2xl font-black text-[#111111] tracking-tight" style="overflow-wrap: anywhere; min-width: 0;">
                        {{ $customer->name }}
                    </h1>
                    @if($pending > 0)
                        <span class="px-2 py-0.5 rounded-full bg-[#FFFBEB] text-[#D97706] border border-[#FEF3C7] text-[10px] font-mono font-bold uppercase">
                            Khata Active
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full bg-[#ECFDF3] text-[#079455] border border-[#D1FADF] text-[10px] font-mono font-bold uppercase">
                            Account Settled
                        </span>
                    @endif
                </div>

                <!-- Contact & Location Badges -->
                <div class="flex items-center gap-2.5 text-xs text-[#666666] flex-wrap">
                    @if($customer->phone)
                        <a href="tel:{{ $customer->phone }}" class="font-mono font-bold text-[#111111] hover:underline inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-[#929292]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span>{{ $customer->phone }}</span>
                        </a>
                    @endif

                    @if($customer->email)
                        <span class="text-[#D0D5DD] hidden sm:inline">&bull;</span>
                        <a href="mailto:{{ $customer->email }}" class="text-[#666666] hover:underline inline-flex items-center gap-1 truncate max-w-[200px]">
                            <svg class="w-3.5 h-3.5 text-[#929292]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span>{{ $customer->email }}</span>
                        </a>
                    @endif

                    @if($customer->gstin)
                        <span class="text-[#D0D5DD] hidden sm:inline">&bull;</span>
                        <span class="px-2 py-0.5 rounded bg-[#F7F7F6] border border-[#E6E6E6] font-mono text-[11px] font-bold text-[#111111]">
                            GST: {{ $customer->gstin }}
                        </span>
                    @endif
                </div>

                @if($customer->address || $customer->city || $customer->state)
                    <p class="text-xs text-[#666666] flex items-center gap-1.5 pt-0.5">
                        <svg class="w-3.5 h-3.5 text-[#929292] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>{{ collect([$customer->address, $customer->city, $customer->state])->filter()->join(', ') }}</span>
                    </p>
                @endif
            </div>
        </div>

        <!-- Financial Metrics Strip (Responsive 3-Box Grid) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 sm:gap-3 w-full lg:w-auto shrink-0 pt-3 lg:pt-0 border-t lg:border-t-0 border-[#E6E6E6]">
            <!-- Card 1: Total Spent -->
            <div class="p-3 sm:p-3.5 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] text-left sm:text-right">
                <span class="text-[10px] font-bold text-[#929292] uppercase tracking-wider block">Total Billed</span>
                <div class="text-base sm:text-lg font-black text-[#111111] mt-0.5 font-mono">₹{{ number_format($totalSpent, 0) }}</div>
                <span class="text-[10px] text-[#666666] font-mono">{{ $customer->invoices->count() }} bills</span>
            </div>

            <!-- Card 2: Total Paid -->
            <div class="p-3 sm:p-3.5 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] text-left sm:text-right">
                <span class="text-[10px] font-bold text-[#929292] uppercase tracking-wider block">Collected</span>
                <div class="text-base sm:text-lg font-black text-[#079455] mt-0.5 font-mono">₹{{ number_format($totalPaid, 0) }}</div>
                <span class="text-[10px] text-[#666666] font-mono">{{ $customer->payments->count() }} payments</span>
            </div>

            <!-- Card 3: Khata Due Balance -->
            <div class="col-span-2 sm:col-span-1 p-3 sm:p-3.5 rounded-xl border {{ $pending > 0 ? 'bg-[#FFFBEB] border-[#FEF3C7]' : 'bg-[#ECFDF3] border-[#D1FADF]' }} text-left sm:text-right flex sm:block items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold {{ $pending > 0 ? 'text-[#D97706]' : 'text-[#079455]' }} uppercase tracking-wider block">Balance Due</span>
                    <div class="text-base sm:text-lg font-black {{ $pending > 0 ? 'text-[#D97706]' : 'text-[#079455]' }} mt-0.5 font-mono">₹{{ number_format($pending, 0) }}</div>
                </div>
                <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded {{ $pending > 0 ? 'bg-[#FEF3C7] text-[#B45309]' : 'bg-[#D1FADF] text-[#067647]' }}">
                    {{ $pending > 0 ? 'Pending Khata' : 'All Clear' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Ledger Content Tabs -->
    <div class="space-y-3">
        <!-- Tab Bar -->
        <div class="flex items-center gap-1.5 border-b border-[#E6E6E6] pb-1 overflow-x-auto scrollbar-none">
            <button type="button" onclick="switchCustomerTab('invoices')" id="tabBtn-invoices"
                class="customer-tab-btn flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-bold bg-[#111111] text-[#B7FF00] whitespace-nowrap transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Purchase Invoices ({{ $customer->invoices->count() }})</span>
            </button>

            <button type="button" onclick="switchCustomerTab('payments')" id="tabBtn-payments"
                class="customer-tab-btn flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold text-[#666666] hover:text-[#111111] hover:bg-white whitespace-nowrap transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span>Payment Receipts ({{ $customer->payments->count() }})</span>
            </button>

            @if($tenant?->supportsRepair())
                <button type="button" onclick="switchCustomerTab('repairs')" id="tabBtn-repairs"
                    class="customer-tab-btn flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold text-[#666666] hover:text-[#111111] hover:bg-white whitespace-nowrap transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>Repair Orders ({{ $customer->repairJobs?->count() ?? 0 }})</span>
                </button>
            @endif

            <button type="button" onclick="switchCustomerTab('details')" id="tabBtn-details"
                class="customer-tab-btn flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold text-[#666666] hover:text-[#111111] hover:bg-white whitespace-nowrap transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span>Customer Profile & Notes</span>
            </button>
        </div>

        <!-- TAB 1: Invoices & Bills -->
        <div id="tabPanel-invoices" class="customer-tab-panel space-y-4">
            <div class="card-surface p-4 sm:p-6 shadow-2xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-[#E6E6E6] pb-3">
                    <div>
                        <h2 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Purchase History & Billing Ledger</h2>
                        <p class="text-[11px] text-[#666666]">Itemized device invoices generated for this account</p>
                    </div>

                    <!-- Filter Pills -->
                    <div class="flex items-center gap-1.5 self-start sm:self-auto text-xs">
                        <button type="button" onclick="filterInvoices('all')" id="flt-all" class="inv-flt-btn px-2.5 py-1 rounded-md text-[11px] font-bold bg-[#111111] text-[#B7FF00]">
                            All ({{ $customer->invoices->count() }})
                        </button>
                        <button type="button" onclick="filterInvoices('due')" id="flt-due" class="inv-flt-btn px-2.5 py-1 rounded-md text-[11px] font-semibold text-[#666666] hover:text-[#111111] hover:bg-[#F7F7F6]">
                            Dues ({{ $customer->invoices->whereIn('payment_status', ['unpaid', 'partial'])->count() }})
                        </button>
                        <button type="button" onclick="filterInvoices('paid')" id="flt-paid" class="inv-flt-btn px-2.5 py-1 rounded-md text-[11px] font-semibold text-[#666666] hover:text-[#111111] hover:bg-[#F7F7F6]">
                            Paid ({{ $customer->invoices->where('payment_status', 'paid')->count() }})
                        </button>
                    </div>
                </div>

                @if($customer->invoices->isEmpty())
                    <div class="py-12 text-center space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-[#F7F7F6] text-[#929292] mx-auto flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <p class="text-xs text-[#666666] font-medium">No invoices recorded for this customer yet.</p>
                        <a href="{{ route('invoices.create', ['customer_id' => $customer->id]) }}" class="btn-lime text-xs inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                            <span>Generate First Invoice</span>
                        </a>
                    </div>
                @else
                    <!-- Responsive List for All Viewports -->
                    <div class="divide-y divide-[#E6E6E6]" id="invoicesList">
                        @foreach($customer->invoices as $inv)
                            @php
                                $dueOnThis = max(0, (float) $inv->grand_total - (float) $inv->amount_paid);
                                $statusGroup = ($inv->payment_status === 'paid') ? 'paid' : 'due';
                            @endphp
                            <div class="py-4 space-y-3 invoice-row" data-status="{{ $statusGroup }}">
                                <!-- Top Row: Number, Status, Date, Actions -->
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="{{ route('invoices.show', $inv) }}" class="font-mono text-sm font-black text-[#111111] hover:underline flex items-center gap-1.5">
                                            <span>{{ $inv->invoice_number }}</span>
                                            <svg class="w-3.5 h-3.5 text-[#929292]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>

                                        @if($inv->payment_status === 'paid')
                                            <span class="badge-paid">Paid</span>
                                        @elseif($inv->payment_status === 'partial')
                                            <span class="badge-due">Due ₹{{ number_format($dueOnThis, 0) }}</span>
                                        @else
                                            <span class="badge-unpaid">Unpaid</span>
                                        @endif

                                        <span class="text-xs text-[#929292] font-mono">&bull; {{ $inv->invoice_date->format('d M Y') }}</span>
                                        <span class="text-[11px] px-1.5 py-0.5 rounded bg-[#F7F7F6] text-[#666666] font-mono capitalize">{{ $inv->payment_method }}</span>
                                    </div>

                                    <!-- Financial Totals & Link -->
                                    <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0">
                                        <div class="text-right">
                                            <div class="text-sm font-black text-[#111111] font-mono">₹{{ number_format($inv->grand_total, 2) }}</div>
                                            <div class="text-[10px] font-mono text-[#666666]">
                                                Paid: ₹{{ number_format($inv->amount_paid, 2) }}
                                                @if($dueOnThis > 0)
                                                    <span class="text-[#D97706] font-bold">&bull; Due: ₹{{ number_format($dueOnThis, 2) }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-1.5">
                                            <a href="{{ route('invoices.show', $inv) }}" class="btn-secondary text-xs px-2.5 py-1.5 min-h-[36px] inline-flex items-center gap-1" title="View & Print Bill">
                                                <span>View</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Items Chips Breakdown -->
                                @if($inv->items->isNotEmpty())
                                    <div class="p-2.5 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] space-y-1.5">
                                        <div class="text-[10px] font-bold text-[#929292] uppercase font-mono tracking-wider">Purchased Items</div>
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            @foreach($inv->items as $item)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white border border-[#E6E6E6] text-xs text-[#111111]">
                                                    <span class="font-bold">{{ $item->item_name }}</span>
                                                    @if($item->storage)
                                                        <span class="text-[10px] font-mono px-1 py-0.5 rounded bg-[#F2F4F7] text-[#344054]">{{ $item->storage }}</span>
                                                    @endif
                                                    @if($item->imei_1)
                                                        <span class="text-[10px] font-mono text-[#929292]">IMEI: {{ $item->imei_1 }}</span>
                                                    @endif
                                                    <span class="text-xs font-mono font-bold text-[#111111]">&bull; ₹{{ number_format($item->selling_price, 0) }}</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- TAB 2: Payment Receipts Ledger -->
        <div id="tabPanel-payments" class="customer-tab-panel hidden space-y-4">
            <div class="card-surface p-4 sm:p-6 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                    <div>
                        <h2 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Payment Receipts & Credits</h2>
                        <p class="text-[11px] text-[#666666]">All payments recorded across invoices and Khata deposits</p>
                    </div>

                    @if($pending > 0)
                        <button type="button" onclick="document.getElementById('recordKhataModal').classList.remove('hidden')" class="btn-lime text-xs min-h-[36px]">
                            <span>+ Record Payment</span>
                        </button>
                    @endif
                </div>

                @if($customer->payments->isEmpty())
                    <div class="py-10 text-center text-xs text-[#929292] space-y-2">
                        <p>No standalone payment records found for this customer.</p>
                        <p class="text-[11px] text-[#666666]">Payments recorded directly on invoice creations appear on invoice profiles.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#F7F7F6] border-b border-[#E6E6E6] text-[#666666] font-mono text-[10px] uppercase">
                                <tr>
                                    <th class="py-2.5 px-3">Date</th>
                                    <th class="py-2.5 px-3">Amount</th>
                                    <th class="py-2.5 px-3">Method</th>
                                    <th class="py-2.5 px-3">Associated Bill</th>
                                    <th class="py-2.5 px-3">Reference / Transaction ID</th>
                                    <th class="py-2.5 px-3">Notes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E6E6E6] text-[#111111]">
                                @foreach($customer->payments as $pmt)
                                    <tr class="hover:bg-[#F7F7F6]/50">
                                        <td class="py-3 px-3 font-mono font-medium">{{ $pmt->payment_date ? \Carbon\Carbon::parse($pmt->payment_date)->format('d M Y') : '—' }}</td>
                                        <td class="py-3 px-3 font-mono font-black text-[#079455]">₹{{ number_format($pmt->amount, 2) }}</td>
                                        <td class="py-3 px-3 uppercase font-mono font-bold text-[11px]">{{ $pmt->payment_method }}</td>
                                        <td class="py-3 px-3">
                                            @if($pmt->invoice)
                                                <a href="{{ route('invoices.show', $pmt->invoice) }}" class="font-mono text-[#111111] hover:underline font-bold">
                                                    {{ $pmt->invoice->invoice_number }}
                                                </a>
                                            @else
                                                <span class="text-[#929292] font-mono">General Khata Credit</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 font-mono text-[#666666]">{{ $pmt->reference_id ?? '—' }}</td>
                                        <td class="py-3 px-3 text-[#666666]">{{ $pmt->notes ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- TAB 3: Customer Profile & Notes -->
        <div id="tabPanel-details" class="customer-tab-panel hidden space-y-4">
            <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-5">
                <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                    <h2 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Account Specifications</h2>
                    <button type="button" onclick="document.getElementById('editCustomerModal').classList.remove('hidden')" class="btn-secondary text-xs px-3 py-1.5 min-h-[36px]">
                        Edit Details
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="p-3.5 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] space-y-1">
                        <span class="text-[10px] font-bold text-[#929292] uppercase font-mono block">Full Name</span>
                        <div class="text-sm font-bold text-[#111111]">{{ $customer->name }}</div>
                    </div>

                    <div class="p-3.5 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] space-y-1">
                        <span class="text-[10px] font-bold text-[#929292] uppercase font-mono block">Primary Phone</span>
                        <div class="text-sm font-bold font-mono text-[#111111]">{{ $customer->phone ?? 'Not provided' }}</div>
                    </div>

                    <div class="p-3.5 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] space-y-1">
                        <span class="text-[10px] font-bold text-[#929292] uppercase font-mono block">Email Address</span>
                        <div class="text-sm font-medium text-[#111111] truncate">{{ $customer->email ?? 'Not provided' }}</div>
                    </div>

                    <div class="p-3.5 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] space-y-1">
                        <span class="text-[10px] font-bold text-[#929292] uppercase font-mono block">GSTIN / Tax ID</span>
                        <div class="text-sm font-mono font-bold text-[#111111]">{{ $customer->gstin ?? 'Unregistered' }}</div>
                    </div>

                    <div class="p-3.5 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] space-y-1">
                        <span class="text-[10px] font-bold text-[#929292] uppercase font-mono block">State / Region</span>
                        <div class="text-sm font-medium text-[#111111]">{{ $customer->state ?? ($customer->state_code ? 'Code: ' . $customer->state_code : 'Not set') }}</div>
                    </div>

                    <div class="p-3.5 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] space-y-1">
                        <span class="text-[10px] font-bold text-[#929292] uppercase font-mono block">Customer Since</span>
                        <div class="text-sm font-mono text-[#111111]">{{ $customer->created_at->format('d M Y') }}</div>
                    </div>

                    <div class="sm:col-span-2 md:col-span-3 p-3.5 bg-[#F7F7F6] rounded-xl border border-[#E6E6E6] space-y-1">
                        <span class="text-[10px] font-bold text-[#929292] uppercase font-mono block">Billing Address</span>
                        <div class="text-xs text-[#111111]">{{ $customer->address ?: 'No address specified' }}</div>
                    </div>

                    @if($customer->notes)
                        <div class="sm:col-span-2 md:col-span-3 p-3.5 bg-[#FFFBEB] rounded-xl border border-[#FEF3C7] space-y-1">
                            <span class="text-[10px] font-bold text-[#D97706] uppercase font-mono block">Khata Ledger Notes</span>
                            <div class="text-xs text-[#92400E]">{{ $customer->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if($tenant?->supportsRepair())
            <!-- TAB 4: Repair Jobs / Service Orders -->
            <div id="tabPanel-repairs" class="customer-tab-panel hidden space-y-4">
                <div class="card-surface p-4 sm:p-6 shadow-2xs space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-[#E6E6E6] pb-3">
                        <div>
                            <h2 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Device Repair History</h2>
                            <p class="text-[11px] text-[#666666]">Hardware and software service orders registered for this customer</p>
                        </div>
                        <a href="{{ route('repairs.create', ['customer_id' => $customer->id]) }}" class="btn-lime text-xs inline-flex items-center gap-1.5 font-bold">
                            <svg class="w-3.5 h-3.5 text-[#101010]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                            <span>+ Check In New Device</span>
                        </a>
                    </div>

                    @if(!isset($customer->repairJobs) || $customer->repairJobs->isEmpty())
                        <div class="py-12 text-center space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-[#E0F2FE] text-[#0284C7] mx-auto flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <p class="text-xs text-[#666666] font-medium">No repair orders logged for this customer yet.</p>
                            <div class="pt-1">
                                <a href="{{ route('repairs.create', ['customer_id' => $customer->id]) }}" class="btn-lime text-xs inline-flex items-center gap-1.5 font-bold">
                                    <span>Check In First Repair</span>
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead class="bg-[#F7F7F6] border-b border-[#E6E6E6] text-[#666666] uppercase tracking-wider font-semibold">
                                    <tr>
                                        <th class="py-3 px-3">Job #</th>
                                        <th class="py-3 px-3">Device &amp; Details</th>
                                        <th class="py-3 px-3">Reported Problem</th>
                                        <th class="py-3 px-3">Technician</th>
                                        <th class="py-3 px-3">Status</th>
                                        <th class="py-3 px-3 text-right">Cost &amp; Due</th>
                                        <th class="py-3 px-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#E6E6E6] text-[#111111]">
                                    @foreach($customer->repairJobs as $rjob)
                                        <tr class="hover:bg-[#F7F7F6]/60 transition">
                                            <td class="py-3 px-3 font-mono font-bold align-top">
                                                <a href="{{ route('repairs.show', $rjob) }}" class="hover:underline">
                                                    #{{ $rjob->job_number }}
                                                </a>
                                                <div class="text-[10px] text-[#888888] font-normal">
                                                    {{ $rjob->created_at->format('d M Y') }}
                                                </div>
                                            </td>
                                            <td class="py-3 px-3 align-top">
                                                <div class="font-bold text-[#111111]">{{ $rjob->device_brand }} {{ $rjob->device_model }}</div>
                                                @if($rjob->imei)
                                                    <div class="text-[10px] font-mono text-[#888888]">IMEI: {{ $rjob->imei }}</div>
                                                @endif
                                            </td>
                                            <td class="py-3 px-3 align-top max-w-[200px]">
                                                <div class="line-clamp-2 text-[#333333]">{{ $rjob->reported_problem }}</div>
                                            </td>
                                            <td class="py-3 px-3 align-top">
                                                {{ $rjob->technician->name ?? 'Unassigned' }}
                                            </td>
                                            <td class="py-3 px-3 align-top">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $rjob->status_info['color'] ?? 'bg-gray-100' }}">
                                                    {{ $rjob->status_info['label'] }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-3 align-top text-right font-mono">
                                                <div class="font-bold">₹{{ number_format($rjob->total_amount, 2) }}</div>
                                                @if($rjob->due_balance > 0)
                                                    <div class="text-[10px] font-semibold text-[#DC2626]">Due: ₹{{ number_format($rjob->due_balance, 2) }}</div>
                                                @else
                                                    <div class="text-[10px] font-semibold text-[#079455]">Settled</div>
                                                @endif
                                            </td>
                                            <td class="py-3 px-3 align-top text-right whitespace-nowrap">
                                                <a href="{{ route('repairs.show', $rjob) }}" class="btn-secondary text-xs px-2.5 py-1 inline-flex items-center gap-1 font-semibold">
                                                    <span>Workshop</span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- MODAL 1: Settle Khata / Record Payment -->
<div id="recordKhataModal" class="fixed inset-0 z-50 bg-black/60 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full border border-[#E6E6E6] shadow-xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="p-5 border-b border-[#E6E6E6] flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#B7FF00] border border-[#111111]"></span>
                <h3 class="text-sm font-bold text-[#111111] uppercase tracking-wider">Settle Khata Payment</h3>
            </div>
            <button type="button" onclick="document.getElementById('recordKhataModal').classList.add('hidden')" class="text-[#929292] hover:text-[#111111] text-lg font-bold">
                &times;
            </button>
        </div>

        <form method="POST" action="{{ route('customers.settle', $customer) }}" class="p-5 space-y-4">
            @csrf

            <div class="p-3 bg-[#FFFBEB] border border-[#FEF3C7] rounded-xl flex items-center justify-between text-xs">
                <span class="text-[#B45309] font-medium">Outstanding Due Balance:</span>
                <span class="font-mono font-black text-sm text-[#D97706]">₹{{ number_format($pending, 2) }}</span>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#344054] mb-1">Amount to Pay (₹) *</label>
                <input type="number" step="0.01" min="0.01" name="amount" required value="{{ $pending > 0 ? $pending : '' }}"
                    class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-black text-[#111111] font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Payment Method *</label>
                    <select name="payment_method" required class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-bold text-[#111111] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black min-h-[38px]">
                        <option value="cash" selected>Cash</option>
                        <option value="upi">UPI / QR Code</option>
                        <option value="bank">Bank / Card</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Payment Date *</label>
                    <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black min-h-[38px]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#344054] mb-1">Reference / Transaction ID (Optional)</label>
                <input type="text" name="reference_id" placeholder="UPI Ref, Cheque, or Receipt #"
                    class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#344054] mb-1">Remarks / Note (Optional)</label>
                <input type="text" name="notes" placeholder="Khata settlement notes..."
                    class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#E6E6E6]">
                <button type="button" onclick="document.getElementById('recordKhataModal').classList.add('hidden')" class="btn-secondary text-xs min-h-[42px]">
                    Cancel
                </button>
                <button type="submit" class="btn-lime text-xs min-h-[42px] font-bold">
                    Confirm & Record Payment
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2: Edit Customer Profile -->
<div id="editCustomerModal" class="fixed inset-0 z-50 bg-black/60 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full border border-[#E6E6E6] shadow-xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="p-5 border-b border-[#E6E6E6] flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#111111]"></span>
                <h3 class="text-sm font-bold text-[#111111] uppercase tracking-wider">Edit Customer Profile</h3>
            </div>
            <button type="button" onclick="document.getElementById('editCustomerModal').classList.add('hidden')" class="text-[#929292] hover:text-[#111111] text-lg font-bold">
                &times;
            </button>
        </div>

        <form method="POST" action="{{ route('customers.update', $customer) }}" class="p-5 space-y-3.5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-[#344054] mb-1">Customer Full Name *</label>
                    <input type="text" name="name" required value="{{ old('name', $customer->name) }}"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Mobile Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $customer->email) }}"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">GSTIN (Optional)</label>
                    <input type="text" name="gstin" value="{{ old('gstin', $customer->gstin) }}"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono uppercase focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">State / Province</label>
                    <input type="text" name="state" value="{{ old('state', $customer->state) }}" placeholder="e.g. Jammu & Kashmir"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-[#344054] mb-1">Billing Address</label>
                    <textarea name="address" rows="2" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">{{ old('address', $customer->address) }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-[#344054] mb-1">Khata Notes</label>
                    <input type="text" name="notes" value="{{ old('notes', $customer->notes) }}" placeholder="Credit limit, VIP status, etc."
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#E6E6E6]">
                <button type="button" onclick="document.getElementById('editCustomerModal').classList.add('hidden')" class="btn-secondary text-xs min-h-[42px]">
                    Cancel
                </button>
                <button type="submit" class="btn-lime text-xs min-h-[42px] font-bold">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function switchCustomerTab(tabKey) {
        document.querySelectorAll('.customer-tab-panel').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.customer-tab-btn').forEach(b => {
            b.classList.remove('bg-[#111111]', 'text-[#B7FF00]', 'font-bold');
            b.classList.add('text-[#666666]', 'font-semibold');
        });

        const activePanel = document.getElementById('tabPanel-' + tabKey);
        const activeBtn = document.getElementById('tabBtn-' + tabKey);
        if (activePanel) activePanel.classList.remove('hidden');
        if (activeBtn) {
            activeBtn.classList.remove('text-[#666666]', 'font-semibold');
            activeBtn.classList.add('bg-[#111111]', 'text-[#B7FF00]', 'font-bold');
        }
    }

    function filterInvoices(status) {
        document.querySelectorAll('.inv-flt-btn').forEach(btn => {
            btn.classList.remove('bg-[#111111]', 'text-[#B7FF00]', 'font-bold');
            btn.classList.add('text-[#666666]', 'font-semibold');
        });

        const activeBtn = document.getElementById('flt-' + status);
        if (activeBtn) {
            activeBtn.classList.remove('text-[#666666]', 'font-semibold');
            activeBtn.classList.add('bg-[#111111]', 'text-[#B7FF00]', 'font-bold');
        }

        const rows = document.querySelectorAll('.invoice-row');
        rows.forEach(r => {
            if (status === 'all') {
                r.style.display = '';
            } else if (r.getAttribute('data-status') === status) {
                r.style.display = '';
            } else {
                r.style.display = 'none';
            }
        });
    }
</script>
@endsection
