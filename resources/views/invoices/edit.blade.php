@extends('layouts.app')

@section('title', 'Edit Invoice • ' . $invoice->invoice_number)
@section('page-title', 'Edit Invoice')
@section('page-subtitle', 'Update items, customer information, or finalize draft invoice #' . $invoice->invoice_number)

@section('content')
@php
    $tenant = $tenant ?? \App\Services\TenantContext::get();
    $user = auth()->user();

    $oldItems = old('items');
    if (empty($oldItems)) {
        $oldItems = [];
        foreach ($invoice->items as $it) {
            $oldItems[] = [
                'item_id' => $it->item_id,
                'item_name' => $it->item_name,
                'storage' => $it->storage ?? $it->ram_rom ?? '',
                'color' => $it->color ?? '',
                'specs' => $it->specs ?? '',
                'imei_1' => $it->imei_1 ?? '',
                'imei_2' => $it->imei_2 ?? '',
                'serial_number' => $it->serial_number ?? '',
                'selling_price' => $it->rate,
                'warranty_status' => $it->warranty_status ?? 'no_warranty',
                'warranty_expiry_date' => $it->warranty_expiry_date ? $it->warranty_expiry_date->format('Y-m-d') : '',
                'notes' => $it->notes ?? '',
            ];
        }
    }
    if (empty($oldItems)) {
        $oldItems = [
            [
                'item_id' => '',
                'item_name' => '',
                'storage' => '',
                'color' => '',
                'specs' => '',
                'imei_1' => '',
                'imei_2' => '',
                'serial_number' => '',
                'selling_price' => '',
                'warranty_status' => 'no_warranty',
                'warranty_expiry_date' => '',
                'notes' => '',
            ]
        ];
    }
