@extends('layouts.app')

@php
    $tenant = $tenant ?? \App\Services\TenantContext::get();
    $user = auth()->user();
@endphp

@section('title', 'Inward New Device')
@section('page-title', 'Inward Purchase')
@section('page-subtitle', 'Record stock inward purchased from suppliers or walk-in customers')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-[#111111] tracking-tight">Inward Device (Purchase)</h2>
            <p class="text-xs sm:text-sm text-[#666666]">Record incoming electronic stock purchased from distributors or walk-in sellers</p>
        </div>
        <a href="{{ route('items.index') }}" class="text-xs font-semibold text-[#666666] hover:text-[#111111] min-h-[36px] inline-flex items-center">
            Back to Stock
        </a>
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

            <p class="text-[11px] text-center text-[#666666]">Point camera at phone box IMEI barcode. It will auto-populate the field.</p>

            <button type="button" onclick="stopScanner()" class="w-full py-2.5 bg-[#F2F4F7] hover:bg-[#EAECF0] text-[#111111] rounded-lg text-xs font-semibold min-h-[44px] transition">
                Close Scanner
            </button>
        </div>
    </div>

    <form method="POST" action="{{ route('items.store') }}" class="space-y-6">
        @csrf

        <!-- 1. Device Core Info Card -->
        <div class="card-surface p-5 sm:p-6 space-y-4 shadow-2xs">
            <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    <span>Device Specifications</span>
                </h3>
                <!-- Barcode Scan Trigger -->
                <button type="button" onclick="startScanner('imei_1')" class="btn-secondary px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 min-h-[36px]">
                    <svg class="w-4 h-4 text-[#111111]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    <span>Scan Box Barcode</span>
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-[#111111] mb-1">Item / Model Name *</label>
                    <input type="text" name="item_name" value="{{ old('item_name') }}" required
                        placeholder="e.g. iPhone 16 Pro Max, Samsung S24 Ultra"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'brand'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Brand</label>
                        <input type="text" name="brand" value="{{ old('brand') }}"
                            placeholder="e.g. Apple, Samsung, OnePlus"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'storage'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Storage</label>
                        <input type="text" name="storage" value="{{ old('storage') }}"
                            placeholder="e.g. 128GB, 256GB, 512GB, 1TB"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'color'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Color</label>
                        <input type="text" name="color" value="{{ old('color') }}"
                            placeholder="e.g. Desert Titanium, Space Black"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'condition'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Device Condition</label>
                        <select name="condition" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                            <option value="used" {{ old('condition') === 'used' ? 'selected' : '' }}>Used (Second Hand)</option>
                            <option value="new" {{ old('condition') === 'new' ? 'selected' : '' }}>Brand New (Sealed)</option>
                            <option value="refurbished" {{ old('condition') === 'refurbished' ? 'selected' : '' }}>Refurbished (Certified)</option>
                        </select>
                    </div>
                @endif
            </div>

            <!-- Hardware Identifiers: IMEI 1, IMEI 2, Serial -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'imei_1'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">IMEI 1 (15 Digits)</label>
                        <div class="relative">
                            <input type="text" name="imei_1" id="imei_1" value="{{ old('imei_1') }}"
                                placeholder="8647210..."
                                class="w-full pl-3 pr-9 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                            <button type="button" onclick="startScanner('imei_1')" class="absolute right-2 top-2 text-[#929292] hover:text-[#111111]" title="Scan Barcode">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            </button>
                        </div>
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'imei_2'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">IMEI 2 (Optional)</label>
                        <div class="relative">
                            <input type="text" name="imei_2" id="imei_2" value="{{ old('imei_2') }}"
                                placeholder="8647210..."
                                class="w-full pl-3 pr-9 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                            <button type="button" onclick="startScanner('imei_2')" class="absolute right-2 top-2 text-[#929292] hover:text-[#111111]" title="Scan Barcode">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            </button>
                        </div>
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'serial_number'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Serial Number</label>
                        <input type="text" name="serial_number" value="{{ old('serial_number') }}"
                            placeholder="e.g. F2LWX890K"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif
            </div>

            @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'specs'))
                <div>
                    <label class="block text-xs font-semibold text-[#111111] mb-1">Specs / Notes / Accessories Included</label>
                    <textarea name="specs" rows="2" placeholder="e.g. Battery health 95%, with original box & 20W charger"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">{{ old('specs') }}</textarea>
                </div>
            @endif
        </div>

        <!-- 2. Pricing, Purchase & Warranty Details Card -->
        <div class="card-surface p-5 sm:p-6 space-y-4 shadow-2xs">
            <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider border-b border-[#E6E6E6] pb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span>Purchase & Warranty Terms</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'purchase_price'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Purchase Price (Cost) {{ \App\Services\DynamicFormConfig::isFieldRequired($tenant, 'item', 'purchase_price') ? '*' : '' }}</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-[#929292] font-semibold text-xs sm:text-sm">₹</span>
                            <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price') }}" {{ \App\Services\DynamicFormConfig::isFieldRequired($tenant, 'item', 'purchase_price') ? 'required' : '' }}
                                placeholder="0.00"
                                class="w-full pl-7 pr-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-bold text-[#111111] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                        </div>
                    </div>
                @else
                    <input type="hidden" name="purchase_price" value="0.00">
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'selling_price'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Target Selling Price</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-[#929292] font-semibold text-xs sm:text-sm">₹</span>
                            <input type="number" step="0.01" name="selling_price" value="{{ old('selling_price') }}"
                                placeholder="0.00"
                                class="w-full pl-7 pr-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-bold text-[#079455] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                        </div>
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'purchase_date'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Purchase Date *</label>
                        <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @else
                    <input type="hidden" name="purchase_date" value="{{ date('Y-m-d') }}">
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'payment_method'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Payment Given *</label>
                        <select name="payment_method" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                            <option value="cash">Cash Counter</option>
                            <option value="upi">UPI / Online</option>
                            <option value="bank">Bank Transfer</option>
                        </select>
                    </div>
                @else
                    <input type="hidden" name="payment_method" value="cash">
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'warranty_status'))
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Warranty Status</label>
                        <select name="warranty_status" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                            <option value="no_warranty">No Warranty</option>
                            <option value="store_warranty">Store Testing Warranty (e.g. 1 - 6 Months)</option>
                            <option value="company_warranty">Brand / Company Official Warranty</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Warranty Expiry Date</label>
                        <input type="date" name="warranty_expiry_date" value="{{ old('warranty_expiry_date') }}"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @else
                    <input type="hidden" name="warranty_status" value="no_warranty">
                @endif
            </div>
        </div>

        @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'supplier'))
            <!-- 3. Supplier / Seller Details Card -->
            <div class="card-surface p-5 sm:p-6 space-y-4 shadow-2xs">
                <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                    <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span>Supplier / Seller Details</span>
                    </h3>
                </div>

                <!-- Existing Supplier Selector -->
                @if($suppliers->isNotEmpty())
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Choose Existing Supplier (Or fill new below)</label>
                        <select name="supplier_id" id="supplier_id" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                            <option value="">-- Or enter new supplier / walk-in customer below --</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }} ({{ $sup->phone ?? 'No Phone' }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Seller / Supplier Name</label>
                        <input type="text" name="new_supplier_name" value="{{ old('new_supplier_name') }}"
                            placeholder="e.g. John Doe, Apple Distributor"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Seller Phone</label>
                        <input type="text" name="new_supplier_phone" value="{{ old('new_supplier_phone') }}"
                            placeholder="e.g. 9876543210"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Seller ID / Aadhaar</label>
                        <input type="text" name="new_supplier_adhar" value="{{ old('new_supplier_adhar') }}"
                            placeholder="Aadhaar / Voter ID"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Seller Address</label>
                        <textarea name="new_supplier_address" rows="1" placeholder="Address of seller or supplier..."
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">{{ old('new_supplier_address') }}</textarea>
                    </div>
                </div>
            </div>
        @endif

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('items.index') }}" class="btn-secondary px-5 py-2.5 rounded-lg text-xs sm:text-sm font-semibold min-h-[44px] inline-flex items-center">
                Cancel
            </a>
            <button type="submit" class="btn-lime px-6 py-2.5 rounded-lg text-xs sm:text-sm font-bold shadow-xs min-h-[44px] active:scale-98 transition">
                Save Device to Inventory
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let html5QrcodeScanner = null;
    let targetInputId = 'imei_1';

    function startScanner(targetField) {
        targetInputId = targetField;
        document.getElementById('scannerModal').classList.remove('hidden');
        document.getElementById('scannerModal').classList.add('flex');

        html5QrcodeScanner = new Html5Qrcode("reader");
        const config = { fps: 10, qrbox: { width: 250, height: 150 } };

        html5QrcodeScanner.start(
            { facingMode: "environment" },
            config,
            (decodedText) => {
                const cleanCode = decodedText.trim();
                document.getElementById(targetInputId).value = cleanCode;
                stopScanner();
            },
            () => {}
        ).catch(err => {
            console.error("Camera access error:", err);
            stopScanner();
        });
    }

    function stopScanner() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().then(() => {
                html5QrcodeScanner.clear();
            }).catch(e => console.log(e));
        }
        document.getElementById('scannerModal').classList.add('hidden');
        document.getElementById('scannerModal').classList.remove('flex');
    }
</script>
@endpush
@endsection
