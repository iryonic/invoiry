@extends('layouts.app')

@section('title', 'New Invoice · Quick Bill')
@section('page-title', 'New Invoice')
@section('page-subtitle', 'Sell in-stock devices, scan box barcodes, or record walk-in counter sales')

@section('content')
@php
    $tenant = $tenant ?? \App\Services\TenantContext::get();
    $user = auth()->user();
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
            <!-- Camera Scanner Button -->
            <button type="button" onclick="startScanner()" class="btn-secondary px-3.5 py-2 rounded-lg text-xs sm:text-sm font-semibold flex items-center gap-2 min-h-[44px]">
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
                    <h3 class="text-sm font-bold text-[#111111]">Scan Device Barcode / IMEI</h3>
                </div>
                <button type="button" onclick="stopScanner()" class="p-1 text-[#929292] hover:text-[#111111] rounded-lg min-h-[36px] min-w-[36px] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Viewfinder -->
            <div id="reader" class="rounded-lg overflow-hidden bg-[#050505] min-h-[250px]"></div>

            <p class="text-[11px] text-center text-[#666666]">Align camera with IMEI or box barcode. In-stock items auto-populate the bill.</p>

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

            <input type="hidden" name="customer_id" id="customer_id">

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
                            <option value="upi">UPI / GPay / PhonePe</option>
                            <option value="cash">Cash Counter</option>
                            <option value="bank">Bank Transfer / IMPS</option>
                            <option value="mixed">Split / Mixed Payment</option>
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

        <!-- 2. Line Items Container -->
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

            <!-- Items Wrapper -->
            <div id="itemsWrapper" class="space-y-4">
                <!-- Template / First Item Entry -->
                <div class="item-row p-4 rounded-xl bg-[#F7F7F6] border border-[#E6E6E6] space-y-4" data-index="0">
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-[#111111] uppercase tracking-wider item-label">
                            <span class="w-2 h-2 rounded-full bg-[#B7FF00]"></span> Item #1
                        </span>
                        <button type="button" onclick="removeItemRow(this)" class="text-xs text-[#DC2626] hover:underline font-semibold remove-btn hidden">
                            Remove
                        </button>
                    </div>

                    <!-- In-Stock Picker -->
                    <div class="p-3 bg-white rounded-lg border border-[#E6E6E6]">
                        <label class="block text-[11px] font-bold text-[#111111] uppercase mb-1">
                            Select Available Phone from Stock (Or fill custom item below)
                        </label>
                        <select onchange="onStockItemSelect(this, 0)" class="stock-picker w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-md text-xs font-medium focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                            <option value="">-- Choose from available in-stock devices --</option>
                            @foreach($inStockItems as $stk)
                                <option value="{{ $stk->id }}"
                                    data-name="{{ $stk->item_name }}"
                                    data-storage="{{ $stk->storage }}"
                                    data-color="{{ $stk->color }}"
                                    data-specs="{{ $stk->specs }}"
                                    data-imei1="{{ $stk->imei_1 }}"
                                    data-imei2="{{ $stk->imei_2 }}"
                                    data-serial="{{ $stk->serial_number }}"
                                    data-price="{{ $stk->selling_price ?: $stk->purchase_price }}"
                                    data-warranty="{{ $stk->warranty_status }}"
                                    data-expiry="{{ $stk->warranty_expiry_date?->format('Y-m-d') }}"
                                    {{ request('preselect_item') == $stk->id ? 'selected' : '' }}>
                                    {{ $stk->item_name }} ({{ $stk->storage }}) &bull; {{ $stk->color }} &bull; IMEI: {{ $stk->imei_1 ?: 'N/A' }} &bull; ₹{{ number_format($stk->selling_price ?: $stk->purchase_price, 0) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" name="items[0][item_id]" class="field-item-id" value="{{ request('preselect_item') }}">

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-semibold text-[#111111] mb-1">Device / Item Name *</label>
                            <input type="text" name="items[0][item_name]" required placeholder="e.g. iPhone 16"
                                class="field-name w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-md text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-black">
                        </div>

                        @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'storage_color'))
                            <div>
                                <label class="block text-[11px] font-semibold text-[#111111] mb-1">Storage</label>
                                <input type="text" name="items[0][storage]" placeholder="128GB"
                                    class="field-storage w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-md text-xs focus:outline-none focus:ring-1 focus:ring-black">
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-[#111111] mb-1">Color</label>
                                <input type="text" name="items[0][color]" placeholder="Black"
                                    class="field-color w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-md text-xs focus:outline-none focus:ring-1 focus:ring-black">
                            </div>
                        @endif

                        @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'imei_serial'))
                            <div>
                                <label class="block text-[11px] font-semibold text-[#111111] mb-1">IMEI 1</label>
                                <input type="text" name="items[0][imei_1]" placeholder="8647210..."
                                    class="field-imei1 w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-md text-xs font-mono focus:outline-none focus:ring-1 focus:ring-black">
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-[#111111] mb-1">IMEI 2</label>
                                <input type="text" name="items[0][imei_2]" placeholder="8647210..."
                                    class="field-imei2 w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-md text-xs font-mono focus:outline-none focus:ring-1 focus:ring-black">
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-[#111111] mb-1">Serial Number</label>
                                <input type="text" name="items[0][serial_number]" placeholder="Serial / Part No."
                                    class="field-serial w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-md text-xs font-mono focus:outline-none focus:ring-1 focus:ring-black">
                            </div>
                        @endif

                        <div>
                            <label class="block text-[11px] font-semibold text-[#111111] mb-1">Selling Price (₹) *</label>
                            <input type="number" step="0.01" name="items[0][selling_price]" required placeholder="0.00" oninput="calculateTotals()"
                                class="field-price w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-md text-xs font-bold text-[#111111] focus:outline-none focus:ring-1 focus:ring-black">
                        </div>

                        @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'warranty'))
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-semibold text-[#111111] mb-1">Warranty Coverage</label>
                                <select name="items[0][warranty_status]" class="field-warranty w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-md text-xs focus:outline-none focus:ring-1 focus:ring-black">
                                    <option value="no_warranty">No Warranty</option>
                                    <option value="store_warranty">Store Testing Warranty</option>
                                    <option value="company_warranty">Brand / Company Warranty</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-semibold text-[#111111] mb-1">Warranty Expiry Date</label>
                                <input type="date" name="items[0][warranty_expiry_date]"
                                    class="field-expiry w-full px-3 py-2 bg-white border border-[#E6E6E6] rounded-md text-xs focus:outline-none focus:ring-1 focus:ring-black">
                            </div>
                        @else
                            <input type="hidden" name="items[0][warranty_status]" value="no_warranty">
                        @endif
                    </div>
                </div>
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
                        <div class="p-3.5 rounded-lg bg-[#F7F7F6] border border-[#E6E6E6] flex items-center justify-between">
                            <div>
                                <span class="block text-xs font-semibold text-[#111111]">GST Tax Invoice</span>
                                <span class="block text-[11px] text-[#666666]" id="gstSubtext">Calculate & report statutory taxes</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_gst" id="is_gst" value="1" {{ old('is_gst', '1') ? 'checked' : '' }} onchange="toggleGstControls(); calculateTotals()" class="sr-only peer">
                                <div class="w-11 h-6 bg-[#E6E6E6] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-[#D6D6D6] after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#050505]"></div>
                            </label>
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
                    @else
                        <input type="hidden" name="tax_rate" id="tax_rate" value="{{ $tenant->default_gst_rate ?? 18.00 }}">
                    @endif

                    <!-- Tax Calculation Mode: Inclusive (MRP) vs Exclusive -->
                    @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'tax_inclusive_toggle'))
                        <div id="gstModeWrapper" class="sm:col-span-2 p-3 rounded-lg bg-[#F8FAFC] border border-[#CBD5E1] space-y-2">
                            <span class="block text-xs font-bold text-[#071426] uppercase tracking-wider">Pricing Tax Engine</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                <label class="flex items-center gap-2 p-2 rounded bg-white border border-[#E2E8F0] cursor-pointer hover:border-black transition">
                                    <input type="radio" name="is_tax_inclusive" value="1" {{ old('is_tax_inclusive', $tenant->is_tax_inclusive) ? 'checked' : '' }} onchange="calculateTotals()" class="text-black focus:ring-black">
                                    <div>
                                        <span class="font-bold text-[#111111] block">Tax Inclusive (MRP)</span>
                                        <span class="text-[10px] text-[#666666] block">Item price entered already includes GST</span>
                                    </div>
                                </label>
                                <label class="flex items-center gap-2 p-2 rounded bg-white border border-[#E2E8F0] cursor-pointer hover:border-black transition">
                                    <input type="radio" name="is_tax_inclusive" value="0" {{ !old('is_tax_inclusive', $tenant->is_tax_inclusive) ? 'checked' : '' }} onchange="calculateTotals()" class="text-black focus:ring-black">
                                    <div>
                                        <span class="font-bold text-[#111111] block">Tax Exclusive (Base + GST)</span>
                                        <span class="text-[10px] text-[#666666] block">GST added on top of item base price</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    @else
                        <input type="hidden" name="is_tax_inclusive" value="{{ $tenant->is_tax_inclusive ? '1' : '0' }}">
                    @endif

                    <!-- Discount -->
                    @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'discount'))
                        <div>
                            <label class="block text-xs font-semibold text-[#111111] mb-1">Discount Given (₹)</label>
                            <input type="number" step="0.01" name="discount" id="discount" value="0.00" oninput="calculateTotals()"
                                placeholder="0.00"
                                class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-semibold focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                        </div>
                    @else
                        <input type="hidden" name="discount" id="discount" value="0.00">
                    @endif

                    <!-- Amount Paid / Partial Payment (Khata) -->
                    @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'amount_paid'))
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="amount_paid" class="block text-xs font-semibold text-[#111111]">Amount Paid by Customer (₹)</label>
                                <button type="button" onclick="setFullPaid()" class="text-[11px] font-bold text-[#079455] hover:text-[#067643] hover:underline cursor-pointer">
                                    Pay Full (₹<span id="fullPayAmt">0.00</span>)
                                </button>
                            </div>
                            <input type="number" step="0.01" name="amount_paid" id="amount_paid" oninput="calculateTotals()"
                                value="{{ old('amount_paid') }}"
                                placeholder="Leave empty if nothing paid (₹0.00)"
                                class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-bold text-[#079455] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                            <span class="text-[10px] text-[#666666]">Leave empty if nothing paid at checkout (unpaid/credit bill).</span>
                        </div>
                    @else
                        <input type="hidden" name="amount_paid" id="amount_paid" value="">
                    @endif

                    @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'due_date'))
                        <div>
                            <label class="block text-xs font-semibold text-[#111111] mb-1">Due Date (If Partial / Credit)</label>
                            <input type="date" name="due_date" value="{{ old('due_date') }}"
                                class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                        </div>
                    @endif

                    @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'invoice', 'notes'))
                        <div>
                            <label class="block text-xs font-semibold text-[#111111] mb-1">Remarks / Note on Bill</label>
                            <textarea name="notes" rows="1" placeholder="Optional notes for customer..."
                                class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black"></textarea>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Grand Total Summary & UPI Payment Preview (1 col) -->
            <div class="bg-[#050505] text-[#E7E7E7] rounded-xl p-5 shadow-sm space-y-4 flex flex-col justify-between border border-black">
                <div>
                    <h3 class="text-xs font-bold text-[#929292] uppercase tracking-wider border-b border-white/10 pb-2">
                        Bill Summary
                    </h3>

                    <div class="space-y-2 mt-4 text-xs">
                        <div class="flex justify-between text-[#E7E7E7]">
                            <span>Items Subtotal:</span>
                            <span class="font-mono font-bold" id="summarySubtotal">₹0.00</span>
                        </div>
                        <div class="flex justify-between text-[#E7E7E7]">
                            <span>Discount:</span>
                            <span class="font-mono text-[#B7FF00]" id="summaryDiscount">- ₹0.00</span>
                        </div>
                        <div class="flex justify-between text-[#E7E7E7]" id="gstTaxableRow" style="display: none;">
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

                <!-- Primary Submit Action: Lime Highlight -->
                <div class="pt-3">
                    <button type="submit" id="submitInvoiceBtn" class="w-full py-3 btn-lime rounded-lg font-bold text-xs sm:text-sm shadow-xs min-h-[44px] flex items-center justify-center gap-2">
                        <span>Generate Bill</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let itemIndex = 1;
    let currentGrandTotal = 0;
    const upiVpa = "{{ $tenant->upi_id ?? '' }}";
    const shopName = "{{ $tenant->name ?? 'Invoice' }}";
    const shopStateCode = "{{ $tenant->state_code ?: '01' }}".trim();

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
        const isGst = gstEl.type === 'checkbox' ? gstEl.checked : (gstEl.value === '1');
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
        if (!option.value) return;

        const cid = document.getElementById('customer_id'); if (cid) cid.value = option.value;
        const cname = document.getElementById('customer_name'); if (cname) cname.value = option.dataset.name || '';
        const cphone = document.getElementById('customer_phone'); if (cphone) cphone.value = option.dataset.phone || '';
        const cemail = document.getElementById('customer_email'); if (cemail) cemail.value = option.dataset.email || '';
        const cadhar = document.getElementById('customer_adhar'); if (cadhar) cadhar.value = option.dataset.adhar || '';
        const caddr = document.getElementById('customer_address'); if (caddr) caddr.value = option.dataset.address || '';
    }

    function onStockItemSelect(selectEl, rowIdx) {
        const row = selectEl.closest('.item-row');
        const option = selectEl.options[selectEl.selectedIndex];

        if (!option.value) {
            if (row.querySelector('.field-item-id')) row.querySelector('.field-item-id').value = '';
            return;
        }

        if (row.querySelector('.field-item-id')) row.querySelector('.field-item-id').value = option.value;
        if (row.querySelector('.field-name')) row.querySelector('.field-name').value = option.dataset.name || '';
        if (row.querySelector('.field-storage')) row.querySelector('.field-storage').value = option.dataset.storage || '';
        if (row.querySelector('.field-color')) row.querySelector('.field-color').value = option.dataset.color || '';
        if (row.querySelector('.field-imei1')) row.querySelector('.field-imei1').value = option.dataset.imei1 || '';
        if (row.querySelector('.field-imei2')) row.querySelector('.field-imei2').value = option.dataset.imei2 || '';
        if (row.querySelector('.field-serial')) row.querySelector('.field-serial').value = option.dataset.serial || '';
        if (row.querySelector('.field-price')) row.querySelector('.field-price').value = option.dataset.price || '';
        if (row.querySelector('.field-warranty')) row.querySelector('.field-warranty').value = option.dataset.warranty || 'no_warranty';
        if (row.querySelector('.field-expiry')) row.querySelector('.field-expiry').value = option.dataset.expiry || '';

        calculateTotals();
    }

    function addItemRow() {
        const wrapper = document.getElementById('itemsWrapper');
        const firstRow = document.querySelector('.item-row');
        const clone = firstRow.cloneNode(true);

        clone.dataset.index = itemIndex;
        clone.querySelector('.item-label').innerHTML = `<span class="w-2 h-2 rounded-full bg-[#B7FF00]"></span> Item #${itemIndex + 1}`;
        clone.querySelector('.remove-btn').classList.remove('hidden');

        clone.querySelectorAll('input, select, textarea').forEach(el => {
            const name = el.getAttribute('name');
            if (name) {
                el.setAttribute('name', name.replace(/\[\d+\]/, '[' + itemIndex + ']'));
            }
            if (el.classList.contains('stock-picker')) {
                el.selectedIndex = 0;
                el.setAttribute('onchange', `onStockItemSelect(this, ${itemIndex})`);
            } else {
                el.value = '';
            }
        });

        wrapper.appendChild(clone);
        itemIndex++;
        calculateTotals();
    }

    function removeItemRow(btn) {
        const row = btn.closest('.item-row');
        row.remove();
        calculateTotals();
    }

    function calculateTotals() {
        let rawSum = 0;
        document.querySelectorAll('.field-price').forEach(input => {
            const val = parseFloat(input.value) || 0;
            rawSum += val;
        });

        const discountInput = document.getElementById('discount');
        const discount = discountInput ? (parseFloat(discountInput.value) || 0) : 0;
        const effectiveGross = Math.max(0, rawSum - discount);

        const gstEl = document.getElementById('is_gst');
        const isGst = gstEl ? (gstEl.type === 'checkbox' ? gstEl.checked : (gstEl.value === '1')) : false;
        const taxRateInput = document.getElementById('tax_rate');
        const taxRate = isGst ? (parseFloat(taxRateInput ? taxRateInput.value : 0) || 0) : 0;

        const modeRadio = document.querySelector('input[name="is_tax_inclusive"]:checked') || document.querySelector('input[name="is_tax_inclusive"][type="hidden"]');
        const isTaxInclusive = modeRadio ? modeRadio.value === '1' : false;

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

        // Compliance: Customer State vs Store State
        const custStateCodeEl = document.getElementById('customer_state_code');
        const custStateCode = custStateCodeEl ? custStateCodeEl.value.trim() : '';
        const isInterState = isGst && custStateCode !== '' && shopStateCode !== '' && custStateCode !== shopStateCode;

        // Update badge
        const badgeText = document.getElementById('complianceText');
        const badgeEl = document.getElementById('complianceBadge');
        if (badgeText && badgeEl) {
            if (!isGst) {
                badgeText.innerText = 'Exempt / Non-GST Sale';
                badgeEl.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-[#F2F4F7] text-[#344054] border border-[#EAECF0]';
            } else if (isInterState) {
                badgeText.innerText = `Inter-State (Code ${custStateCode}) · IGST ${taxRate}%`;
                badgeEl.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-[#EFF8FF] text-[#175CD3] border border-[#B2DDFF]';
            } else {
                const halfRate = (taxRate / 2).toFixed(1).replace(/\.0$/, '');
                badgeText.innerText = `Intra-State (Code ${custStateCode || shopStateCode}) · CGST ${halfRate}% + SGST ${halfRate}%`;
                badgeEl.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-[#ECFDF3] text-[#027A48] border border-[#A6F4C5]';
            }
        }

        // Summary Rows
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

            if (isInterState) {
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

        currentGrandTotal = grandTotal;
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

        if (upiVpa) {
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${encodeURIComponent(`upi://pay?pa=${upiVpa}&pn=${encodeURIComponent(shopName)}&am=${grandTotal.toFixed(2)}&tn=Bill%20Payment&cu=INR`)}`;
            const qrImg = document.getElementById('upiQrImg');
            if (qrImg) qrImg.src = qrUrl;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const picker = document.querySelector('.stock-picker');
        if (picker && picker.value) {
            onStockItemSelect(picker, 0);
        }
        toggleGstControls();
        calculateTotals();
    });

    let html5QrcodeScanner = null;

    function startScanner() {
        document.getElementById('scannerModal').classList.remove('hidden');
        document.getElementById('scannerModal').classList.add('flex');

        html5QrcodeScanner = new Html5Qrcode("reader");
        const config = { fps: 10, qrbox: { width: 250, height: 150 } };

        html5QrcodeScanner.start(
            { facingMode: "environment" },
            config,
            (decodedText) => {
                const cleanCode = decodedText.trim();
                fetch(`/api/items/search?q=${encodeURIComponent(cleanCode)}`)
                    .then(r => r.json())
                    .then(items => {
                        if (items.length > 0) {
                            const found = items[0];
                            const firstRow = document.querySelector('.item-row');
                            firstRow.querySelector('.field-item-id').value = found.id;
                            firstRow.querySelector('.field-name').value = found.item_name;
                            firstRow.querySelector('.field-storage').value = found.storage || '';
                            firstRow.querySelector('.field-color').value = found.color || '';
                            firstRow.querySelector('.field-imei1').value = found.imei_1 || cleanCode;
                            firstRow.querySelector('.field-imei2').value = found.imei_2 || '';
                            firstRow.querySelector('.field-serial').value = found.serial_number || '';
                            firstRow.querySelector('.field-price').value = found.selling_price || found.purchase_price;
                            calculateTotals();
                        } else {
                            document.querySelector('.field-imei1').value = cleanCode;
                        }
                        stopScanner();
                    })
                    .catch(() => {
                        document.querySelector('.field-imei1').value = cleanCode;
                        stopScanner();
                    });
            },
            () => {}
        ).catch(err => {
            console.error("Camera error:", err);
            stopScanner();
        });
    }

    function stopScanner() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().then(() => html5QrcodeScanner.clear()).catch(e => console.log(e));
        }
        document.getElementById('scannerModal').classList.add('hidden');
        document.getElementById('scannerModal').classList.remove('flex');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const invoiceForm = document.querySelector('form[action="{{ route('invoices.store') }}"]');
        if (invoiceForm) {
            invoiceForm.addEventListener('submit', function () {
                const btn = document.getElementById('submitInvoiceBtn');
                if (btn && !btn.disabled) {
                    btn.disabled = true;
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                    btn.innerHTML = '<span>Processing Bill...</span>';
                }
            });
        }
    });
</script>
@endpush
@endsection