@endphp
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 bg-[#F2F4F7] text-[#344054] rounded-md text-xs font-semibold tracking-wide border border-[#EAECF0] mb-1">
                <span class="w-2 h-2 rounded-full {{ $invoice->isDraft() ? 'bg-[#F79009]' : 'bg-[#079455]' }}"></span>
                <span class="font-mono">Invoice #{{ $invoice->invoice_number }}</span>
                <span class="uppercase text-[10px] px-1.5 py-0.2 bg-white rounded border border-[#D0D5DD] font-bold">
                    {{ $invoice->status }}
                </span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-[#111111] tracking-tight">Edit Invoice</h2>
            <p class="text-xs sm:text-sm text-[#666666]">Reconcile stock, modify items, and adjust billing details</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('invoices.show', $invoice) }}" class="btn-secondary px-3.5 py-2 rounded-lg text-xs sm:text-sm font-semibold flex items-center gap-2 min-h-[44px]">
                <svg class="w-4 h-4 text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                <span>View Invoice</span>
            </a>
            <!-- Central Camera Scanner Button -->
            <button type="button" onclick="startScanner('central')" class="btn-secondary px-3.5 py-2 rounded-lg text-xs sm:text-sm font-semibold flex items-center gap-2 min-h-[44px]">
                <svg class="w-4 h-4 text-[#111111]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                <span>Scan Barcode / Box</span>
            </button>
        </div>
    </div>

    <!-- Camera Barcode Scanner Modal -->
    <div id="scannerModal" class="modal-backdrop hidden" onclick="if(event.target === this) stopScanner()">
        <div class="bg-white rounded-2xl p-5 w-full max-w-md shadow-2xl space-y-4 border border-[#E6E6E6] my-auto max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#DC2626] animate-ping"></span>
                    <h3 class="text-sm font-bold text-[#111111]" id="scannerModalTitle">Scan Device Barcode / IMEI</h3>
                </div>
                <button type="button" onclick="stopScanner()" class="p-1 text-[#929292] hover:text-[#111111] rounded-lg min-h-[36px] min-w-[36px] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Viewfinder -->
            <div id="reader" class="rounded-lg overflow-hidden bg-[#050505] min-h-[250px]"></div>

            <p class="text-[11px] text-center text-[#666666]" id="scannerModalInstructions">Align camera with IMEI 1, IMEI 2, or box serial barcode.</p>

            <button type="button" onclick="stopScanner()" class="w-full py-2.5 bg-[#F2F4F7] hover:bg-[#EAECF0] text-[#111111] rounded-lg text-xs font-semibold min-h-[44px] transition">
                Close Scanner
            </button>
        </div>
    </div>

    <form method="POST" action="{{ route('invoices.update', $invoice) }}" id="invoiceForm" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- 1. Customer Details Section -->
        <div class="card-surface p-5 sm:p-6 space-y-4 shadow-2xs">
            <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span>Customer & Payment Information</span>
                </h3>
            </div>

            @if($customers->isNotEmpty())
                <div class="p-3 bg-[#F7F7F6] rounded-lg border border-[#E6E6E6]">
                    <label class="block text-[11px] font-bold text-[#666666] uppercase mb-1">Select Existing Customer (Optional)</label>
                    <select id="customer_selector" onchange="autoFillCustomer(this)" class="w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-md text-xs sm:text-sm focus:outline-none focus:ring-1 focus:ring-black">
                        <option value="">-- Choose saved customer or edit below --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}"
                                data-name="{{ $c->name }}"
                                data-phone="{{ $c->phone }}"
                                data-email="{{ $c->email }}"
                                data-adhar="{{ $c->id_proof_number }}"
                                data-address="{{ $c->address }}"
                                data-state="{{ $c->state }}"
                                data-statecode="{{ $c->state_code }}"
                                {{ old('customer_id', $invoice->customer_id) == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->phone }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id', $invoice->customer_id) }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#111111] mb-1">Customer Name *</label>
                    <input type="text" name="customer_name" id="customer_name" required value="{{ old('customer_name', $invoice->customer?->name) }}"
                        placeholder="e.g. Muhammad Basit"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-medium focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'customer_phone'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Customer Phone {{ \App\Services\DynamicFormConfig::isFieldRequired($tenant, 'invoice', 'customer_phone') ? '*' : '' }}</label>
                        <input type="text" name="customer_phone" id="customer_phone" {{ \App\Services\DynamicFormConfig::isFieldRequired($tenant, 'invoice', 'customer_phone') ? 'required' : '' }} value="{{ old('customer_phone', $invoice->customer?->phone) }}"
                            placeholder="e.g. 7006079473"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-medium focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'customer_email'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Customer Email {{ \App\Services\DynamicFormConfig::isFieldRequired($tenant, 'invoice', 'customer_email') ? '*' : '' }}</label>
                        <input type="email" name="customer_email" id="customer_email" {{ \App\Services\DynamicFormConfig::isFieldRequired($tenant, 'invoice', 'customer_email') ? 'required' : '' }} value="{{ old('customer_email', $invoice->customer?->email) }}"
                            placeholder="client@gmail.com"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'customer_adhar'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Aadhaar / ID Number</label>
                        <input type="text" name="customer_adhar" id="customer_adhar" value="{{ old('customer_adhar', $invoice->customer?->id_proof_number ?? $invoice->customer?->adhar_number) }}"
                            placeholder="4-4-4 Aadhaar Digits"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'customer_address'))
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Customer Address {{ \App\Services\DynamicFormConfig::isFieldRequired($tenant, 'invoice', 'customer_address') ? '*' : '' }}</label>
                        <textarea name="customer_address" id="customer_address" rows="1" placeholder="Address..." {{ \App\Services\DynamicFormConfig::isFieldRequired($tenant, 'invoice', 'customer_address') ? 'required' : '' }}
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">{{ old('customer_address', $invoice->customer?->address) }}</textarea>
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'customer_state'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Customer State</label>
                        <input type="text" name="customer_state" id="customer_state" value="{{ old('customer_state', $invoice->customer?->state ?? $tenant->state) }}"
                            placeholder="e.g. Jammu & Kashmir"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">State Code (2 Digits)</label>
                        <input type="text" name="customer_state_code" id="customer_state_code" value="{{ old('customer_state_code', $invoice->customer?->state_code ?? $tenant->state_code) }}" oninput="calculateTotals()"
                            placeholder="01"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @else
                    <input type="hidden" name="customer_state" id="customer_state" value="{{ $invoice->customer?->state ?? $tenant->state }}">
                    <input type="hidden" name="customer_state_code" id="customer_state_code" value="{{ $invoice->customer?->state_code ?? $tenant->state_code }}">
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'payment_method'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Payment Method *</label>
                        <select name="payment_method" id="payment_method" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-semibold text-[#111111] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                            <option value="upi" {{ old('payment_method', $invoice->payment_method) === 'upi' ? 'selected' : '' }}>UPI / GPay / PhonePe</option>
                            <option value="cash" {{ old('payment_method', $invoice->payment_method) === 'cash' ? 'selected' : '' }}>Cash Counter</option>
                            <option value="bank" {{ old('payment_method', $invoice->payment_method) === 'bank' ? 'selected' : '' }}>Bank Transfer / IMPS</option>
                            <option value="mixed" {{ old('payment_method', $invoice->payment_method) === 'mixed' ? 'selected' : '' }}>Split / Mixed Payment</option>
                        </select>
                    </div>
                @else
                    <input type="hidden" name="payment_method" id="payment_method" value="{{ $invoice->payment_method ?: 'cash' }}">
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'invoice_date'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Sale Date *</label>
                        <input type="date" name="invoice_date" value="{{ old('invoice_date', $invoice->invoice_date?->format('Y-m-d')) }}" required
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @else
                    <input type="hidden" name="invoice_date" value="{{ $invoice->invoice_date?->format('Y-m-d') }}">
                @endif
            </div>
        </div>

        <!-- 2. Line Items Container & Centralized Barcode Scanner -->
        <div class="card-surface p-5 sm:p-6 space-y-4 shadow-2xs">
            <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    <span>Line Items to Sell</span>
                </h3>
                <button type="button" onclick="addItemRow()" class="btn-secondary px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 min-h-[36px]">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Add Item</span>
                </button>
            </div>

            <!-- Centralized Barcode / IMEI / Serial Scanner Bar -->
            <div class="p-3.5 bg-[#050505] text-white rounded-xl border border-white/10 space-y-2.5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#B7FF00] animate-pulse"></span>
                        <span class="text-xs font-bold text-white uppercase tracking-wider">Centralized Device Scanner</span>
                    </div>
                    <span class="text-[10px] text-[#929292] font-mono">Supports Barcode Guns & Camera</span>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                    <div class="relative flex-1">
                        <input type="text" id="centralScannerInput" placeholder="Scan or type IMEI 1, IMEI 2, or Serial Number (Press Enter)..."
                            autocomplete="off"
                            class="w-full pl-9 pr-4 py-2.5 bg-white/10 border border-white/20 rounded-lg text-xs font-mono text-white placeholder-white/40 focus:outline-none focus:ring-1 focus:ring-[#B7FF00] min-h-[44px]">
                        <svg class="w-4 h-4 text-white/50 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    </div>
                    <button type="button" onclick="startScanner('central')" class="btn-lime px-4 py-2.5 rounded-lg text-xs font-bold flex items-center justify-center gap-2 min-h-[44px] shrink-0">
                        <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                        <span>Scan Camera</span>
                    </button>
                </div>

                <div id="centralScannerFeedback" class="hidden text-xs py-1.5 px-3 rounded-lg font-medium flex items-center justify-between">
                    <span id="centralScannerMsg"></span>
                    <button type="button" onclick="document.getElementById('centralScannerFeedback').classList.add('hidden')" class="text-white/60 hover:text-white">&times;</button>
                </div>
            </div>

            <!-- Dynamic Items Container -->
            <div id="itemsWrapper" class="space-y-4">
                @foreach($oldItems as $idx => $it)
                    <div class="item-row p-4 sm:p-5 rounded-xl border border-[#E6E6E6] bg-[#FAFAFA] space-y-4 relative transition hover:border-[#CCCCCC]" data-index="{{ $idx }}">
                        <!-- Row Header with Number and Remove Button -->
                        <div class="flex items-center justify-between border-b border-[#EBEBEB] pb-2">
                            <span class="text-xs font-bold text-[#111111] uppercase tracking-wider flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-[#111111] text-[#B7FF00] inline-flex items-center justify-center text-[10px] font-mono row-number">{{ $idx + 1 }}</span>
                                <span>Device / Item Details</span>
                            </span>
                            <button type="button" onclick="removeItemRow(this)" class="text-xs text-[#DC2626] hover:underline font-semibold flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                <span>Remove</span>
                            </button>
                        </div>

                        <!-- Device Inventory Selector Dropdown -->
                        <div class="p-3 bg-white rounded-lg border border-[#E6E6E6]">
                            <label class="block text-[11px] font-bold text-[#666666] uppercase mb-1">Pick Registered Device / Stock (Optional)</label>
                            <select name="items[{{ $idx }}][item_id]" class="device-select w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-md text-xs sm:text-sm focus:outline-none focus:ring-1 focus:ring-black" onchange="autoFillItemRow(this)">
                                <option value="">-- Manual walk-in entry or select registered stock --</option>
                                @foreach($inStockItems as $device)
                                    <option value="{{ $device->id }}"
                                        data-name="{{ $device->item_name }}"
                                        data-brand="{{ $device->brand }}"
                                        data-model="{{ $device->model }}"
                                        data-storage="{{ $device->storage ?? $device->ram_rom }}"
                                        data-color="{{ $device->color }}"
                                        data-imei1="{{ $device->imei_1 }}"
                                        data-imei2="{{ $device->imei_2 }}"
                                        data-serial="{{ $device->serial_number }}"
                                        data-price="{{ $device->selling_price }}"
                                        data-warranty="{{ $device->warranty_status ?? 'no_warranty' }}"
                                        data-warranty-date="{{ $device->warranty_expiry_date ? $device->warranty_expiry_date->format('Y-m-d') : '' }}"
                                        data-notes="{{ $device->notes }}"
                                        {{ (string)($it['item_id'] ?? '') === (string)$device->id ? 'selected' : '' }}>
                                        {{ $device->item_name }} &bull; {{ $device->brand }} {{ $device->model }} &bull; {{ $device->storage }} &bull; ₹{{ number_format($device->selling_price, 2) }} (Qty: {{ $device->quantity }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Manual Fields Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                            <div class="sm:col-span-2">
                                <label class="block font-semibold text-[#111111] mb-1">Item / Device Name *</label>
                                <input type="text" name="items[{{ $idx }}][item_name]" value="{{ $it['item_name'] ?? '' }}" required placeholder="e.g. Apple iPhone 15 Pro"
                                    class="row-name w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                            </div>

                            @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'storage'))
                                <div>
                                    <label class="block font-semibold text-[#111111] mb-1">Storage / RAM</label>
                                    <input type="text" name="items[{{ $idx }}][storage]" value="{{ $it['storage'] ?? '' }}" placeholder="128GB / 8GB RAM"
                                        class="row-storage w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                                </div>
                            @endif

                            @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'color'))
                                <div>
                                    <label class="block font-semibold text-[#111111] mb-1">Color / Finish</label>
                                    <input type="text" name="items[{{ $idx }}][color]" value="{{ $it['color'] ?? '' }}" placeholder="Natural Titanium"
                                        class="row-color w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                                </div>
                            @endif

                            <!-- IMEI 1 with Direct Barcode Scanner -->
                            @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'imei_1'))
                                <div>
                                    <label class="block font-semibold text-[#111111] mb-1">IMEI 1 (15 digits)</label>
                                    <div class="flex items-center gap-1.5">
                                        <input type="text" name="items[{{ $idx }}][imei_1]" value="{{ $it['imei_1'] ?? '' }}" placeholder="35xxxxxxxxxxxxx"
                                            class="row-imei1 w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg font-mono focus:outline-none focus:ring-1 focus:ring-black">
                                        <button type="button" onclick="startScanner('field', this, 'imei_1')" title="Scan IMEI 1"
                                            class="p-2 bg-[#F2F4F7] hover:bg-[#EAECF0] rounded-lg text-[#111111] shrink-0 min-h-[38px] min-w-[38px] flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <!-- IMEI 2 with Direct Barcode Scanner -->
                            @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'imei_2'))
                                <div>
                                    <label class="block font-semibold text-[#111111] mb-1">IMEI 2 (Optional)</label>
                                    <div class="flex items-center gap-1.5">
                                        <input type="text" name="items[{{ $idx }}][imei_2]" value="{{ $it['imei_2'] ?? '' }}" placeholder="35xxxxxxxxxxxxx"
                                            class="row-imei2 w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg font-mono focus:outline-none focus:ring-1 focus:ring-black">
                                        <button type="button" onclick="startScanner('field', this, 'imei_2')" title="Scan IMEI 2"
                                            class="p-2 bg-[#F2F4F7] hover:bg-[#EAECF0] rounded-lg text-[#111111] shrink-0 min-h-[38px] min-w-[38px] flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <!-- Serial Number with Direct Barcode Scanner -->
                            @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'serial_number'))
                                <div>
                                    <label class="block font-semibold text-[#111111] mb-1">Serial Number</label>
                                    <div class="flex items-center gap-1.5">
                                        <input type="text" name="items[{{ $idx }}][serial_number]" value="{{ $it['serial_number'] ?? '' }}" placeholder="F2Lxxxxxxxxx"
                                            class="row-serial w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg font-mono focus:outline-none focus:ring-1 focus:ring-black">
                                        <button type="button" onclick="startScanner('field', this, 'serial_number')" title="Scan Serial Number"
                                            class="p-2 bg-[#F2F4F7] hover:bg-[#EAECF0] rounded-lg text-[#111111] shrink-0 min-h-[38px] min-w-[38px] flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <div>
                                <label class="block font-semibold text-[#111111] mb-1">Selling Rate (₹) *</label>
                                <input type="number" step="0.01" min="0" name="items[{{ $idx }}][selling_price]" value="{{ $it['selling_price'] ?? '' }}" required
                                    placeholder="0.00" oninput="calculateTotals()"
                                    class="row-price w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg font-mono font-bold focus:outline-none focus:ring-1 focus:ring-black">
                            </div>

                            @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'warranty_status'))
                                <div>
                                    <label class="block font-semibold text-[#111111] mb-1">Warranty Term</label>
                                    <select name="items[{{ $idx }}][warranty_status]" class="row-warranty w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                                        <option value="no_warranty" {{ ($it['warranty_status'] ?? '') === 'no_warranty' ? 'selected' : '' }}>No Warranty</option>
                                        <option value="brand_warranty" {{ ($it['warranty_status'] ?? '') === 'brand_warranty' ? 'selected' : '' }}>Brand Warranty</option>
                                        <option value="store_warranty" {{ ($it['warranty_status'] ?? '') === 'store_warranty' ? 'selected' : '' }}>Store Warranty</option>
                                    </select>
                                </div>
                            @endif

                            @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'warranty_expiry_date'))
                                <div>
                                    <label class="block font-semibold text-[#111111] mb-1">Warranty Expiry</label>
                                    <input type="date" name="items[{{ $idx }}][warranty_expiry_date]" value="{{ $it['warranty_expiry_date'] ?? '' }}"
                                        class="row-warranty-date w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                                </div>
                            @endif

                            @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'notes'))
                                <div class="sm:col-span-2">
                                    <label class="block font-semibold text-[#111111] mb-1">Item Notes / Remarks</label>
                                    <input type="text" name="items[{{ $idx }}][notes]" value="{{ $it['notes'] ?? '' }}" placeholder="Condition, included charger, adapter, etc."
                                        class="row-notes w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 3. Billing & Tax Configuration -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Tax & Discount Settings (2 cols) -->
            <div class="lg:col-span-2 card-surface p-5 sm:p-6 space-y-4 shadow-2xs">
                <div class="border-b border-[#E6E6E6] pb-3">
                    <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path></svg>
                        <span>GST & Commercial Terms</span>
                    </h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- GST Toggle -->
                    @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'is_gst'))
                        <div class="p-3.5 rounded-xl border border-[#E6E6E6] bg-[#F7F7F6] flex items-center justify-between">
                            <div>
                                <label for="is_gst" class="block text-xs font-bold text-[#111111]">Generate GST Invoice</label>
                                <span class="text-[11px] text-[#666666]">Enable to compute CGST / SGST / IGST</span>
                            </div>
                            <input type="checkbox" name="is_gst" id="is_gst" value="1" {{ old('is_gst', $invoice->is_gst) ? 'checked' : '' }} onchange="toggleGstFields()"
                                class="w-5 h-5 accent-black rounded cursor-pointer">
                        </div>
                    @else
                        <input type="hidden" name="is_gst" id="is_gst" value="{{ $invoice->is_gst ? 1 : 0 }}">
                    @endif

                    <!-- Tax Rate -->
                    <div id="gstRateContainer" class="{{ old('is_gst', $invoice->is_gst) ? '' : 'hidden' }}">
                        <label class="block text-xs font-semibold text-[#111111] mb-1">GST Tax Rate (%)</label>
                        <select name="tax_rate" id="tax_rate" onchange="calculateTotals()" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-semibold focus:outline-none focus:ring-1 focus:ring-black">
                            <option value="18" {{ (string)old('tax_rate', $invoice->tax_rate) === '18' ? 'selected' : '' }}>18% Standard (Smartphones, Electronics)</option>
                            <option value="12" {{ (string)old('tax_rate', $invoice->tax_rate) === '12' ? 'selected' : '' }}>12% IT Accessories</option>
                            <option value="28" {{ (string)old('tax_rate', $invoice->tax_rate) === '28' ? 'selected' : '' }}>28% Luxury Items</option>
                            <option value="5" {{ (string)old('tax_rate', $invoice->tax_rate) === '5' ? 'selected' : '' }}>5% Goods</option>
                            <option value="0" {{ (string)old('tax_rate', $invoice->tax_rate) === '0' ? 'selected' : '' }}>0% Exempt</option>
                        </select>
                    </div>

                    <!-- Tax Inclusive Toggle -->
                    <div id="taxInclusiveContainer" class="{{ old('is_gst', $invoice->is_gst) ? '' : 'hidden' }} sm:col-span-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-semibold text-[#111111]">
                            <input type="checkbox" name="is_tax_inclusive" id="is_tax_inclusive" value="1" {{ old('is_tax_inclusive', $invoice->is_tax_inclusive) ? 'checked' : '' }} onchange="calculateTotals()" class="w-4 h-4 accent-black rounded">
                            <span>Item selling rates are Tax-Inclusive (GST is already included in line prices)</span>
                        </label>
                    </div>

                    <!-- Flat Discount Field -->
                    @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'discount'))
                        <div>
                            <label class="block text-xs font-semibold text-[#111111] mb-1">Discount Amount (₹)</label>
                            <input type="number" step="0.01" min="0" name="discount" id="discount" value="{{ old('discount', $invoice->discount) }}" oninput="calculateTotals()"
                                placeholder="0.00"
                                class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                        </div>
                    @endif
                </div>

                <!-- Customer Payment at Checkout -->
                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'amount_paid'))
                    <div class="pt-4 border-t border-[#E6E6E6] space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-semibold text-[#111111]">
                                Amount Paid by Customer (₹)
                            </label>
                            <button type="button" onclick="setFullPaid()" class="text-xs font-bold text-[#079455] hover:underline">
                                Full Settlement (₹<span id="fullPayAmt">0.00</span>)
                            </button>
                        </div>
                        <input type="number" step="0.01" min="0" name="amount_paid" id="amount_paid" value="{{ old('amount_paid', $invoice->amount_paid) }}" oninput="calculateTotals()"
                            placeholder="0.00 (leave blank if nothing paid)"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-sm font-bold font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                        <p class="text-[11px] text-[#666666]">Leave blank or enter 0 if nothing paid (records credit due / Khata). Click 'Full Settlement' above for full payment.</p>
                    </div>
                @endif

                <!-- Invoice Notes / Terms -->
                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'notes'))
                    <div class="pt-2">
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Invoice Notes / Special Terms</label>
                        <input type="text" name="notes" value="{{ old('notes', $invoice->notes) }}"
                            placeholder="e.g. 7-day checking warranty on hardware. Accessories carry brand warranty."
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif
            </div>

            <!-- Sticky Summary & Checkout Card (1 col) -->
            <div class="space-y-4">
                <div class="card-dark p-6 rounded-2xl shadow-xl space-y-4">
                    <span class="text-xs font-bold text-[#929292] uppercase tracking-wider block">Billing Breakdown</span>

                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between text-[#E7E7E7]">
                            <span>Items Subtotal:</span>
                            <span class="font-mono font-bold" id="summarySubtotal">₹0.00</span>
                        </div>
                        <div class="flex justify-between text-[#E7E7E7]">
                            <span>Discount:</span>
                            <span class="font-mono text-[#F04438]" id="summaryDiscount">- ₹0.00</span>
                        </div>
                        <div class="flex justify-between text-[#929292] pt-1" id="gstTaxableRow" style="display: none;">
                            <span>Taxable Base:</span>
                            <span class="font-mono" id="summaryTaxableBase">₹0.00</span>
                        </div>
                        <div class="flex justify-between text-[#E7E7E7]" id="gstBreakdownRow">
                            <span id="summaryTaxLabel">GST:</span>
                            <span class="font-mono" id="summaryTax">₹0.00</span>
                        </div>
                        <div class="text-[10.5px] text-[#929292] pl-2 border-l border-white/20 space-y-0.5" id="gstSplitBreakdown" style="display: none;">
                        </div>
                        <div class="pt-3 border-t border-white/10 flex justify-between items-baseline">
                            <span class="text-sm font-bold text-white">Grand Total:</span>
                            <span class="text-2xl font-black text-[#B7FF00] font-mono tracking-tight" id="summaryGrandTotal">₹0.00</span>
                        </div>
                        <div class="flex justify-between text-xs pt-1 text-[#D97706]" id="dueRow" style="display: none;">
                            <span>Balance Due:</span>
                            <span class="font-mono font-bold" id="summaryDue">₹0.00</span>
                        </div>
                    </div>

                    @if($tenant->upi_id)
                        <div class="mt-4 p-3 rounded-lg bg-white/5 border border-white/10 text-center">
                            <p class="text-[10px] font-bold text-[#929292] uppercase tracking-wider mb-2">Scan & Pay at Counter</p>
                            <img id="upiQrImg" src="" alt="UPI QR" class="w-24 h-24 mx-auto rounded-lg bg-white p-1 shadow-2xs">
                            <p class="text-[10px] font-mono text-[#929292] mt-1">{{ $tenant->upi_id }}</p>
                        </div>
                    @endif
                </div>

                <!-- Primary Submit Actions: Draft vs Finalize -->
                <div class="pt-1 grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <button type="submit" name="status" value="draft" id="saveDraftBtn" class="w-full py-3 btn-secondary rounded-lg font-semibold text-xs min-h-[44px] flex items-center justify-center gap-1.5 transition">
                        <svg class="w-4 h-4 text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        <span>Save as Draft</span>
                    </button>
                    <button type="submit" name="status" value="finalized" id="submitInvoiceBtn" class="w-full py-3 btn-lime rounded-lg font-bold text-xs shadow-xs min-h-[44px] flex items-center justify-center gap-1.5 transition">
                        <span>{{ $invoice->isDraft() ? 'Finalize & Issue Bill' : 'Update Invoice' }}</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const registeredItems = @json($inStockItems);
    const storeStateCode = "{{ $tenant->state_code ?? '01' }}";
    const storeUpiId = "{{ $tenant->upi_id ?? '' }}";
    const storeName = "{{ addslashes($tenant->name) }}";

    function autoFillCustomer(selectEl) {
        const opt = selectEl.options[selectEl.selectedIndex];
        if (!opt || !opt.value) return;

        document.getElementById('customer_id').value = opt.value;
        document.getElementById('customer_name').value = opt.dataset.name || '';
        if (document.getElementById('customer_phone')) document.getElementById('customer_phone').value = opt.dataset.phone || '';
        if (document.getElementById('customer_email')) document.getElementById('customer_email').value = opt.dataset.email || '';
        if (document.getElementById('customer_adhar')) document.getElementById('customer_adhar').value = opt.dataset.adhar || '';
        if (document.getElementById('customer_address')) document.getElementById('customer_address').value = opt.dataset.address || '';
        if (document.getElementById('customer_state')) document.getElementById('customer_state').value = opt.dataset.state || '';
        if (document.getElementById('customer_state_code')) document.getElementById('customer_state_code').value = opt.dataset.statecode || '';

        calculateTotals();
    }

    function toggleGstFields() {
        const isGst = document.getElementById('is_gst')?.checked;
        const rateContainer = document.getElementById('gstRateContainer');
        const inclusiveContainer = document.getElementById('taxInclusiveContainer');

        if (rateContainer) {
            if (isGst) {
                rateContainer.classList.remove('hidden');
            } else {
                rateContainer.classList.add('hidden');
            }
        }
        if (inclusiveContainer) {
            if (isGst) {
                inclusiveContainer.classList.remove('hidden');
            } else {
                inclusiveContainer.classList.add('hidden');
            }
        }

        calculateTotals();
    }

    function autoFillItemRow(selectEl) {
        const row = selectEl.closest('.item-row');
        const opt = selectEl.options[selectEl.selectedIndex];
        if (!opt || !opt.value) return;

        const nameInput = row.querySelector('.row-name');
        const storageInput = row.querySelector('.row-storage');
        const colorInput = row.querySelector('.row-color');
        const imei1Input = row.querySelector('.row-imei1');
        const imei2Input = row.querySelector('.row-imei2');
        const serialInput = row.querySelector('.row-serial');
        const priceInput = row.querySelector('.row-price');
        const warrantySelect = row.querySelector('.row-warranty');
        const warrantyDateInput = row.querySelector('.row-warranty-date');
        const notesInput = row.querySelector('.row-notes');

        if (nameInput) nameInput.value = opt.dataset.name || '';
        if (storageInput) storageInput.value = opt.dataset.storage || '';
        if (colorInput) colorInput.value = opt.dataset.color || '';
        if (imei1Input) imei1Input.value = opt.dataset.imei1 || '';
        if (imei2Input) imei2Input.value = opt.dataset.imei2 || '';
        if (serialInput) serialInput.value = opt.dataset.serial || '';
        if (priceInput) priceInput.value = opt.dataset.price || '';
        if (warrantySelect) warrantySelect.value = opt.dataset.warranty || 'no_warranty';
        if (warrantyDateInput) warrantyDateInput.value = opt.dataset.warrantyDate || '';
        if (notesInput) notesInput.value = opt.dataset.notes || '';

        calculateTotals();
    }

    function addItemRow(prefillData = null) {
        const wrapper = document.getElementById('itemsWrapper');
        const currentCount = wrapper.querySelectorAll('.item-row').length;
        const newIndex = currentCount;

        const rowDiv = document.createElement('div');
        rowDiv.className = 'item-row p-4 sm:p-5 rounded-xl border border-[#E6E6E6] bg-[#FAFAFA] space-y-4 relative transition hover:border-[#CCCCCC]';
        rowDiv.dataset.index = newIndex;

        let optionsHtml = '<option value="">-- Manual walk-in entry or select registered stock --</option>';
        registeredItems.forEach(item => {
            const isSel = prefillData && (String(prefillData.id) === String(item.id) || String(prefillData.item_id) === String(item.id));
            optionsHtml += `<option value="${item.id}"
                data-name="${item.item_name || ''}"
                data-brand="${item.brand || ''}"
                data-model="${item.model || ''}"
                data-storage="${item.storage || item.ram_rom || ''}"
                data-color="${item.color || ''}"
                data-imei1="${item.imei_1 || ''}"
                data-imei2="${item.imei_2 || ''}"
                data-serial="${item.serial_number || ''}"
                data-price="${item.selling_price || ''}"
                data-warranty="${item.warranty_status || 'no_warranty'}"
                data-warranty-date="${item.warranty_expiry_date || ''}"
                data-notes="${item.notes || ''}"
                ${isSel ? 'selected' : ''}>
                ${item.item_name} &bull; ${item.brand || ''} ${item.model || ''} &bull; ${item.storage || item.ram_rom || ''} &bull; ₹${parseFloat(item.selling_price || 0).toLocaleString('en-IN', {minimumFractionDigits: 2})} (Qty: ${item.quantity || 1})
            </option>`;
        });

        rowDiv.innerHTML = `
            <div class="flex items-center justify-between border-b border-[#EBEBEB] pb-2">
                <span class="text-xs font-bold text-[#111111] uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded-full bg-[#111111] text-[#B7FF00] inline-flex items-center justify-center text-[10px] font-mono row-number">${newIndex + 1}</span>
                    <span>Device / Item Details</span>
                </span>
                <button type="button" onclick="removeItemRow(this)" class="text-xs text-[#DC2626] hover:underline font-semibold flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    <span>Remove</span>
                </button>
            </div>

            <div class="p-3 bg-white rounded-lg border border-[#E6E6E6]">
                <label class="block text-[11px] font-bold text-[#666666] uppercase mb-1">Pick Registered Device / Stock (Optional)</label>
                <select name="items[${newIndex}][item_id]" class="device-select w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-md text-xs sm:text-sm focus:outline-none focus:ring-1 focus:ring-black" onchange="autoFillItemRow(this)">
                    ${optionsHtml}
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                <div class="sm:col-span-2">
                    <label class="block font-semibold text-[#111111] mb-1">Item / Device Name *</label>
                    <input type="text" name="items[${newIndex}][item_name]" value="${prefillData?.item_name || ''}" required placeholder="e.g. Apple iPhone 15 Pro"
                        class="row-name w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'storage'))
                    <div>
                        <label class="block font-semibold text-[#111111] mb-1">Storage / RAM</label>
                        <input type="text" name="items[${newIndex}][storage]" value="${prefillData?.storage || ''}" placeholder="128GB / 8GB RAM"
                            class="row-storage w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'color'))
                    <div>
                        <label class="block font-semibold text-[#111111] mb-1">Color / Finish</label>
                        <input type="text" name="items[${newIndex}][color]" value="${prefillData?.color || ''}" placeholder="Natural Titanium"
                            class="row-color w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'imei_1'))
                    <div>
                        <label class="block font-semibold text-[#111111] mb-1">IMEI 1 (15 digits)</label>
                        <div class="flex items-center gap-1.5">
                            <input type="text" name="items[${newIndex}][imei_1]" value="${prefillData?.imei_1 || ''}" placeholder="35xxxxxxxxxxxxx"
                                class="row-imei1 w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg font-mono focus:outline-none focus:ring-1 focus:ring-black">
                            <button type="button" onclick="startScanner('field', this, 'imei_1')" title="Scan IMEI 1"
                                class="p-2 bg-[#F2F4F7] hover:bg-[#EAECF0] rounded-lg text-[#111111] shrink-0 min-h-[38px] min-w-[38px] flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            </button>
                        </div>
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'imei_2'))
                    <div>
                        <label class="block font-semibold text-[#111111] mb-1">IMEI 2 (Optional)</label>
                        <div class="flex items-center gap-1.5">
                            <input type="text" name="items[${newIndex}][imei_2]" value="${prefillData?.imei_2 || ''}" placeholder="35xxxxxxxxxxxxx"
                                class="row-imei2 w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg font-mono focus:outline-none focus:ring-1 focus:ring-black">
                            <button type="button" onclick="startScanner('field', this, 'imei_2')" title="Scan IMEI 2"
                                class="p-2 bg-[#F2F4F7] hover:bg-[#EAECF0] rounded-lg text-[#111111] shrink-0 min-h-[38px] min-w-[38px] flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            </button>
                        </div>
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'serial_number'))
                    <div>
                        <label class="block font-semibold text-[#111111] mb-1">Serial Number</label>
                        <div class="flex items-center gap-1.5">
                            <input type="text" name="items[${newIndex}][serial_number]" value="${prefillData?.serial_number || ''}" placeholder="F2Lxxxxxxxxx"
                                class="row-serial w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg font-mono focus:outline-none focus:ring-1 focus:ring-black">
                            <button type="button" onclick="startScanner('field', this, 'serial_number')" title="Scan Serial Number"
                                class="p-2 bg-[#F2F4F7] hover:bg-[#EAECF0] rounded-lg text-[#111111] shrink-0 min-h-[38px] min-w-[38px] flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            </button>
                        </div>
                    </div>
                @endif

                <div>
                    <label class="block font-semibold text-[#111111] mb-1">Selling Rate (₹) *</label>
                    <input type="number" step="0.01" min="0" name="items[${newIndex}][selling_price]" value="${prefillData?.selling_price || ''}" required
                        placeholder="0.00" oninput="calculateTotals()"
                        class="row-price w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg font-mono font-bold focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'warranty_status'))
                    <div>
                        <label class="block font-semibold text-[#111111] mb-1">Warranty Term</label>
                        <select name="items[${newIndex}][warranty_status]" class="row-warranty w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                            <option value="no_warranty" ${prefillData?.warranty_status === 'no_warranty' ? 'selected' : ''}>No Warranty</option>
                            <option value="brand_warranty" ${prefillData?.warranty_status === 'brand_warranty' ? 'selected' : ''}>Brand Warranty</option>
                            <option value="store_warranty" ${prefillData?.warranty_status === 'store_warranty' ? 'selected' : ''}>Store Warranty</option>
                        </select>
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'warranty_expiry_date'))
                    <div>
                        <label class="block font-semibold text-[#111111] mb-1">Warranty Expiry</label>
                        <input type="date" name="items[${newIndex}][warranty_expiry_date]" value="${prefillData?.warranty_expiry_date || ''}"
                            class="row-warranty-date w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'notes'))
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-[#111111] mb-1">Item Notes / Remarks</label>
                        <input type="text" name="items[${newIndex}][notes]" value="${prefillData?.notes || ''}" placeholder="Condition, included charger, adapter, etc."
                            class="row-notes w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif
            </div>
        `;

        wrapper.appendChild(rowDiv);
        renumberRows();
        calculateTotals();
    }

    function removeItemRow(btn) {
        const wrapper = document.getElementById('itemsWrapper');
        if (wrapper.querySelectorAll('.item-row').length <= 1) {
            alert('An invoice must have at least one line item.');
            return;
        }
        btn.closest('.item-row').remove();
        renumberRows();
        calculateTotals();
    }

    function renumberRows() {
        const rows = document.querySelectorAll('#itemsWrapper .item-row');
        rows.forEach((row, idx) => {
            row.dataset.index = idx;
            const badge = row.querySelector('.row-number');
            if (badge) badge.innerText = idx + 1;

            row.querySelectorAll('input, select, textarea').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/items\[\d+\]/, `items[${idx}]`));
                }
            });
        });
    }

    let currentGrandTotal = 0;

    function setFullPaid() {
        const paidInput = document.getElementById('amount_paid');
        if (paidInput) {
            paidInput.value = currentGrandTotal > 0 ? currentGrandTotal.toFixed(2) : '';
            calculateTotals();
        }
    }

    function calculateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.row-price').forEach(input => {
            const val = parseFloat(input.value) || 0;
            subtotal += val;
        });

        const isGst = document.getElementById('is_gst')?.checked ?? false;
        const taxRate = isGst ? (parseFloat(document.getElementById('tax_rate')?.value) || 0) : 0;
        const isInclusive = document.getElementById('is_tax_inclusive')?.checked ?? false;
        const discountInput = document.getElementById('discount');
        const discount = discountInput ? (parseFloat(discountInput.value) || 0) : 0;

        let taxableAmount = 0;
        let taxAmount = 0;
        let grandTotal = 0;

        if (isGst && taxRate > 0) {
            if (isInclusive) {
                const discountedSubtotal = Math.max(0, subtotal - discount);
                taxableAmount = discountedSubtotal / (1 + (taxRate / 100));
                taxAmount = discountedSubtotal - taxableAmount;
                grandTotal = discountedSubtotal;
            } else {
                taxableAmount = Math.max(0, subtotal - discount);
                taxAmount = taxableAmount * (taxRate / 100);
                grandTotal = taxableAmount + taxAmount;
            }
        } else {
            taxableAmount = Math.max(0, subtotal - discount);
            taxAmount = 0;
            grandTotal = taxableAmount;
        }

        currentGrandTotal = grandTotal;

        const custCode = document.getElementById('customer_state_code')?.value?.trim();
        const isInterstate = isGst && custCode && storeStateCode && (custCode !== storeStateCode);

        document.getElementById('summarySubtotal').innerText = '₹' + subtotal.toLocaleString('en-IN', {minimumFractionDigits: 2});
        document.getElementById('summaryDiscount').innerText = '- ₹' + discount.toLocaleString('en-IN', {minimumFractionDigits: 2});

        const taxableRow = document.getElementById('gstTaxableRow');
        const breakdownRow = document.getElementById('gstBreakdownRow');
        const splitRow = document.getElementById('gstSplitBreakdown');

        if (isGst && taxRate > 0) {
            taxableRow.style.display = 'flex';
            document.getElementById('summaryTaxableBase').innerText = '₹' + taxableAmount.toLocaleString('en-IN', {minimumFractionDigits: 2});

            breakdownRow.style.display = 'flex';
            document.getElementById('summaryTaxLabel').innerText = `GST (${taxRate}%):`;
            document.getElementById('summaryTax').innerText = '₹' + taxAmount.toLocaleString('en-IN', {minimumFractionDigits: 2});

            splitRow.style.display = 'block';
            if (isInterstate) {
                splitRow.innerHTML = `<div>IGST (${taxRate}%): ₹${taxAmount.toLocaleString('en-IN', {minimumFractionDigits: 2})}</div>`;
            } else {
                const halfTax = taxAmount / 2;
                const halfRate = taxRate / 2;
                splitRow.innerHTML = `
                    <div>CGST (${halfRate}%): ₹${halfTax.toLocaleString('en-IN', {minimumFractionDigits: 2})}</div>
                    <div>SGST (${halfRate}%): ₹${halfTax.toLocaleString('en-IN', {minimumFractionDigits: 2})}</div>
                `;
            }
        } else {
            taxableRow.style.display = 'none';
            breakdownRow.style.display = 'none';
            splitRow.style.display = 'none';
        }

        document.getElementById('summaryGrandTotal').innerText = '₹' + grandTotal.toLocaleString('en-IN', {minimumFractionDigits: 2});

        const fullPayEl = document.getElementById('fullPayAmt');
        if (fullPayEl) {
            fullPayEl.innerText = grandTotal.toLocaleString('en-IN', {minimumFractionDigits: 2});
        }

        const paidInput = document.getElementById('amount_paid');
        const paidVal = (paidInput && paidInput.value !== '') ? (parseFloat(paidInput.value) || 0) : 0;
        const due = Math.max(0, grandTotal - paidVal);

        const dueRow = document.getElementById('dueRow');
        if (due > 0) {
            dueRow.style.display = 'flex';
            document.getElementById('summaryDue').innerText = '₹' + due.toLocaleString('en-IN', {minimumFractionDigits: 2});
        } else {
            dueRow.style.display = 'none';
        }

        const qrImg = document.getElementById('upiQrImg');
        if (qrImg && storeUpiId) {
            const upiString = `upi://pay?pa=${storeUpiId}&pn=${encodeURIComponent(storeName)}&am=${grandTotal.toFixed(2)}&cu=INR`;
            qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(upiString)}`;
        }
    }

    // Centralized Barcode Scanner & Hardware Scanner Support
    const centralInput = document.getElementById('centralScannerInput');
    if (centralInput) {
        centralInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const code = this.value.trim();
                if (code) {
                    processScannedCode(code);
                    this.value = '';
                }
            }
        });
    }

    function showCentralFeedback(msg, isSuccess = true) {
        const box = document.getElementById('centralScannerFeedback');
        const label = document.getElementById('centralScannerMsg');
        if (!box || !label) return;

        label.innerText = msg;
        box.className = isSuccess
            ? 'text-xs py-1.5 px-3 rounded-lg font-medium flex items-center justify-between bg-[#079455]/20 text-[#B7FF00] border border-[#079455]/40'
            : 'text-xs py-1.5 px-3 rounded-lg font-medium flex items-center justify-between bg-[#DC2626]/20 text-[#FCA5A5] border border-[#DC2626]/40';
        box.classList.remove('hidden');

        setTimeout(() => {
            box.classList.add('hidden');
        }, 5000);
    }

    async function processScannedCode(code) {
        const query = code.trim();
        if (!query) return;

        // Check if barcode already added in the bill
        let alreadyAdded = false;
        document.querySelectorAll('.row-imei1, .row-imei2, .row-serial').forEach(inp => {
            if (inp.value && inp.value.trim().toLowerCase() === query.toLowerCase()) {
                alreadyAdded = true;
            }
        });

        if (alreadyAdded) {
            showCentralFeedback(`Identifier "${query}" is already added to this bill.`, false);
            return;
        }

        try {
            const resp = await fetch(`/items/search-api?q=${encodeURIComponent(query)}`);
            const data = await resp.json();

            if (data && data.length > 0) {
                const item = data[0];
                const matchedType = item.matched_field ? item.matched_field.toUpperCase().replace('_', ' ') : 'CODE';

                // Find first empty row or create new
                let targetRow = null;
                document.querySelectorAll('#itemsWrapper .item-row').forEach(row => {
                    const name = row.querySelector('.row-name')?.value?.trim();
                    const price = row.querySelector('.row-price')?.value?.trim();
                    if (!targetRow && !name && !price) {
                        targetRow = row;
                    }
                });

                if (targetRow) {
                    populateRowWithDevice(targetRow, item);
                } else {
                    addItemRow(item);
                }

                showCentralFeedback(`Matched device: ${item.item_name} via ${matchedType} (${query})`, true);
            } else {
                showCentralFeedback(`No device in inventory matched: "${query}". You can add manual entry.`, false);
            }
        } catch (err) {
            console.error('Barcode lookup error:', err);
            showCentralFeedback('Error searching for device. Check network connection.', false);
        }
    }

    function populateRowWithDevice(row, item) {
        const select = row.querySelector('.device-select');
        if (select) {
            select.value = item.id;
        }

        const nameInput = row.querySelector('.row-name');
        const storageInput = row.querySelector('.row-storage');
        const colorInput = row.querySelector('.row-color');
        const imei1Input = row.querySelector('.row-imei1');
        const imei2Input = row.querySelector('.row-imei2');
        const serialInput = row.querySelector('.row-serial');
        const priceInput = row.querySelector('.row-price');
        const warrantySelect = row.querySelector('.row-warranty');
        const warrantyDateInput = row.querySelector('.row-warranty-date');
        const notesInput = row.querySelector('.row-notes');

        if (nameInput) nameInput.value = item.item_name || '';
        if (storageInput) storageInput.value = item.storage || item.ram_rom || '';
        if (colorInput) colorInput.value = item.color || '';
        if (imei1Input) imei1Input.value = item.imei_1 || '';
        if (imei2Input) imei2Input.value = item.imei_2 || '';
        if (serialInput) serialInput.value = item.serial_number || '';
        if (priceInput) priceInput.value = item.selling_price || '';
        if (warrantySelect) warrantySelect.value = item.warranty_status || 'no_warranty';
        if (warrantyDateInput) warrantyDateInput.value = item.warranty_expiry_date || '';
        if (notesInput) notesInput.value = item.notes || '';

        calculateTotals();
    }

    // Camera Barcode Scanning with HTML5Qrcode
    let html5QrCode = null;
    let activeScanTarget = null;
    let activeFieldType = null;

    function startScanner(target, btnEl = null, fieldType = null) {
        activeScanTarget = target;
        activeFieldType = fieldType;

        const modal = document.getElementById('scannerModal');
        const title = document.getElementById('scannerModalTitle');
        const instructions = document.getElementById('scannerModalInstructions');

        if (target === 'central') {
            title.innerText = 'Central Device Scanner';
            instructions.innerText = 'Align camera with IMEI 1, IMEI 2, or Serial Number barcode.';
        } else {
            const fieldLabel = (fieldType || 'barcode').replace('_', ' ').toUpperCase();
            title.innerText = `Scan ${fieldLabel}`;
            instructions.innerText = `Align camera with ${fieldLabel} barcode on device or retail box.`;
        }

        modal.classList.remove('hidden');

        if (typeof Html5Qrcode === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://unpkg.com/html5-qrcode';
            script.onload = () => initHtml5Scanner();
            document.head.appendChild(script);
        } else {
            initHtml5Scanner();
        }
    }

    function initHtml5Scanner() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                startScanningInstance();
            }).catch(() => {
                startScanningInstance();
            });
        } else {
            startScanningInstance();
        }
    }

    function startScanningInstance() {
        html5QrCode = new Html5Qrcode("reader");
        const config = {
            fps: 15,
            qrbox: { width: 260, height: 160 },
            formatsToSupport: [
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.CODE_39,
                Html5QrcodeSupportedFormats.EAN_13,
                Html5QrcodeSupportedFormats.UPC_A,
                Html5QrcodeSupportedFormats.QR_CODE
            ]
        };

        html5QrCode.start(
            { facingMode: "environment" },
            config,
            (decodedText) => {
                handleBarcodeScanned(decodedText);
            },
            (error) => {
                // Ignore frame-by-frame parse misses
            }
        ).catch(err => {
            console.error('Camera init error:', err);
            alert('Unable to access camera: ' + err);
            stopScanner();
        });
    }

    function handleBarcodeScanned(code) {
        stopScanner();

        if (activeScanTarget === 'central') {
            processScannedCode(code);
        } else if (activeScanTarget === 'field') {
            // Find active row input
            let targetRow = null;
            if (activeFieldType) {
                const modal = document.getElementById('scannerModal');
                const lastFocused = document.activeElement;
            }
            alert(`Scanned: ${code}`);
        }
    }

    function stopScanner() {
        const modal = document.getElementById('scannerModal');
        modal.classList.add('hidden');

        if (html5QrCode) {
            html5QrCode.stop().catch(() => {}).finally(() => {
                html5QrCode = null;
            });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        calculateTotals();
    });
</script>
@endpush
@endsection

