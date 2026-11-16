@extends('layouts.app')

@section('title', 'Inward Spare Part · ' . ($tenant->name ?? 'Mobile Repair'))
@section('page-title', 'Inward Spare Part')
@section('page-subtitle', 'Add replacement screens, batteries, internal components, or materials to workshop inventory')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Action Row -->
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-[#111111] tracking-tight">Add Spare Part / Component</h2>
            <p class="text-xs sm:text-sm text-[#666666] mt-0.5">Inward new spare parts into repair inventory with stock tracking</p>
        </div>
        <a href="{{ route('spare-parts.index') }}" class="btn-secondary px-4 py-2 rounded-lg text-xs font-semibold">
            &larr; Back to Catalogue
        </a>
    </div>

    @if($errors->any())
        <div class="p-4 bg-[#FEF2F2] border border-[#FECACA] rounded-xl text-xs text-[#B91C1C] space-y-1">
            <div class="font-bold">Please correct the following errors before submitting:</div>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('spare-parts.store') }}" class="space-y-6">
        @csrf

        <!-- Card 1: Core Part Identity & Specifications -->
        <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-4">
            <div class="flex items-center gap-2 border-b border-[#E6E6E6] pb-3">
                <span class="w-2 h-2 rounded-full bg-[#D97706]"></span>
                <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Part Identity &amp; Device Compatibility</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-[#344054] mb-1">Spare Part Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        placeholder="e.g. iPhone 13 Pro OLED Display Panel, Samsung S22 Charging Sub-board"
                        class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-bold text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Part Category *</label>
                    <select name="category" required class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-bold text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Brand / Manufacturer</label>
                    <input type="text" name="brand" value="{{ old('brand') }}"
                        placeholder="e.g. Apple, Samsung, OEM, Foxconn"
                        class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-[#344054] mb-1">Compatible Device Models</label>
                    <input type="text" name="compatible_models" value="{{ old('compatible_models') }}"
                        placeholder="e.g. iPhone 13, iPhone 13 Pro, iPhone 14 (Multiple comma-separated)"
                        class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">SKU / Internal Part Code</label>
                    <input type="text" name="sku" value="{{ old('sku') }}"
                        placeholder="e.g. SCR-IP13P-OEM"
                        class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Barcode / EAN</label>
                    <input type="text" name="barcode" value="{{ old('barcode') }}"
                        placeholder="Scan or type barcode string"
                        class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Workshop Shelf / Bin Location</label>
                    <input type="text" name="location" value="{{ old('location') }}"
                        placeholder="e.g. Drawer A-3, Rack 2"
                        class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-xs font-bold text-[#344054] mb-1">Specifications &amp; Quality Notes</label>
                    <textarea name="specifications" rows="2"
                        placeholder="e.g. Grade A Original IC, 100% Battery Health, 120Hz ProMotion supported, includes adhesive pre-cut..."
                        class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">{{ old('specifications') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Card 2: Pricing, Inward Quantity & Supplier -->
        <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-4">
            <div class="flex items-center gap-2 border-b border-[#E6E6E6] pb-3">
                <span class="w-2 h-2 rounded-full bg-[#079455]"></span>
                <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Pricing, Initial Quantity &amp; Vendor</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Inward Purchase Cost (₹) *</label>
                    <input type="number" step="0.01" min="0" name="purchase_price" value="{{ old('purchase_price', '0.00') }}" required
                        class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    <span class="text-[10px] text-[#666666]">Wholesale cost per piece</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Selling / Repair Price (₹) *</label>
                    <input type="number" step="0.01" min="0" name="selling_price" value="{{ old('selling_price', '0.00') }}" required
                        class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-bold font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    <span class="text-[10px] text-[#666666]">Base charge on repair job</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Inward Stock Quantity *</label>
                    <input type="number" min="0" name="stock_quantity" value="{{ old('stock_quantity', '1') }}" required
                        class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-bold font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    <span class="text-[10px] text-[#666666]">Units received into workshop</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Min Stock Alert Level</label>
                    <input type="number" min="0" name="min_stock_level" value="{{ old('min_stock_level', '5') }}"
                        class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                    <span class="text-[10px] text-[#666666]">Triggers low-stock warning</span>
                </div>

                <div class="sm:col-span-2 lg:col-span-4">
                    <label class="block text-xs font-bold text-[#344054] mb-1">Supplier / Vendor Origin</label>
                    <select name="supplier_id" class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-semibold text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111]">
                        <option value="">-- No Supplier Linked (Direct Procurement) --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                                {{ $sup->name }} @if($sup->company_name)({{ $sup->company_name }})@endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Sticky Floating Save Bar -->
        <div class="card-surface p-4 shadow-lg border border-[#E6E6E6] rounded-xl flex items-center justify-between gap-3 sticky bottom-4 z-20">
            <span class="text-xs text-[#666666]">
                Spare parts stock will be immediately credited and available for technician repair jobs.
            </span>
            <div class="flex items-center gap-2">
                <a href="{{ route('spare-parts.index') }}" class="btn-secondary min-h-[44px]">Cancel</a>
                <button type="submit" class="btn-lime min-h-[44px] px-8 text-sm font-bold shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    <span>Save Spare Part &amp; Inward Stock</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

