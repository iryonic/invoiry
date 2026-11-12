{{-- Hallmark · macrostructure: Invoice Document View · theme: Tactile Sage & Deep Rail · genre: modern-minimal
 * pre-emit critique: P5 H5 E5 S5 R5 V5
 * slop test: pass
--}}
@extends('layouts.app')

@section('title', 'Invoice ' . $invoice->invoice_number)
@section('page-title', 'Invoice ' . $invoice->invoice_number)

@section('content')
<div class="w-full space-y-5">

    @php
        $tenant = $invoice->tenant;
        $customer = $invoice->customer;
        $whatsAppText = urlencode("Hello {$customer->name}, thank you for your purchase from {$tenant->name}!\n\nInvoice: {$invoice->invoice_number}\nAmount: ₹" . number_format($invoice->grand_total, 2) . "\nDate: " . $invoice->invoice_date->format('d M Y') . "\n\nWe appreciate your business!");
        $whatsAppLink = "https://wa.me/91" . preg_replace('/[^0-9]/', '', $customer->phone) . "?text={$whatsAppText}";
        $firstItem = $invoice->items->first();
        $logoUrl = $tenant->logo_url;
        $sigUrl = $tenant->signature_url;
    @endphp

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 card-surface p-4 shadow-2xs">
        <a href="{{ route('invoices.index') }}" class="text-xs font-semibold text-[#666666] hover:text-[#111111] min-h-[36px] inline-flex items-center">
            Back to Invoices
        </a>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('invoices.print', $invoice) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 btn-lime rounded-lg text-xs font-bold shadow-2xs min-h-[40px] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span class="whitespace-nowrap">Print Invoice</span>
            </a>
            <a href="{{ route('invoices.pdf', $invoice) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 btn-secondary rounded-lg text-xs font-semibold shadow-2xs min-h-[40px] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span class="whitespace-nowrap">Download PDF</span>
            </a>
            <a href="{{ route('invoices.thermal', $invoice) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#050505] hover:bg-[#1a1a1a] text-white rounded-lg text-xs font-semibold shadow-2xs min-h-[40px] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span class="whitespace-nowrap">Thermal POS</span>
            </a>
            <a href="{{ $whatsAppLink }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#ECFDF3] text-[#027A48] border border-[#A6F4C5] rounded-lg text-xs font-semibold shadow-2xs min-h-[40px] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                <span class="whitespace-nowrap">WhatsApp</span>
            </a>
            @if($invoice->payment_status !== 'paid')
                <button type="button" onclick="document.getElementById('paymentModal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#111111] hover:bg-black text-[#B7FF00] rounded-lg text-xs font-bold shadow-2xs min-h-[40px] transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="whitespace-nowrap">Record Payment</span>
                </button>
            @endif
        </div>
    </div>

    <!-- The Official Bill Card — Exact Classic Retail GST Invoice UI (Image 4) -->
    <div class="card-surface p-4 sm:p-8 space-y-4 shadow-sm border border-[#E2E8F0] bg-white text-[#000000]">

        <!-- Main Grid Box with 1.5px Black Borders -->
        <div class="border-[1.5px] border-[#000000] bg-white">

            <!-- 1. Top Header: Store Details (Left) and Retail Invoice Meta (Right) -->
            <div class="flex flex-col sm:flex-row border-b-[1.5px] border-[#000000]">
                <!-- Left Column: Store Details -->
                <div class="p-3 sm:p-4 w-full sm:w-[58%] space-y-1">
                    <div class="flex items-center gap-3">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $tenant->name }}" class="h-10 w-10 sm:h-12 sm:w-12 object-contain border border-[#E2E8F0] p-0.5 rounded bg-white flex-shrink-0">
                        @endif
                        <div>
                            <h2 class="text-base sm:text-xl font-black tracking-tight text-[#000000] leading-tight">
                                {{ strtoupper($tenant->name) }}
                            </h2>
                            @if($tenant->invoice_title && $tenant->invoice_title !== 'Tax Invoice')
                                <div class="text-[9px] font-bold uppercase tracking-wider text-[#444444]">
                                    {{ $tenant->invoice_title }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="text-xs text-[#111111] leading-relaxed pt-1">
                        @if($tenant->address)<p class="font-normal">{{ $tenant->address }}{{ $tenant->city ? ', ' . $tenant->city : '' }}{{ $tenant->state ? ', ' . $tenant->state : '' }}</p>@endif
                        <p class="text-[11px] font-mono">
                            @if($tenant->phone)Phone: <strong>{{ $tenant->phone }}</strong>@endif
                            @if($tenant->email) &bull; Email: {{ $tenant->email }}@endif
                        </p>
                        <p class="text-[11px] font-mono">GSTIN/UIN: <strong>{{ $tenant->gstin ?: 'UNREGISTERED' }}</strong></p>
                        <p class="text-[11px]">State: <strong>{{ $tenant->state ?? 'Jammu & Kashmir' }}</strong>, Code: <strong>{{ $tenant->state_code ?? '01' }}</strong></p>
                    </div>
                </div>

                <!-- Right Column: Retail Invoice Meta -->
                <div class="p-3 sm:p-4 w-full sm:w-[42%] border-t-[1.5px] sm:border-t-0 sm:border-l-[1.5px] border-[#000000] text-left sm:text-right flex flex-col justify-between">
                    <div>
                        <div class="text-base sm:text-lg font-black uppercase tracking-wider text-[#000000]">
                            {{ $tenant->invoice_title ?: ($invoice->is_gst ? 'RETAIL INVOICE' : 'RETAIL CASH MEMO') }}
                        </div>
                        <div class="text-[10px] text-[#555555] uppercase tracking-wider">
                            (ORIGINAL FOR RECIPIENT)
                        </div>
                    </div>

                    <div class="mt-3 text-xs space-y-0.5 text-[#111111]">
                        <div>Invoice No: <strong class="font-mono text-sm sm:text-base font-black text-[#000000]">{{ $invoice->invoice_number }}</strong></div>
                        <div>Dated: <strong class="font-semibold">{{ $invoice->invoice_date->format('d-m-Y') }}</strong></div>
                        <div>Mode of Payment: <strong class="font-mono uppercase font-bold">{{ $invoice->payment_method }}</strong></div>
                    </div>
                </div>
            </div>

            <!-- 2. Details of Receiver (Billed To) -->
            <div class="p-3 sm:p-4 bg-[#fafafa] border-b-[1.5px] border-[#000000] text-xs">
                <div class="text-[10px] uppercase font-bold tracking-wider text-[#444444]">
                    DETAILS OF RECEIVER (BILLED TO):
                </div>
                <div class="text-sm sm:text-base font-black text-[#000000] mt-0.5">
                    {{ strtoupper($customer->name) }}
                </div>
                <div class="text-xs text-[#222222] mt-0.5 font-mono">
                    Phone: <strong>{{ $customer->phone }}</strong>
                    @if($customer->email) &bull; Email: {{ $customer->email }}@endif
                </div>
                @if($customer->address)
                    <div class="text-xs text-[#222222] mt-0.5">Address: {{ $customer->address }}</div>
                @endif
                <div class="text-xs text-[#222222] mt-0.5">
                    State: {{ $customer->state ?? $tenant->state }}@if($customer->state_code ?? $tenant->state_code), Code: {{ $customer->state_code ?? $tenant->state_code }}@endif
                </div>
            </div>

            <!-- 3. Line Items Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse min-w-[620px] sm:min-w-0">
                    <thead class="bg-[#f2f2f2] text-[#000000] uppercase text-[10px] font-black tracking-wider border-b border-[#000000]">
                        <tr>
                            <th class="py-2.5 px-3 text-center w-10 border-r border-[#000000]">S.N</th>
                            <th class="py-2.5 px-3 border-r border-[#000000]">DESCRIPTION OF GOODS</th>
                            <th class="py-2.5 px-3 text-center w-24 border-r border-[#000000]">HSN/SAC</th>
                            <th class="py-2.5 px-3 text-center w-16 border-r border-[#000000]">QTY</th>
                            <th class="py-2.5 px-3 text-right w-28 border-r border-[#000000]">RATE (₹)</th>
                            <th class="py-2.5 px-3 text-right w-32">AMOUNT (₹)</th>
                        </tr>
                    </thead>
                    <tbody class="text-[#000000] text-xs">
                        @foreach($invoice->items as $idx => $item)
                            @php
                                $itemBase = $item->tax_amount > 0 ? ($item->total_price - $item->tax_amount) : $item->unit_price;
                                $unitBase = $itemBase / max(1, $item->quantity);
                            @endphp
                            <tr class="align-top hover:bg-slate-50 transition">
                                <td class="py-2.5 px-3 text-center font-mono border-r border-[#000000]">{{ $idx + 1 }}</td>
                                <td class="py-2.5 px-3 border-r border-[#000000]">
                                    <span class="font-bold text-[#000000]">{{ $item->item_name }}</span>
                                    @if($item->storage || $item->color)
                                        <span class="text-[#444444]">({{ trim(($item->storage ?? '') . ' ' . ($item->color ?? '')) }})</span>
                                    @endif

                                    <div class="text-[11px] text-[#333333] space-y-0.5 mt-1 font-mono">
                                        @if($item->imei_1)<p>IMEI 1: <strong>{{ $item->imei_1 }}</strong></p>@endif
                                        @if($item->imei_2)<p>IMEI 2: {{ $item->imei_2 }}</p>@endif
                                        @if($item->serial_number)<p>SERIAL NO: {{ $item->serial_number }}</p>@endif
                                        @if($item->warranty_status)
                                            <p class="font-sans">Warranty: {{ ucwords(str_replace('_', ' ', $item->warranty_status)) }}
                                                @if($item->warranty_expiry_date) (Valid till {{ $item->warranty_expiry_date->format('d-m-Y') }})@endif
                                            </p>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-2.5 px-3 text-center font-mono border-r border-[#000000]">{{ $item->hsn_sac ?: '8517' }}</td>
                                <td class="py-2.5 px-3 text-center font-mono border-r border-[#000000]">{{ $item->quantity }} Pcs</td>
                                <td class="py-2.5 px-3 text-right font-mono border-r border-[#000000]">₹{{ number_format($unitBase, 2) }}</td>
                                <td class="py-2.5 px-3 text-right font-mono font-bold">₹{{ number_format($item->total_price, 2) }}</td>
                            </tr>
                        @endforeach

                        <!-- Spacing row -->
                        <tr>
                            <td class="py-3 border-r border-[#000000]">&nbsp;</td>
                            <td class="py-3 border-r border-[#000000]">&nbsp;</td>
                            <td class="py-3 border-r border-[#000000]">&nbsp;</td>
                            <td class="py-3 border-r border-[#000000]">&nbsp;</td>
                            <td class="py-3 border-r border-[#000000]">&nbsp;</td>
                            <td class="py-3">&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- 4. Amount Chargeable in Words & Totals Box -->
            <div class="border-t-[1.5px] border-[#000000] flex flex-col sm:flex-row text-xs">
                <!-- Left: Amount in Words -->
                <div class="p-3 sm:p-4 w-full sm:w-[65%] border-b sm:border-b-0 flex flex-col justify-start">
                    <div class="text-[10px] font-bold text-[#444444]">Amount Chargeable (in words):</div>
                    <div class="text-xs sm:text-sm italic font-bold text-[#000000] mt-1">
                        {{ $invoice->amountInWords() }}
                    </div>
                </div>

                <!-- Right: Financial Summary -->
                <div class="w-full sm:w-[35%] border-l-0 sm:border-l-[1.5px] border-[#000000] divide-y divide-[#000000]">
                    <div class="flex justify-between py-1.5 px-3 sm:px-4">
                        <span class="font-bold text-[#444444]">Subtotal:</span>
                        <span class="font-mono font-bold text-[#000000]">₹{{ number_format($invoice->subtotal, 2) }}</span>
                    </div>
                    @if($invoice->discount > 0)
                        <div class="flex justify-between py-1.5 px-3 sm:px-4 text-[#b91c1c]">
                            <span class="font-semibold">Discount:</span>
                            <span class="font-mono font-bold">-₹{{ number_format($invoice->discount, 2) }}</span>
                        </div>
                    @endif
                    @if($invoice->tax_amount > 0)
                        <div class="flex justify-between py-1.5 px-3 sm:px-4 text-[#334155]">
                            <span class="font-semibold">Total Tax (GST):</span>
                            <span class="font-mono font-bold">₹{{ number_format($invoice->tax_amount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between py-2 px-3 sm:px-4 bg-[#f2f2f2] font-black text-sm sm:text-base text-[#000000]">
                        <span>Grand Total:</span>
                        <span class="font-mono">₹{{ number_format($invoice->grand_total, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 px-3 sm:px-4 text-[11px] text-[#444444]">
                        <span>Amount Paid:</span>
                        <span class="font-mono text-[#000000]">₹{{ number_format($invoice->amount_paid, 2) }}</span>
                    </div>
                    @if($invoice->amount_due > 0)
                        <div class="flex justify-between py-1.5 px-3 sm:px-4 text-[11px] font-bold text-[#b45309]">
                            <span>Balance Due:</span>
                            <span class="font-mono">₹{{ number_format($invoice->amount_due, 2) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 5. GST Breakdown Table (if GST is enabled) -->
            @if($invoice->is_gst && $invoice->tax_amount > 0)
                <div class="p-3 sm:p-4 border-t-[1.5px] border-[#000000] overflow-x-auto">
                    <table class="w-full text-center text-xs border border-[#000000] min-w-[500px] sm:min-w-0">
                        <thead class="bg-[#f2f2f2] font-bold text-[10px] uppercase">
                            @if($invoice->isInterState())
                                <tr class="border-b border-[#000000]">
                                    <th class="py-1.5 px-2 border-r border-[#000000]">HSN/SAC</th>
                                    <th class="py-1.5 px-2 border-r border-[#000000]">Taxable Value (₹)</th>
                                    <th colspan="2" class="py-1.5 px-2 border-r border-[#000000]">Integrated Tax (IGST)</th>
                                    <th class="py-1.5 px-2">Total Tax Amount (₹)</th>
                                </tr>
                                <tr class="border-b border-[#000000] text-[9px]">
                                    <th class="py-1 px-2 border-r border-[#000000]"></th>
                                    <th class="py-1 px-2 border-r border-[#000000]"></th>
                                    <th class="py-1 px-2 border-r border-[#000000]">Rate</th>
                                    <th class="py-1 px-2 border-r border-[#000000]">Amount (₹)</th>
                                    <th class="py-1 px-2"></th>
                                </tr>
                            @else
                                <tr class="border-b border-[#000000]">
                                    <th rowspan="2" class="py-1.5 px-2 border-r border-[#000000]">HSN/SAC</th>
                                    <th rowspan="2" class="py-1.5 px-2 border-r border-[#000000]">Taxable Value (₹)</th>
                                    <th colspan="2" class="py-1.5 px-2 border-r border-[#000000]">Central Tax (CGST)</th>
                                    <th colspan="2" class="py-1.5 px-2 border-r border-[#000000]">State Tax (SGST)</th>
                                    <th rowspan="2" class="py-1.5 px-2">Total Tax Amount (₹)</th>
                                </tr>
                                <tr class="border-b border-[#000000] text-[9px]">
                                    <th class="py-1 px-2 border-r border-[#000000]">Rate</th>
                                    <th class="py-1 px-2 border-r border-[#000000]">Amount (₹)</th>
                                    <th class="py-1 px-2 border-r border-[#000000]">Rate</th>
                                    <th class="py-1 px-2 border-r border-[#000000]">Amount (₹)</th>
                                </tr>
                            @endif
                        </thead>
                        <tbody class="font-mono text-xs divide-y divide-[#000000]">
                            @if($invoice->isInterState())
                                <tr>
                                    <td class="py-1.5 px-2 border-r border-[#000000]">{{ $firstItem?->hsn_sac ?: '8517' }}</td>
                                    <td class="py-1.5 px-2 border-r border-[#000000]">{{ number_format($invoice->subtotal, 2) }}</td>
                                    <td class="py-1.5 px-2 border-r border-[#000000]">{{ number_format($invoice->igst_rate, $invoice->igst_rate == (int)$invoice->igst_rate ? 0 : 2) }}%</td>
                                    <td class="py-1.5 px-2 border-r border-[#000000]">{{ number_format($invoice->igst_amount, 2) }}</td>
                                    <td class="py-1.5 px-2 font-bold">{{ number_format($invoice->tax_amount, 2) }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td class="py-1.5 px-2 border-r border-[#000000]">{{ $firstItem?->hsn_sac ?: '8517' }}</td>
                                    <td class="py-1.5 px-2 border-r border-[#000000]">{{ number_format($invoice->subtotal, 2) }}</td>
                                    <td class="py-1.5 px-2 border-r border-[#000000]">{{ number_format($invoice->cgst_rate, $invoice->cgst_rate == (int)$invoice->cgst_rate ? 0 : 2) }}%</td>
                                    <td class="py-1.5 px-2 border-r border-[#000000]">{{ number_format($invoice->cgst_amount, 2) }}</td>
                                    <td class="py-1.5 px-2 border-r border-[#000000]">{{ number_format($invoice->sgst_rate, $invoice->sgst_rate == (int)$invoice->sgst_rate ? 0 : 2) }}%</td>
                                    <td class="py-1.5 px-2 border-r border-[#000000]">{{ number_format($invoice->sgst_amount, 2) }}</td>
                                    <td class="py-1.5 px-2 font-bold">{{ number_format($invoice->tax_amount, 2) }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- 6. Bank Details, Terms & Signatures -->
            <div class="border-t-[1.5px] border-[#000000] flex flex-col sm:flex-row text-xs">
                <!-- Left: Bank Details & Terms -->
                <div class="p-3 sm:p-4 w-full sm:w-[58%] text-[11px] leading-relaxed space-y-2">
                    @if($tenant->bank_name || $tenant->bank_account_no)
                        <div>
                            <strong class="text-xs">Bank Details:</strong><br>
                            Bank: {{ $tenant->bank_name ?: 'Jammu & Kashmir Bank Ltd.' }}<br>
                            A/C No: <strong class="font-mono">{{ $tenant->bank_account_no ?: '0123040100098765' }}</strong><br>
                            IFSC Code: <strong class="font-mono">{{ $tenant->bank_ifsc ?: 'JAKA0RESIDENCY' }}</strong> &bull; Branch: {{ $tenant->bank_branch ?: 'Residency Road Branch' }}<br>
                            @if($tenant->upi_id)UPI ID: <strong class="font-mono">{{ $tenant->upi_id }}</strong>@endif
                        </div>
                    @endif

                    <div>
                        <strong class="text-xs">Terms &amp; Conditions:</strong><br>
                        @if($tenant->terms_conditions)
                            {!! nl2br(e($tenant->terms_conditions)) !!}
                        @else
                            1. Goods once sold will not be taken back or refunded.<br>
                            2. Warranty claims are subject to company/store policy norms.<br>
                            3. Water damage or physical breakage is not covered under warranty.
                        @endif
                    </div>

                    <div>
                        <strong class="text-xs">Declaration:</strong><br>
                        {{ $tenant->declaration ?: 'We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.' }}
                        @if($tenant->invoice_footer_note)
                            <br><strong class="text-[#000000]">{{ $tenant->invoice_footer_note }}</strong>
                        @endif
                    </div>
                </div>

                <!-- Right: Customer Seal & Authorized Signatory -->
                <div class="p-3 sm:p-4 w-full sm:w-[42%] border-t-[1.5px] sm:border-t-0 sm:border-l-[1.5px] border-[#000000] flex flex-col justify-between text-center min-h-[140px]">
                    <div>
                        <span class="text-xs text-[#555555]">Customer's Seal &amp; Signature</span>
                    </div>

                    <div class="pt-6">
                        @if($tenant->signature_url)
                            <div class="h-10 flex items-center justify-center mb-1">
                                <img src="{{ $tenant->signature_url }}" class="max-h-10 max-w-[120px] object-contain" alt="Signature">
                            </div>
                        @endif
                        <strong class="text-xs sm:text-sm">For {{ $tenant->name }}</strong><br>
                        <span class="text-[10px] text-[#555555]">(Authorised Signatory)</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Bottom Computer Generated Note -->
        <div class="text-center text-[10px] text-[#666666] mt-2">
            This is a Computer Generated Tax Invoice &bull; Valid without physical signature where permissible
        </div>
    </div>

    <!-- Payment Modal (Hidden by default) -->
    @if($invoice->payment_status !== 'paid')
        <div id="paymentModal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden">
            <div class="bg-white rounded-xl max-w-md w-full p-6 space-y-4 shadow-xl">
                <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
                    <h3 class="text-base font-bold text-[#111111]">Record Payment &bull; {{ $invoice->invoice_number }}</h3>
                    <button type="button" onclick="document.getElementById('paymentModal').classList.add('hidden')" class="text-[#929292] hover:text-black">&times;</button>
                </div>

                <form method="POST" action="{{ route('invoices.payments.store', $invoice) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-[#666666] mb-1">Amount Due</label>
                        <div class="text-xl font-black text-[#111111] font-mono">₹{{ number_format($invoice->amount_due, 2) }}</div>
                    </div>

                    <div>
                        <label for="payment_amount" class="block text-xs font-semibold text-[#666666] mb-1">Payment Amount (₹) *</label>
                        <input type="number" id="payment_amount" name="amount" step="0.01" min="1" max="{{ $invoice->amount_due }}" value="{{ $invoice->amount_due }}" required
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-[#111111] min-h-[44px]">
                    </div>

                    <div>
                        <label for="payment_method" class="block text-xs font-semibold text-[#666666] mb-1">Payment Method *</label>
                        <select id="payment_method" name="payment_method" required class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#111111] min-h-[44px]">
                            <option value="cash">Cash</option>
                            <option value="upi">UPI (GPay, PhonePe, Paytm)</option>
                            <option value="card">Debit / Credit Card</option>
                            <option value="bank_transfer">Net Banking / NEFT</option>
                        </select>
                    </div>

                    <div>
                        <label for="payment_notes" class="block text-xs font-semibold text-[#666666] mb-1">Payment Reference / Notes</label>
                        <input type="text" id="payment_notes" name="notes" placeholder="e.g. UTR / Transaction ID"
                            class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#111111] min-h-[44px]">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#E6E6E6]">
                        <button type="button" onclick="document.getElementById('paymentModal').classList.add('hidden')" class="btn-secondary min-h-[40px] text-xs">
                            Cancel
                        </button>
                        <button type="submit" class="btn-lime min-h-[40px] text-xs font-bold">
                            Submit Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
@endsection
