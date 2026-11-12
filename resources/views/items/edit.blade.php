@extends('layouts.app')

@section('title', 'Edit Device · ' . $item->item_name)
@section('page-title', 'Edit Device')
@section('page-subtitle', 'Update hardware specifications, price, or inventory status')

@section('content')
@php
    $tenant = $tenant ?? \App\Services\TenantContext::get();
@endphp
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-[#111111] tracking-tight">Edit Device</h2>
            <p class="text-xs sm:text-sm text-[#666666] mt-0.5">Update hardware specifications, price, or inventory status</p>
        </div>
        <a href="{{ route('items.index') }}" class="text-xs font-semibold text-[#666666] hover:text-[#111111] min-h-[36px] inline-flex items-center">
            Back to Stock
        </a>
    </div>

    <form method="POST" action="{{ route('items.update', $item) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="card-surface p-5 sm:p-6 space-y-4 shadow-2xs">
            <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider border-b border-[#E6E6E6] pb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                <span>Hardware Information</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-[#111111] mb-1">Item / Model Name *</label>
                    <input type="text" name="item_name" value="{{ old('item_name', $item->item_name) }}" required
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#111111] mb-1">Inventory Status *</label>
                    <select name="status" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black font-semibold {{ $item->status === 'in_stock' ? 'text-[#079455]' : 'text-[#666666]' }}">
                        <option value="in_stock" {{ old('status', $item->status) === 'in_stock' ? 'selected' : '' }}>In Stock (Available)</option>
                        <option value="sold" {{ old('status', $item->status) === 'sold' ? 'selected' : '' }}>Sold</option>
                        <option value="returned" {{ old('status', $item->status) === 'returned' ? 'selected' : '' }}>Returned</option>
                    </select>
                </div>

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'brand'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Brand</label>
                        <input type="text" name="brand" value="{{ old('brand', $item->brand) }}"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'storage'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Storage</label>
                        <input type="text" name="storage" value="{{ old('storage', $item->storage) }}"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'color'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Color</label>
                        <input type="text" name="color" value="{{ old('color', $item->color) }}"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'imei_1'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">IMEI 1</label>
                        <input type="text" name="imei_1" value="{{ old('imei_1', $item->imei_1) }}"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'imei_2'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">IMEI 2</label>
                        <input type="text" name="imei_2" value="{{ old('imei_2', $item->imei_2) }}"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'serial_number'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Serial Number</label>
                        <input type="text" name="serial_number" value="{{ old('serial_number', $item->serial_number) }}"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'condition'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Condition *</label>
                        <select name="condition" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                            <option value="used" {{ old('condition', $item->condition) === 'used' ? 'selected' : '' }}>Used</option>
                            <option value="new" {{ old('condition', $item->condition) === 'new' ? 'selected' : '' }}>Brand New</option>
                            <option value="refurbished" {{ old('condition', $item->condition) === 'refurbished' ? 'selected' : '' }}>Refurbished</option>
                        </select>
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'purchase_price'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Purchase Price (Cost) *</label>
                        <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price', $item->purchase_price) }}" required
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-bold focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'selling_price'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Selling Price</label>
                        <input type="number" step="0.01" name="selling_price" value="{{ old('selling_price', $item->selling_price) }}"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-bold text-[#079455] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'warranty_status'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Warranty Status</label>
                        <select name="warranty_status" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                            <option value="no_warranty" {{ old('warranty_status', $item->warranty_status) === 'no_warranty' ? 'selected' : '' }}>No Warranty</option>
                            <option value="store_warranty" {{ old('warranty_status', $item->warranty_status) === 'store_warranty' ? 'selected' : '' }}>Store Warranty</option>
                            <option value="company_warranty" {{ old('warranty_status', $item->warranty_status) === 'company_warranty' ? 'selected' : '' }}>Company Warranty</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Warranty Expiry Date</label>
                        <input type="date" name="warranty_expiry_date" value="{{ old('warranty_expiry_date', $item->warranty_expiry_date?->format('Y-m-d')) }}"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'purchase_date'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Purchase Date</label>
                        <input type="date" name="purchase_date" value="{{ old('purchase_date', $item->purchase_date->format('Y-m-d')) }}" required
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'payment_method'))
                    <div>
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Payment Method</label>
                        <select name="payment_method" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                            <option value="cash" {{ $item->payment_method === 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="upi" {{ $item->payment_method === 'upi' ? 'selected' : '' }}>UPI</option>
                            <option value="bank" {{ $item->payment_method === 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                        </select>
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'supplier'))
                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Supplier</label>
                        <select name="supplier_id" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                            <option value="">Walk-in Seller</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}" {{ $item->supplier_id === $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if(\App\Services\DynamicFormConfig::isFieldVisible($tenant, 'item', 'specs'))
                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-[#111111] mb-1">Specs & Remarks</label>
                        <textarea name="specs" rows="2" class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">{{ old('specs', $item->specs) }}</textarea>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('items.index') }}" class="btn-secondary px-5 py-2.5 rounded-lg text-xs sm:text-sm font-semibold min-h-[44px] inline-flex items-center">
                Cancel
            </a>
            <button type="submit" class="btn-lime px-6 py-2.5 rounded-lg text-xs sm:text-sm font-bold shadow-xs min-h-[44px] active:scale-98 transition">
                Update Device
            </button>
        </div>
    </form>
</div>
@endsection
