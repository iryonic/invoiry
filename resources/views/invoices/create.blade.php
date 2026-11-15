@extends('layouts.app')

@section('title', 'New Invoice · Quick Bill')
@section('page-title', 'New Invoice')
@section('page-subtitle', 'Sell in-stock devices, scan box barcodes, or record walk-in counter sales')

@section('content')
@php
    $tenant = $tenant ?? \App\Services\TenantContext::get();
    $user = auth()->user();

    $oldItems = old('items');
    if (empty($oldItems)) {
        $oldItems = [
            [
                'item_id' => request('preselect_item'),
                'item_name' => '',
                'storage' => '',
                'color' => '',
                'specs' => '',
                'imei_1' => '',
                'imei_2' => '',
                'serial_number' => '',
                'quantity' => 1,
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
                <span class="w-2 h-2 rounded-full bg-[#079455]"></span>
                <span class="font-mono">Invoice #{{ $nextInvoiceNumber }}</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-[#111111] tracking-tight">Sell Items & Generate Invoice</h2>
            <p class="text-xs sm:text-sm text-[#666666]">Select available stock or scan IMEI barcode to populate bill instantly</p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Central Camera Scanner Button -->
            <button type="button" onclick="startScanner('central')" class="btn-secondary px-3.5 py-2 rounded-lg text-xs sm:text-sm font-semibold flex items-center gap-2 min-h-[44px]">
                <svg class="w-4 h-4 text-[#111111]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                <span>Scan Barcode / Box</span>
            </button>
        </div>
    </div>

    <!-- Draft Auto-Restore Notice Bar (Hidden by default, shown by JS if unsaved draft exists) -->
    <div id="draftAlertBar" class="hidden p-3.5 rounded-xl bg-[#EFF8FF] border border-[#B2DDFF] text-xs text-[#175CD3] flex items-center justify-between gap-3 shadow-2xs">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-[#175CD3] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>You have an unsaved bill draft with items previously added in this browser.</span>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="restoreLocalDraft()" class="px-2.5 py-1 bg-[#175CD3] text-white rounded font-bold hover:bg-[#154fc1]">Restore Draft</button>
            <button type="button" onclick="discardLocalDraft()" class="px-2.5 py-1 bg-white text-[#666666] border border-[#D0D5DD] rounded font-semibold hover:text-black">Discard</button>
        </div>
    </div>

    <!-- Camera Barcode Scanner Modal (Shared for Central & Targeted Field Scanning) -->
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

    <form method="POST" action="{{ route('invoices.store') }}" id="invoiceForm" class="space-y-6">
        @csrf

        <!-- 1. Customer Details Section -->
        <div class="card-surface p-5 sm:p-6 space-y-4 shadow-2xs">
            <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span>Customer & Payment Information</span>
                </h3>
            </div>

            <!-- Quick Customer Selector Dropdown if customers exist -->
            @if($customers->isNotEmpty())
                <div class="p-3 bg-[#F7F7F6] rounded-lg border border-[#E6E6E6]">
                    <label class="block text-[11px] font-bold text-[#666666] uppercase mb-1">Select Existing Customer (Optional)</label>
                    <select id="customer_selector" onchange="autoFillCustomer(this)" class="w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-md text-xs sm:text-sm focus:outline-none focus:ring-1 focus:ring-black">
                        <option value="">-- Choose saved customer or type manually below --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}"
                                data-name="{{ $c->name }}"
                                data-phone="{{ $c->phone }}"
                                data-email="{{ $c->email }}"
                                data-adhar="{{ $c->id_proof_number }}"
                                data-address="{{ $c->address }}"
                                data-state="{{ $c->state }}"
                                data-statecode="{{ $c->state_code }}">
                                {{ $c->name }} ({{ $c->phone }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id') }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#111111] mb-1">Customer Name *</label>
                    <input type="text" name="customer_name" id="customer_name" required value="{{ old('customer_name') }}"
                        placeholder="e.g. Muhammad Basit"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-medium focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'customer_phone'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Customer Phone {{ \App\Services\DynamicFormConfig::isFieldRequired($tenant, 'invoice', 'customer_phone') ? '*' : '' }}</label>
                        <input type="text" name="customer_phone" id="customer_phone" {{ \App\Services\DynamicFormConfig::isFieldRequired($tenant, 'invoice', 'customer_phone') ? 'required' : '' }} value="{{ old('customer_phone') }}"
                            placeholder="e.g. 7006079473"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-medium focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'customer_email'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Customer Email {{ \App\Services\DynamicFormConfig::isFieldRequired($tenant, 'invoice', 'customer_email') ? '*' : '' }}</label>
                        <input type="email" name="customer_email" id="customer_email" {{ \App\Services\DynamicFormConfig::isFieldRequired($tenant, 'invoice', 'customer_email') ? 'required' : '' }} value="{{ old('customer_email') }}"
                            placeholder="client@gmail.com"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'customer_adhar'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Aadhaar / ID Number</label>
                        <input type="text" name="customer_adhar" id="customer_adhar" value="{{ old('customer_adhar') }}"
                            placeholder="4-4-4 Aadhaar Digits"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'customer_address'))
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Customer Address {{ \App\Services\DynamicFormConfig::isFieldRequired($tenant, 'invoice', 'customer_address') ? '*' : '' }}</label>
                        <textarea name="customer_address" id="customer_address" rows="1" placeholder="Address..." {{ \App\Services\DynamicFormConfig::isFieldRequired($tenant, 'invoice', 'customer_address') ? 'required' : '' }}
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">{{ old('customer_address') }}</textarea>
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'customer_state'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Customer State</label>
                        <input type="text" name="customer_state" id="customer_state" value="{{ old('customer_state', $tenant->state) }}"
                            placeholder="e.g. Jammu & Kashmir"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">State Code (2 Digits)</label>
                        <input type="text" name="customer_state_code" id="customer_state_code" value="{{ old('customer_state_code', $tenant->state_code) }}" oninput="calculateTotals()"
                            placeholder="01"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @else
                    <input type="hidden" name="customer_state" id="customer_state" value="{{ $tenant->state }}">
                    <input type="hidden" name="customer_state_code" id="customer_state_code" value="{{ $tenant->state_code }}">
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'payment_method'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Payment Method *</label>
                        <select name="payment_method" id="payment_method" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-semibold text-[#111111] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                            <option value="upi" {{ old('payment_method') === 'upi' ? 'selected' : '' }}>UPI / GPay / PhonePe</option>
                            <option value="cash" {{ old('payment_method', 'cash') === 'cash' ? 'selected' : '' }}>Cash Counter</option>
                            <option value="bank" {{ old('payment_method') === 'bank' ? 'selected' : '' }}>Bank Transfer / IMPS</option>
                            <option value="mixed" {{ old('payment_method') === 'mixed' ? 'selected' : '' }}>Split / Mixed Payment</option>
                        </select>
                    </div>
                @else
                    <input type="hidden" name="payment_method" id="payment_method" value="cash">
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'invoice_date'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Sale Date *</label>
                        <input type="date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" required
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @else
                    <input type="hidden" name="invoice_date" value="{{ date('Y-m-d') }}">
                @endif
            </div>
        </div>

        <!-- 2. Line Items Container & Centralized Barcode Scanner -->
        <div class="card-surface p-5 sm:p-6 space-y-4 shadow-2xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-[#E6E6E6] pb-3">
                <div>
                    <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        <span>Line Items to Sell</span>
                    </h3>
                    <p class="text-[11px] text-[#079455] font-medium flex items-center gap-1 mt-0.5">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span>Devices added here are automatically saved into your Device Catalogue & Stock Inventory.</span>
                    </p>
                </div>
                <button type="button" onclick="addItemRow()" class="btn-secondary px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 min-h-[36px] self-start sm:self-auto">
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
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="quickSaveDeviceToCatalogue(this)" class="quick-save-stock-btn text-[11px] font-semibold text-[#079455] hover:text-[#067644] bg-[#ECFDF3] hover:bg-[#D1FADF] px-2.5 py-1 rounded-md transition flex items-center gap-1 border border-[#A6F4C5]" title="Immediately catalogue this device to /items stock">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                    <span>Save to Device List</span>
                                </button>
                                <button type="button" onclick="removeItemRow(this)" class="text-xs text-[#DC2626] hover:underline font-semibold flex items-center gap-1 remove-btn {{ $idx === 0 && count($oldItems) === 1 ? 'hidden' : '' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span>Remove</span>
                                </button>
                            </div>
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
                                        data-price="{{ $device->selling_price ?: $device->purchase_price }}"
                                        data-warranty="{{ $device->warranty_status ?? 'no_warranty' }}"
                                        data-warranty-date="{{ $device->warranty_expiry_date ? $device->warranty_expiry_date->format('Y-m-d') : '' }}"
                                        data-notes="{{ $device->notes }}"
                                        {{ (string)($it['item_id'] ?? '') === (string)$device->id ? 'selected' : '' }}>
                                        {{ $device->item_name }} &bull; {{ $device->brand }} {{ $device->model }} &bull; {{ $device->storage }} &bull; ₹{{ number_format((float) ($device->selling_price ?: $device->purchase_price ?: 0), 2) }} (Qty: {{ $device->quantity }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Fields Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                            <div class="sm:col-span-2">
                                <label class="block font-semibold text-[#111111] mb-1">Item / Device Name *</label>
                                <input type="text" name="items[{{ $idx }}][item_name]" value="{{ $it['item_name'] ?? '' }}" required placeholder="e.g. Apple iPhone 15 Pro"
                                    class="row-name w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                            </div>

                            @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'storage_color'))
                                <div>
                                    <label class="block font-semibold text-[#111111] mb-1">Storage / RAM</label>
                                    <input type="text" name="items[{{ $idx }}][storage]" value="{{ $it['storage'] ?? '' }}" placeholder="128GB / 8GB RAM"
                                        class="row-storage w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                                </div>

                                <div>
                                    <label class="block font-semibold text-[#111111] mb-1">Color / Finish</label>
                                    <input type="text" name="items[{{ $idx }}][color]" value="{{ $it['color'] ?? '' }}" placeholder="Natural Titanium"
                                        class="row-color w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                                </div>
                            @endif

                            @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'imei_serial'))
                                <div>
                                    <label class="block font-semibold text-[#111111] mb-1">IMEI 1 (15 digits)</label>
                                    <div class="flex items-center gap-1.5">
                                        <input type="text" name="items[{{ $idx }}][imei_1]" value="{{ $it['imei_1'] ?? '' }}" placeholder="35xxxxxxxxxxxxx"
                                            class="row-imei1 w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg font-mono focus:outline-none focus:ring-1 focus:ring-black">
                                        <button type="button" onclick="startScanner('imei_1', this)" title="Scan IMEI 1"
                                            class="p-2 bg-[#F2F4F7] hover:bg-[#EAECF0] rounded-lg text-[#111111] shrink-0 min-h-[38px] min-w-[38px] flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="block font-semibold text-[#111111] mb-1">IMEI 2 (Optional)</label>
                                    <div class="flex items-center gap-1.5">
                                        <input type="text" name="items[{{ $idx }}][imei_2]" value="{{ $it['imei_2'] ?? '' }}" placeholder="35xxxxxxxxxxxxx"
                                            class="row-imei2 w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg font-mono focus:outline-none focus:ring-1 focus:ring-black">
                                        <button type="button" onclick="startScanner('imei_2', this)" title="Scan IMEI 2"
                                            class="p-2 bg-[#F2F4F7] hover:bg-[#EAECF0] rounded-lg text-[#111111] shrink-0 min-h-[38px] min-w-[38px] flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="block font-semibold text-[#111111] mb-1">Serial Number</label>
                                    <div class="flex items-center gap-1.5">
                                        <input type="text" name="items[{{ $idx }}][serial_number]" value="{{ $it['serial_number'] ?? '' }}" placeholder="F2Lxxxxxxxxx"
                                            class="row-serial w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg font-mono focus:outline-none focus:ring-1 focus:ring-black">
                                        <button type="button" onclick="startScanner('serial_number', this)" title="Scan Serial Number"
                                            class="p-2 bg-[#F2F4F7] hover:bg-[#EAECF0] rounded-lg text-[#111111] shrink-0 min-h-[38px] min-w-[38px] flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <div>
                                <label class="block font-semibold text-[#111111] mb-1">Qty *</label>
                                <input type="number" step="1" min="1" name="items[{{ $idx }}][quantity]" value="{{ $it['quantity'] ?? 1 }}" required
                                    oninput="calculateTotals()"
                                    class="row-qty w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg font-mono font-bold focus:outline-none focus:ring-1 focus:ring-black">
                            </div>

                            <div>
                                <label class="block font-semibold text-[#111111] mb-1">Selling Rate (₹) *</label>
                                <input type="number" step="0.01" min="0" name="items[{{ $idx }}][selling_price]" value="{{ $it['selling_price'] ?? '' }}" required
                                    placeholder="0.00" oninput="calculateTotals()"
                                    class="row-price w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg font-mono font-bold focus:outline-none focus:ring-1 focus:ring-black">
                            </div>

                            @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'warranty'))
                                <div>
                                    <label class="block font-semibold text-[#111111] mb-1">Warranty Term</label>
                                    <select name="items[{{ $idx }}][warranty_status]" class="row-warranty w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                                        <option value="no_warranty" {{ ($it['warranty_status'] ?? '') === 'no_warranty' ? 'selected' : '' }}>No Warranty</option>
                                        <option value="store_warranty" {{ ($it['warranty_status'] ?? '') === 'store_warranty' ? 'selected' : '' }}>Store Warranty</option>
                                        <option value="company_warranty" {{ ($it['warranty_status'] ?? '') === 'company_warranty' ? 'selected' : '' }}>Brand Warranty</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-semibold text-[#111111] mb-1">Warranty Expiry</label>
                                    <input type="date" name="items[{{ $idx }}][warranty_expiry_date]" value="{{ $it['warranty_expiry_date'] ?? '' }}"
                                        class="row-warranty-date w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                                </div>
                            @endif

                            @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'notes'))
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

            <button type="button" onclick="addItemRow()" class="w-full py-2.5 border border-dashed border-[#D6D6D6] hover:border-[#111111] hover:bg-white rounded-lg text-xs font-semibold text-[#666666] hover:text-[#111111] min-h-[44px] transition">
                + Add Another Device / Line Item
            </button>
        </div>

        <!-- 3. Billing Engine & Settlement Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- GST & Pricing Inputs (2 cols) -->
            <div class="lg:col-span-2 card-surface p-5 sm:p-6 space-y-4 shadow-2xs">
                <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                    <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider">
                        Taxation & Settlement
                    </h3>
                    <div id="complianceBadge" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-[#ECFDF3] text-[#027A48] border border-[#A6F4C5]">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#079455]"></span>
                        <span id="complianceText">Intra-State (CGST + SGST)</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- GST Invoice Switch -->
                    @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'is_gst'))
                        <div class="p-3.5 rounded-xl border border-[#E6E6E6] bg-[#F7F7F6] flex items-center justify-between">
                            <div>
                                <label for="is_gst" class="block text-xs font-bold text-[#111111]">Generate GST Invoice</label>
                                <span class="text-[11px] text-[#666666]">Enable to compute CGST / SGST / IGST</span>
                            </div>
                            <input type="checkbox" name="is_gst" id="is_gst" value="1" {{ old('is_gst', '1') ? 'checked' : '' }} onchange="toggleGstControls(); calculateTotals()"
                                class="w-5 h-5 accent-black rounded cursor-pointer">
                        </div>
                    @else
                        <input type="hidden" name="is_gst" id="is_gst" value="0">
                    @endif

                    <!-- GST Rate Selector & Presets -->
                    @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'tax_rate'))
                        <div id="gstRateWrapper" class="space-y-1.5">
                            <label class="block text-xs font-semibold text-[#111111]">
                                GST Tax Rate (%)
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="number" step="0.01" min="0" max="100" name="tax_rate" id="tax_rate"
                                    value="{{ old('tax_rate', $tenant->default_gst_rate ?? 18.00) }}"
                                    oninput="calculateTotals()"
                                    class="w-24 px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-bold text-[#111111] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                                <div class="flex items-center gap-1 flex-wrap">
                                    @foreach([0, 5, 12, 18, 28] as $rate)
                                        <button type="button" onclick="setGstRate({{ $rate }})" class="gst-preset-btn px-2 py-1 bg-white hover:bg-[#111111] hover:text-white border border-[#E6E6E6] rounded text-[11px] font-bold text-[#111111] transition">
                                            {{ $rate }}%
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Tax Mode -->
                    <div id="gstModeWrapper" class="space-y-1.5">
                        <label class="block text-xs font-semibold text-[#111111]">Tax Calculation Mode</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="is_tax_inclusive" value="1" {{ old('is_tax_inclusive', ($tenant->is_tax_inclusive ?? true) ? '1' : '0') === '1' ? 'checked' : '' }} onchange="calculateTotals()" class="peer sr-only">
                                <div class="px-3 py-2 rounded-lg border border-[#E6E6E6] text-center text-xs font-semibold text-[#666666] peer-checked:bg-[#050505] peer-checked:text-[#B7FF00] peer-checked:border-black peer-checked:font-bold transition">
                                    Tax Inclusive (MRP)
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="is_tax_inclusive" value="0" {{ old('is_tax_inclusive', ($tenant->is_tax_inclusive ?? true) ? '1' : '0') === '0' ? 'checked' : '' }} onchange="calculateTotals()" class="peer sr-only">
                                <div class="px-3 py-2 rounded-lg border border-[#E6E6E6] text-center text-xs font-semibold text-[#666666] peer-checked:bg-[#050505] peer-checked:text-[#B7FF00] peer-checked:border-black peer-checked:font-bold transition">
                                    Tax Exclusive (+GST)
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Discount Field -->
                    @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'discount'))
                        <div>
                            <label class="block text-xs font-semibold text-[#111111] mb-1">Discount Amount (₹)</label>
                            <input type="number" step="0.01" min="0" name="discount" id="discount" value="{{ old('discount', 0) }}" oninput="calculateTotals()"
                                class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-semibold focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                        </div>
                    @else
                        <input type="hidden" name="discount" id="discount" value="0">
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
                        <input type="number" step="0.01" min="0" name="amount_paid" id="amount_paid" value="{{ old('amount_paid') }}" oninput="calculateTotals()"
                            placeholder="0.00 (leave blank if nothing paid)"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-sm font-bold font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                        <p class="text-[11px] text-[#666666]">Leave blank or enter 0 if nothing paid (records credit due / Khata). Click 'Full Settlement' above for full payment.</p>
                    </div>
                @endif

                <!-- Invoice Notes / Terms -->
                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'notes'))
                    <div class="pt-2">
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Invoice Notes / Special Terms</label>
                        <input type="text" name="notes" value="{{ old('notes') }}"
                            placeholder="e.g. 7-day checking warranty on hardware. Accessories carry brand warranty."
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif
            </div>

            <!-- Sticky Summary & Checkout Card (1 col) -->
            <div class="space-y-4">
                <div class="bg-black card-dark p-6 rounded-2xl shadow-xl space-y-4">
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

                    <!-- Dynamic UPI QR Code Preview -->
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
                        <span>Generate Bill</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const registeredItems = @json($inStockItems);
    const storeStateCode = "{{ $tenant->state_code ?? '01' }}".trim();
    const storeUpiId = "{{ $tenant->upi_id ?? '' }}";
    const storeName = "{{ addslashes($tenant->name) }}";
    const tenantId = "{{ $tenant->id }}";
    const draftStorageKey = "invoiry_bill_draft_" + tenantId;

    let currentGrandTotal = 0;

    function setFullPaid() {
        const paidInput = document.getElementById('amount_paid');
        if (paidInput) {
            paidInput.value = currentGrandTotal > 0 ? currentGrandTotal.toFixed(2) : '';
            calculateTotals();
        }
    }

    function setGstRate(rate) {
        const input = document.getElementById('tax_rate');
        if (input) {
            input.value = rate;
            calculateTotals();
        }
    }

    function toggleGstControls() {
        const gstEl = document.getElementById('is_gst');
        if (!gstEl) return;
        const isGst = gstEl.checked;
        const rateWrapper = document.getElementById('gstRateWrapper');
        const modeWrapper = document.getElementById('gstModeWrapper');
        if (rateWrapper && modeWrapper) {
            if (isGst) {
                rateWrapper.style.opacity = '1';
                rateWrapper.style.pointerEvents = 'auto';
                modeWrapper.style.opacity = '1';
                modeWrapper.style.pointerEvents = 'auto';
            } else {
                rateWrapper.style.opacity = '0.4';
                rateWrapper.style.pointerEvents = 'none';
                modeWrapper.style.opacity = '0.4';
                modeWrapper.style.pointerEvents = 'none';
            }
        }
    }

    function autoFillCustomer(selectEl) {
        const option = selectEl.options[selectEl.selectedIndex];
        if (!option || !option.value) return;

        const cid = document.getElementById('customer_id'); if (cid) cid.value = option.value;
        const cname = document.getElementById('customer_name'); if (cname) cname.value = option.dataset.name || '';
        const cphone = document.getElementById('customer_phone'); if (cphone) cphone.value = option.dataset.phone || '';
        const cemail = document.getElementById('customer_email'); if (cemail) cemail.value = option.dataset.email || '';
        const cadhar = document.getElementById('customer_adhar'); if (cadhar) cadhar.value = option.dataset.adhar || '';
        const caddr = document.getElementById('customer_address'); if (caddr) caddr.value = option.dataset.address || '';
        const cstate = document.getElementById('customer_state'); if (cstate) cstate.value = option.dataset.state || '';
        const cstatecode = document.getElementById('customer_state_code'); if (cstatecode) cstatecode.value = option.dataset.statecode || '';

        calculateTotals();
        saveLocalDraft();
    }

    function autoFillItemRow(selectEl) {
        const row = selectEl.closest('.item-row');
        const opt = selectEl.options[selectEl.selectedIndex];
        if (!opt || !opt.value) {
            saveLocalDraft();
            return;
        }

        // Prevent duplicate addition of the same inventory item in different rows
        const selectedId = opt.value;
        let isDuplicate = false;
        document.querySelectorAll('.device-select').forEach(sel => {
            if (sel !== selectEl && sel.value === selectedId) {
                isDuplicate = true;
            }
        });

        if (isDuplicate) {
            showCentralFeedback('Warning: This device is already added in another row.', false);
            selectEl.value = '';
            return;
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
        saveLocalDraft();
    }

    function addItemRow(prefillData = null) {
        const wrapper = document.getElementById('itemsWrapper');
        const newIndex = wrapper.querySelectorAll('.item-row').length;

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
                data-price="${item.selling_price || item.purchase_price || ''}"
                data-warranty="${item.warranty_status || 'no_warranty'}"
                data-warranty-date="${item.warranty_expiry_date || ''}"
                data-notes="${item.notes || ''}"
                ${isSel ? 'selected' : ''}>
                ${item.item_name} &bull; ${item.brand || ''} ${item.model || ''} &bull; ${item.storage || item.ram_rom || ''} &bull; ₹${parseFloat(item.selling_price || item.purchase_price || 0).toLocaleString('en-IN', {minimumFractionDigits: 2})} (Qty: ${item.quantity || 1})
            </option>`;
        });

        rowDiv.innerHTML = `
            <div class="flex items-center justify-between border-b border-[#EBEBEB] pb-2">
                <span class="text-xs font-bold text-[#111111] uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded-full bg-[#111111] text-[#B7FF00] inline-flex items-center justify-center text-[10px] font-mono row-number">${newIndex + 1}</span>
                    <span>Device / Item Details</span>
                </span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="quickSaveDeviceToCatalogue(this)" class="quick-save-stock-btn text-[11px] font-semibold text-[#079455] hover:text-[#067644] bg-[#ECFDF3] hover:bg-[#D1FADF] px-2.5 py-1 rounded-md transition flex items-center gap-1 border border-[#A6F4C5]" title="Immediately catalogue this device to /items stock">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        <span>Save to Device List</span>
                    </button>
                    <button type="button" onclick="removeItemRow(this)" class="text-xs text-[#DC2626] hover:underline font-semibold flex items-center gap-1 remove-btn">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        <span>Remove</span>
                    </button>
                </div>
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

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'storage_color'))
                    <div>
                        <label class="block font-semibold text-[#111111] mb-1">Storage / RAM</label>
                        <input type="text" name="items[${newIndex}][storage]" value="${prefillData?.storage || ''}" placeholder="128GB / 8GB RAM"
                            class="row-storage w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                    </div>

                    <div>
                        <label class="block font-semibold text-[#111111] mb-1">Color / Finish</label>
                        <input type="text" name="items[${newIndex}][color]" value="${prefillData?.color || ''}" placeholder="Natural Titanium"
                            class="row-color w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'imei_serial'))
                    <div>
                        <label class="block font-semibold text-[#111111] mb-1">IMEI 1 (15 digits)</label>
                        <div class="flex items-center gap-1.5">
                            <input type="text" name="items[${newIndex}][imei_1]" value="${prefillData?.imei_1 || ''}" placeholder="35xxxxxxxxxxxxx"
                                class="row-imei1 w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg font-mono focus:outline-none focus:ring-1 focus:ring-black">
                            <button type="button" onclick="startScanner('imei_1', this)" title="Scan IMEI 1"
                                class="p-2 bg-[#F2F4F7] hover:bg-[#EAECF0] rounded-lg text-[#111111] shrink-0 min-h-[38px] min-w-[38px] flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-[#111111] mb-1">IMEI 2 (Optional)</label>
                        <div class="flex items-center gap-1.5">
                            <input type="text" name="items[${newIndex}][imei_2]" value="${prefillData?.imei_2 || ''}" placeholder="35xxxxxxxxxxxxx"
                                class="row-imei2 w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg font-mono focus:outline-none focus:ring-1 focus:ring-black">
                            <button type="button" onclick="startScanner('imei_2', this)" title="Scan IMEI 2"
                                class="p-2 bg-[#F2F4F7] hover:bg-[#EAECF0] rounded-lg text-[#111111] shrink-0 min-h-[38px] min-w-[38px] flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-[#111111] mb-1">Serial Number</label>
                        <div class="flex items-center gap-1.5">
                            <input type="text" name="items[${newIndex}][serial_number]" value="${prefillData?.serial_number || ''}" placeholder="F2Lxxxxxxxxx"
                                class="row-serial w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg font-mono focus:outline-none focus:ring-1 focus:ring-black">
                            <button type="button" onclick="startScanner('serial_number', this)" title="Scan Serial Number"
                                class="p-2 bg-[#F2F4F7] hover:bg-[#EAECF0] rounded-lg text-[#111111] shrink-0 min-h-[38px] min-w-[38px] flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            </button>
                        </div>
                    </div>
                @endif

                <div>
                    <label class="block font-semibold text-[#111111] mb-1">Qty *</label>
                    <input type="number" step="1" min="1" name="items[${newIndex}][quantity]" value="${prefillData?.quantity || 1}" required
                        oninput="calculateTotals()"
                        class="row-qty w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg font-mono font-bold focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                <div>
                    <label class="block font-semibold text-[#111111] mb-1">Selling Rate (₹) *</label>
                    <input type="number" step="0.01" min="0" name="items[${newIndex}][selling_price]" value="${prefillData?.selling_price || ''}" required
                        placeholder="0.00" oninput="calculateTotals()"
                        class="row-price w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg font-mono font-bold focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'warranty'))
                    <div>
                        <label class="block font-semibold text-[#111111] mb-1">Warranty Term</label>
                        <select name="items[${newIndex}][warranty_status]" class="row-warranty w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                            <option value="no_warranty" ${prefillData?.warranty_status === 'no_warranty' ? 'selected' : ''}>No Warranty</option>
                            <option value="store_warranty" ${prefillData?.warranty_status === 'store_warranty' ? 'selected' : ''}>Store Warranty</option>
                            <option value="company_warranty" ${prefillData?.warranty_status === 'company_warranty' ? 'selected' : ''}>Brand Warranty</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-[#111111] mb-1">Warranty Expiry</label>
                        <input type="date" name="items[${newIndex}][warranty_expiry_date]" value="${prefillData?.warranty_expiry_date || ''}"
                            class="row-warranty-date w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'notes'))
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
        saveLocalDraft();
        return rowDiv;
    }

    function removeItemRow(btn) {
        const wrapper = document.getElementById('itemsWrapper');
        if (wrapper.querySelectorAll('.item-row').length <= 1) {
            // Clear only row instead of removing
            const row = wrapper.querySelector('.item-row');
            row.querySelectorAll('input, textarea').forEach(el => el.value = '');
            const sel = row.querySelector('.device-select');
            if (sel) sel.selectedIndex = 0;
            const qty = row.querySelector('.row-qty');
            if (qty) qty.value = 1;
            calculateTotals();
            saveLocalDraft();
            return;
        }
        btn.closest('.item-row').remove();
        renumberRows();
        calculateTotals();
        saveLocalDraft();
    }

    async function quickSaveDeviceToCatalogue(button) {
        const row = button.closest('.item-row');
        if (!row) return;

        const itemNameInput = row.querySelector('input[name*="[item_name]"]');
        const itemName = itemNameInput ? itemNameInput.value.trim() : '';

        if (!itemName) {
            alert('Please enter a Device / Model Name before saving to the Device List.');
            if (itemNameInput) itemNameInput.focus();
            return;
        }

        const brandInput = row.querySelector('input[name*="[brand]"]');
        const storageInput = row.querySelector('input[name*="[storage]"]');
        const colorInput = row.querySelector('input[name*="[color]"]');
        const specsInput = row.querySelector('input[name*="[specs]"]');
        const imei1Input = row.querySelector('input[name*="[imei_1]"]');
        const imei2Input = row.querySelector('input[name*="[imei_2]"]');
        const serialInput = row.querySelector('input[name*="[serial_number]"]');
        const priceInput = row.querySelector('input[name*="[selling_price]"]');
        const warrantyInput = row.querySelector('select[name*="[warranty_status]"]');
        const warrantyDateInput = row.querySelector('input[name*="[warranty_expiry_date]"]');
        const qtyInput = row.querySelector('input[name*="[quantity]"]');

        const originalHtml = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="inline-block w-3 h-3 border-2 border-green-700 border-t-transparent rounded-full animate-spin"></span> Saving...';

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const response = await fetch('{{ route("items.quick-store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    item_name: itemName,
                    brand: brandInput?.value || '',
                    storage: storageInput?.value || '',
                    color: colorInput?.value || '',
                    specs: specsInput?.value || '',
                    imei_1: imei1Input?.value || '',
                    imei_2: imei2Input?.value || '',
                    serial_number: serialInput?.value || '',
                    selling_price: priceInput?.value || 0,
                    purchase_price: 0,
                    warranty_status: warrantyInput?.value || 'no_warranty',
                    warranty_expiry_date: warrantyDateInput?.value || null,
                    quantity: qtyInput ? (parseInt(qtyInput.value) || 1) : 1
                })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                button.classList.remove('text-[#079455]', 'bg-[#ECFDF3]', 'border-[#A6F4C5]');
                button.classList.add('text-white', 'bg-[#079455]', 'border-[#079455]');
                button.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg> <span>Saved in /items!</span>';

                // Also update the row's device-select dropdown
                const deviceSelect = row.querySelector('.device-select');
                if (deviceSelect && data.item) {
                    let opt = deviceSelect.querySelector(`option[value="${data.item.id}"]`);
                    if (!opt) {
                        opt = document.createElement('option');
                        opt.value = data.item.id;
                        opt.textContent = `${data.item.item_name} • ${data.item.imei_1 || data.item.serial_number || 'New'} • ₹${parseFloat(data.item.selling_price || 0).toFixed(2)}`;
                        opt.dataset.name = data.item.item_name;
                        opt.dataset.brand = data.item.brand || '';
                        opt.dataset.storage = data.item.storage || '';
                        opt.dataset.color = data.item.color || '';
                        opt.dataset.imei1 = data.item.imei_1 || '';
                        opt.dataset.imei2 = data.item.imei_2 || '';
                        opt.dataset.serial = data.item.serial_number || '';
                        opt.dataset.price = data.item.selling_price || 0;
                        opt.dataset.warranty = data.item.warranty_status || 'no_warranty';
                        deviceSelect.appendChild(opt);
                    }
                    deviceSelect.value = data.item.id;
                }

                setTimeout(() => {
                    button.disabled = false;
                    button.innerHTML = originalHtml;
                    button.classList.remove('text-white', 'bg-[#079455]', 'border-[#079455]');
                    button.classList.add('text-[#079455]', 'bg-[#ECFDF3]', 'border-[#A6F4C5]');
                }, 3000);
            } else {
                alert(data.message || 'Could not save device to stock.');
                button.disabled = false;
                button.innerHTML = originalHtml;
            }
        } catch (err) {
            console.error('Quick store error:', err);
            alert('An error occurred while saving to device catalogue.');
            button.disabled = false;
            button.innerHTML = originalHtml;
        }
    }

    function renumberRows() {
        const rows = document.querySelectorAll('#itemsWrapper .item-row');
        rows.forEach((row, idx) => {
            row.dataset.index = idx;
            const badge = row.querySelector('.row-number');
            if (badge) badge.innerText = idx + 1;

            const removeBtn = row.querySelector('.remove-btn');
            if (removeBtn) {
                if (rows.length === 1) {
                    removeBtn.classList.add('hidden');
                } else {
                    removeBtn.classList.remove('hidden');
                }
            }

            row.querySelectorAll('input, select, textarea').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/items\[\d+\]/, `items[${idx}]`));
                }
            });
        });
    }

    function calculateTotals() {
        let rawSum = 0;
        document.querySelectorAll('#itemsWrapper .item-row').forEach(row => {
            const price = parseFloat(row.querySelector('.row-price')?.value) || 0;
            const qty = parseInt(row.querySelector('.row-qty')?.value, 10) || 1;
            rawSum += (price * qty);
        });

        const isGst = document.getElementById('is_gst')?.checked ?? false;
        const taxRateInput = document.getElementById('tax_rate');
        const taxRate = isGst ? (parseFloat(taxRateInput ? taxRateInput.value : 0) || 0) : 0;

        const modeRadio = document.querySelector('input[name="is_tax_inclusive"]:checked') || document.querySelector('input[name="is_tax_inclusive"][type="hidden"]');
        const isTaxInclusive = modeRadio ? modeRadio.value === '1' : false;

        const discountInput = document.getElementById('discount');
        const discount = discountInput ? (parseFloat(discountInput.value) || 0) : 0;
        const effectiveGross = Math.max(0, rawSum - discount);

        let subtotal = effectiveGross;
        let tax = 0;
        let grandTotal = effectiveGross;

        if (isGst && taxRate > 0) {
            const factor = taxRate / 100;
            if (isTaxInclusive) {
                subtotal = Math.round((effectiveGross / (1 + factor)) * 100) / 100;
                tax = Math.round((effectiveGross - subtotal) * 100) / 100;
                grandTotal = effectiveGross;
            } else {
                subtotal = effectiveGross;
                tax = Math.round((subtotal * factor) * 100) / 100;
                grandTotal = Math.round((subtotal + tax) * 100) / 100;
            }
        }

        currentGrandTotal = grandTotal;

        const custCode = document.getElementById('customer_state_code')?.value?.trim();
        const isInterstate = isGst && custCode && storeStateCode && (custCode !== storeStateCode);

        const badgeText = document.getElementById('complianceText');
        const badgeEl = document.getElementById('complianceBadge');
        if (badgeText && badgeEl) {
            if (!isGst) {
                badgeText.innerText = 'Exempt / Non-GST Sale';
                badgeEl.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-[#F2F4F7] text-[#344054] border border-[#EAECF0]';
            } else if (isInterstate) {
                badgeText.innerText = `Inter-State (Code ${custCode}) · IGST ${taxRate}%`;
                badgeEl.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-[#EFF8FF] text-[#175CD3] border border-[#B2DDFF]';
            } else {
                const halfRate = (taxRate / 2).toFixed(1).replace(/\.0$/, '');
                badgeText.innerText = `Intra-State (Code ${custCode || storeStateCode}) · CGST ${halfRate}% + SGST ${halfRate}%`;
                badgeEl.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-[#ECFDF3] text-[#027A48] border border-[#A6F4C5]';
            }
        }

        document.getElementById('summarySubtotal').innerText = '₹' + rawSum.toLocaleString('en-IN', {minimumFractionDigits: 2});
        document.getElementById('summaryDiscount').innerText = '- ₹' + discount.toLocaleString('en-IN', {minimumFractionDigits: 2});

        const taxableRow = document.getElementById('gstTaxableRow');
        const summaryTaxableBase = document.getElementById('summaryTaxableBase');
        if (isGst && taxRate > 0) {
            taxableRow.style.display = 'flex';
            summaryTaxableBase.innerText = '₹' + subtotal.toLocaleString('en-IN', {minimumFractionDigits: 2});
        } else {
            taxableRow.style.display = 'none';
        }

        const summaryTaxLabel = document.getElementById('summaryTaxLabel');
        const summaryTax = document.getElementById('summaryTax');
        const splitBreakdown = document.getElementById('gstSplitBreakdown');

        if (isGst && taxRate > 0) {
            summaryTaxLabel.innerText = `GST (${taxRate}% ${isTaxInclusive ? 'Incl.' : 'Excl.'}):`;
            summaryTax.innerText = '₹' + tax.toLocaleString('en-IN', {minimumFractionDigits: 2});

            if (isInterstate) {
                splitBreakdown.innerHTML = `<div>• IGST (${taxRate}%): ₹${tax.toLocaleString('en-IN', {minimumFractionDigits: 2})}</div>`;
            } else {
                const halfRate = (taxRate / 2).toFixed(1).replace(/\.0$/, '');
                const halfAmount = (tax / 2).toLocaleString('en-IN', {minimumFractionDigits: 2});
                splitBreakdown.innerHTML = `
                    <div>• Central Tax CGST (${halfRate}%): ₹${halfAmount}</div>
                    <div>• State Tax SGST (${halfRate}%): ₹${halfAmount}</div>
                `;
            }
            splitBreakdown.style.display = 'block';
        } else {
            summaryTaxLabel.innerText = 'GST (0%):';
            summaryTax.innerText = '₹0.00';
            splitBreakdown.style.display = 'none';
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
                    targetRow = addItemRow(item);
                    populateRowWithDevice(targetRow, item);
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
        if (priceInput) priceInput.value = item.selling_price || item.purchase_price || '';
        if (warrantySelect) warrantySelect.value = item.warranty_status || 'no_warranty';
        if (warrantyDateInput) warrantyDateInput.value = item.warranty_expiry_date || '';
        if (notesInput) notesInput.value = item.notes || '';

        calculateTotals();
        saveLocalDraft();
    }

    // Camera Barcode Scanning with HTML5Qrcode
    let html5QrCode = null;
    let activeScanTarget = null;
    let activeTargetInput = null;

    function startScanner(target, btnEl = null) {
        activeScanTarget = target;
        activeTargetInput = null;

        const modal = document.getElementById('scannerModal');
        const title = document.getElementById('scannerModalTitle');
        const instructions = document.getElementById('scannerModalInstructions');

        if (target === 'central') {
            title.innerText = 'Central Device Scanner';
            instructions.innerText = 'Align camera with IMEI 1, IMEI 2, or Serial Number barcode.';
        } else {
            const fieldLabel = target.replace('_', ' ').toUpperCase();
            title.innerText = `Scan ${fieldLabel}`;
            instructions.innerText = `Align camera with ${fieldLabel} barcode on device or retail box.`;

            if (btnEl) {
                const row = btnEl.closest('.item-row');
                if (target === 'imei_1') activeTargetInput = row.querySelector('.row-imei1');
                if (target === 'imei_2') activeTargetInput = row.querySelector('.row-imei2');
                if (target === 'serial_number') activeTargetInput = row.querySelector('.row-serial');
            }
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
            () => {}
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
        } else if (activeTargetInput) {
            activeTargetInput.value = code;
            showCentralFeedback(`Populated scanned barcode: ${code}`, true);
            calculateTotals();
            saveLocalDraft();
        }
    }

    function stopScanner() {
        const modal = document.getElementById('scannerModal');
        if (modal) modal.classList.add('hidden');

        if (html5QrCode) {
            html5QrCode.stop().catch(() => {}).finally(() => {
                html5QrCode = null;
            });
        }
    }

    // Client-Side LocalStorage Draft Persistence
    function saveLocalDraft() {
        try {
            const draft = {
                customer_id: document.getElementById('customer_id')?.value || '',
                customer_name: document.getElementById('customer_name')?.value || '',
                customer_phone: document.getElementById('customer_phone')?.value || '',
                customer_email: document.getElementById('customer_email')?.value || '',
                customer_adhar: document.getElementById('customer_adhar')?.value || '',
                customer_address: document.getElementById('customer_address')?.value || '',
                customer_state: document.getElementById('customer_state')?.value || '',
                customer_state_code: document.getElementById('customer_state_code')?.value || '',
                discount: document.getElementById('discount')?.value || '',
                amount_paid: document.getElementById('amount_paid')?.value || '',
                notes: document.querySelector('input[name="notes"]')?.value || '',
                items: []
            };

            document.querySelectorAll('#itemsWrapper .item-row').forEach(row => {
                draft.items.push({
                    item_id: row.querySelector('.device-select')?.value || '',
                    item_name: row.querySelector('.row-name')?.value || '',
                    storage: row.querySelector('.row-storage')?.value || '',
                    color: row.querySelector('.row-color')?.value || '',
                    imei_1: row.querySelector('.row-imei1')?.value || '',
                    imei_2: row.querySelector('.row-imei2')?.value || '',
                    serial_number: row.querySelector('.row-serial')?.value || '',
                    quantity: row.querySelector('.row-qty')?.value || 1,
                    selling_price: row.querySelector('.row-price')?.value || '',
                    warranty_status: row.querySelector('.row-warranty')?.value || 'no_warranty',
                    warranty_expiry_date: row.querySelector('.row-warranty-date')?.value || '',
                    notes: row.querySelector('.row-notes')?.value || ''
                });
            });

            localStorage.setItem(draftStorageKey, JSON.stringify(draft));
        } catch (e) {}
    }

    function checkLocalDraft() {
        try {
            const saved = localStorage.getItem(draftStorageKey);
            if (!saved) return;
            const draft = JSON.parse(saved);
            if (draft && draft.items && draft.items.length > 0 && draft.items.some(i => i.item_name || i.selling_price)) {
                const currentName = document.getElementById('customer_name')?.value;
                if (!currentName) {
                    const alertBar = document.getElementById('draftAlertBar');
                    if (alertBar) alertBar.classList.remove('hidden');
                }
            }
        } catch (e) {}
    }

    function restoreLocalDraft() {
        try {
            const saved = localStorage.getItem(draftStorageKey);
            if (!saved) return;
            const draft = JSON.parse(saved);

            if (draft.customer_id) document.getElementById('customer_id').value = draft.customer_id;
            if (draft.customer_name) document.getElementById('customer_name').value = draft.customer_name;
            if (draft.customer_phone && document.getElementById('customer_phone')) document.getElementById('customer_phone').value = draft.customer_phone;
            if (draft.customer_email && document.getElementById('customer_email')) document.getElementById('customer_email').value = draft.customer_email;
            if (draft.customer_adhar && document.getElementById('customer_adhar')) document.getElementById('customer_adhar').value = draft.customer_adhar;
            if (draft.customer_address && document.getElementById('customer_address')) document.getElementById('customer_address').value = draft.customer_address;
            if (draft.customer_state && document.getElementById('customer_state')) document.getElementById('customer_state').value = draft.customer_state;
            if (draft.customer_state_code && document.getElementById('customer_state_code')) document.getElementById('customer_state_code').value = draft.customer_state_code;
            if (draft.discount) document.getElementById('discount').value = draft.discount;
            if (draft.amount_paid) document.getElementById('amount_paid').value = draft.amount_paid;
            if (draft.notes && document.querySelector('input[name="notes"]')) document.querySelector('input[name="notes"]').value = draft.notes;

            if (draft.items && draft.items.length > 0) {
                const wrapper = document.getElementById('itemsWrapper');
                wrapper.innerHTML = '';

                draft.items.forEach(it => {
                    addItemRow(it);
                });
            }

            document.getElementById('draftAlertBar')?.classList.add('hidden');
            calculateTotals();
            showCentralFeedback('Draft items restored successfully!', true);
        } catch (e) {
            console.error(e);
        }
    }

    function discardLocalDraft() {
        localStorage.removeItem(draftStorageKey);
        document.getElementById('draftAlertBar')?.classList.add('hidden');
    }

    document.addEventListener('input', function(e) {
        if (e.target.closest('#invoiceForm')) {
            saveLocalDraft();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        toggleGstControls();
        calculateTotals();
        checkLocalDraft();

        const invoiceForm = document.getElementById('invoiceForm');
        if (invoiceForm) {
            invoiceForm.addEventListener('submit', function () {
                localStorage.removeItem(draftStorageKey);
            });
        }
    });
</script>
@endpush
@endsection
