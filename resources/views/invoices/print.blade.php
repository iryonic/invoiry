<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $invoice->invoice_number }} &bull; Retail Tax Invoice</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #F7F7F6;
            color: #000000;
        }

        .mono {
            font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, monospace;
        }

        @media screen {
            .invoice-page {
                max-width: 210mm;
                min-height: 297mm;
                margin: 20px auto;
                background: white;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
                border: 1px solid #E6E6E6;
                border-radius: 6px;
                padding: 10mm 12mm;
            }
        }

        /* Mobile & Small Screen Responsive Overrides */
        @media screen and (max-width: 768px) {
            body {
                background-color: #F7F7F6 !important;
            }
            .invoice-page {
                max-width: 100% !important;
                min-height: auto !important;
                margin: 8px !important;
                padding: 12px 10px !important;
                border-radius: 8px !important;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08) !important;
            }
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                color: #000000 !important;
            }
            .invoice-page {
                width: 100% !important;
                max-width: none !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                padding: 6mm 8mm !important;
                margin: 0 !important;
            }
            @page {
                size: A4 portrait;
                margin: 6mm 8mm;
            }
        }
    </style>
</head>
<body class="antialiased min-h-screen">

    @php
        $tenant = $invoice->tenant;
        $customer = $invoice->customer;
        $logoBase64 = $tenant->logo_base64;
        $logoUrl = $logoBase64 ?: $tenant->logo_url;
        $sigBase64 = $tenant->signature_base64;
        $sigUrl = $sigBase64 ?: $tenant->signature_url;
        $firstItem = $invoice->items->first();
    @endphp

    <!-- Sticky Top Utility Header (Hidden when printing) -->
    <header class="no-print sticky top-0 z-50 bg-[#071426] text-white px-3 sm:px-4 py-2.5 sm:py-3 shadow-md flex flex-wrap items-center justify-between gap-2">
        <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
            <a href="{{ route('invoices.show', $invoice) }}" class="text-xs text-slate-300 hover:text-white flex items-center gap-1 font-semibold">
                Back to Bill
            </a>
            <span class="text-white/20">|</span>
            <span class="text-xs font-bold font-mono text-[#B7FF00]">{{ $invoice->invoice_number }}</span>
            <span class="text-[11px] text-slate-300 hidden sm:inline">({{ $invoice->invoice_date->format('d M Y') }})</span>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('invoices.pdf', $invoice) }}" class="btn-secondary text-xs px-3 py-1.5 min-h-[36px] bg-white text-[#071426] hover:bg-slate-100 font-bold">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span>Download PDF</span>
            </a>
            <button type="button" onclick="window.print()" class="btn-lime text-xs px-3.5 py-1.5 font-bold min-h-[36px] flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Print Invoice</span>
            </button>
        </div>
    </header>

    <!-- Printable Invoice Sheet — Exact Classic Retail GST Invoice UI (Image 4) -->
    <div class="invoice-page flex flex-col justify-between text-[#000000]">
        <div>
            <!-- Main Grid Box with 1.5px Black Borders -->
            <div class="border-[1.5px] border-[#000000] bg-white">

                <!-- 1. Top Header: Store Details (Left) and Retail Invoice Meta (Right) -->
                <div class="flex flex-col sm:flex-row print:flex-row border-b-[1.5px] border-[#000000]">
                    <!-- Left Column: Store Details -->
                    <div class="p-3 sm:p-3.5 w-full sm:w-[58%] print:w-[58%] space-y-1">
                        <div class="flex items-center gap-2.5">
                            @if($logoUrl)
                                <img src="{{ $logoUrl }}" alt="{{ $tenant->name }}" class="h-10 w-10 sm:h-12 sm:w-12 object-contain border border-[#E2E8F0] p-0.5 rounded bg-white flex-shrink-0">
                            @endif
                            <div>
                                <h1 class="text-base sm:text-lg font-black tracking-tight text-[#000000] leading-tight">
                                    {{ strtoupper($tenant->name) }}
                                </h1>
                                @if($tenant->invoice_title && $tenant->invoice_title !== 'Tax Invoice')
                                    <div class="text-[9px] font-bold uppercase tracking-wider text-[#444444]">
                                        {{ $tenant->invoice_title }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="text-[11px] sm:text-xs text-[#111111] leading-relaxed pt-1">
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
                    <div class="p-3 sm:p-3.5 w-full sm:w-[42%] print:w-[42%] border-t-[1.5px] sm:border-t-0 sm:border-l-[1.5px] print:border-t-0 print:border-l-[1.5px] border-[#000000] text-left sm:text-right print:text-right flex flex-col justify-between">
                        <div>
                            <div class="text-sm sm:text-base font-black uppercase tracking-wider text-[#000000]">
                                {{ $tenant->invoice_title ?: ($invoice->is_gst ? 'RETAIL INVOICE' : 'RETAIL CASH MEMO') }}
                            </div>
                            <div class="text-[10px] text-[#555555] uppercase tracking-wider">
                                (ORIGINAL FOR RECIPIENT)
                            </div>
                        </div>

                        <div class="mt-2 text-xs space-y-0.5 text-[#111111]">
                            <div>Invoice No: <strong class="font-mono text-sm font-black text-[#000000]">{{ $invoice->invoice_number }}</strong></div>
                            <div>Dated: <strong class="font-semibold">{{ $invoice->invoice_date->format('d-m-Y') }}</strong></div>
                            <div>Mode of Payment: <strong class="font-mono uppercase font-bold">{{ $invoice->payment_method }}</strong></div>
                        </div>
                    </div>
                </div>

                <!-- 2. Details of Receiver (Billed To) -->
                <div class="p-3 sm:p-3.5 bg-[#fafafa] border-b-[1.5px] border-[#000000] text-xs">
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
                <div class="overflow-x-auto print:overflow-visible">
                    <table class="w-full text-left text-xs border-collapse min-w-[600px] sm:min-w-0 print:min-w-0">
                        <thead class="bg-[#f2f2f2] text-[#000000] uppercase text-[10px] font-black tracking-wider border-b border-[#000000]">
                            <tr>
                                <th class="py-2 px-2.5 text-center w-10 border-r border-[#000000]">S.N</th>
                                <th class="py-2 px-3 border-r border-[#000000]">DESCRIPTION OF GOODS</th>
                                <th class="py-2 px-2.5 text-center w-20 border-r border-[#000000]">HSN/SAC</th>
                                <th class="py-2 px-2.5 text-center w-16 border-r border-[#000000]">QTY</th>
                                <th class="py-2 px-3 text-right w-24 border-r border-[#000000]">RATE (&#8377;)</th>
                                <th class="py-2 px-3 text-right w-28">AMOUNT (&#8377;)</th>
                            </tr>
                        </thead>
                        <tbody class="text-[#000000] text-xs">
                            @foreach($invoice->items as $idx => $item)
                                @php
                                    $itemBase = $item->tax_amount > 0 ? ($item->total_price - $item->tax_amount) : $item->unit_price;
                                    $unitBase = $itemBase / max(1, $item->quantity);
                                @endphp
                                <tr class="align-top">
                                    <td class="py-2 px-2.5 text-center font-mono border-r border-[#000000]">{{ $idx + 1 }}</td>
                                    <td class="py-2 px-3 border-r border-[#000000]">
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
                                    <td class="py-2 px-2.5 text-center font-mono border-r border-[#000000]">{{ $item->hsn_sac ?: '8517' }}</td>
                                    <td class="py-2 px-2.5 text-center font-mono border-r border-[#000000]">{{ $item->quantity }} Pcs</td>
                                    <td class="py-2 px-3 text-right font-mono border-r border-[#000000]">{{ number_format($unitBase, 2) }}</td>
                                    <td class="py-2 px-3 text-right font-mono font-bold">{{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @endforeach

                            <!-- Padding row for visual breathing room -->
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
                <div class="border-t-[1.5px] border-[#000000] flex flex-col sm:flex-row print:flex-row text-xs">
                    <!-- Left: Amount in Words -->
                    <div class="p-3 sm:p-3.5 w-full sm:w-[65%] print:w-[65%] border-b sm:border-b-0 print:border-b-0 flex flex-col justify-start">
                        <div class="text-[10px] font-bold text-[#444444]">Amount Chargeable (in words):</div>
                        <div class="text-xs sm:text-sm italic font-bold text-[#000000] mt-1">
                            {{ $invoice->amountInWords() }}
                        </div>
                    </div>

                    <!-- Right: Financial Summary -->
                    <div class="w-full sm:w-[35%] print:w-[35%] border-l-0 sm:border-l-[1.5px] print:border-l-[1.5px] border-[#000000] divide-y divide-[#000000]">
                        <div class="flex justify-between py-1.5 px-3">
                            <span class="font-bold text-[#444444]">Subtotal:</span>
                            <span class="font-mono font-bold text-[#000000]">&#8377;{{ number_format($invoice->subtotal, 2) }}</span>
                        </div>
                        @if($invoice->discount > 0)
                            <div class="flex justify-between py-1.5 px-3 text-[#b91c1c]">
                                <span class="font-semibold">Discount:</span>
                                <span class="font-mono font-bold">-&#8377;{{ number_format($invoice->discount, 2) }}</span>
                            </div>
                        @endif
                        @if($invoice->tax_amount > 0)
                            <div class="flex justify-between py-1.5 px-3 text-[#334155]">
                                <span class="font-semibold">Total Tax (GST):</span>
                                <span class="font-mono font-bold">&#8377;{{ number_format($invoice->tax_amount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between py-2 px-3 bg-[#f2f2f2] font-black text-sm text-[#000000]">
                            <span>Grand Total:</span>
                            <span class="font-mono">&#8377;{{ number_format($invoice->grand_total, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 px-3 text-[11px] text-[#444444]">
                            <span>Amount Paid:</span>
                            <span class="font-mono text-[#000000]">&#8377;{{ number_format($invoice->amount_paid, 2) }}</span>
                        </div>
                        @if($invoice->amount_due > 0)
                            <div class="flex justify-between py-1.5 px-3 text-[11px] font-bold text-[#b45309]">
                                <span>Balance Due:</span>
                                <span class="font-mono">&#8377;{{ number_format($invoice->amount_due, 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 5. GST Breakdown Table (if GST is enabled) -->
                @if($invoice->is_gst && $invoice->tax_amount > 0)
                    <div class="p-3 border-t-[1.5px] border-[#000000] overflow-x-auto print:overflow-visible">
                        <table class="w-full text-center text-xs border border-[#000000] min-w-[500px] sm:min-w-0 print:min-w-0">
                            <thead class="bg-[#f2f2f2] font-bold text-[10px] uppercase">
                                @if($invoice->isInterState())
                                    <tr class="border-b border-[#000000]">
                                        <th class="py-1 px-2 border-r border-[#000000]">HSN/SAC</th>
                                        <th class="py-1 px-2 border-r border-[#000000]">Taxable Value (&#8377;)</th>
                                        <th colspan="2" class="py-1 px-2 border-r border-[#000000]">Integrated Tax (IGST)</th>
                                        <th class="py-1 px-2">Total Tax Amount (&#8377;)</th>
                                    </tr>
                                    <tr class="border-b border-[#000000] text-[9px]">
                                        <th class="py-1 px-2 border-r border-[#000000]"></th>
                                        <th class="py-1 px-2 border-r border-[#000000]"></th>
                                        <th class="py-1 px-2 border-r border-[#000000]">Rate</th>
                                        <th class="py-1 px-2 border-r border-[#000000]">Amount (&#8377;)</th>
                                        <th class="py-1 px-2"></th>
                                    </tr>
                                @else
                                    <tr class="border-b border-[#000000]">
                                        <th rowspan="2" class="py-1 px-2 border-r border-[#000000]">HSN/SAC</th>
                                        <th rowspan="2" class="py-1 px-2 border-r border-[#000000]">Taxable Value (&#8377;)</th>
                                        <th colspan="2" class="py-1 px-2 border-r border-[#000000]">Central Tax (CGST)</th>
                                        <th colspan="2" class="py-1 px-2 border-r border-[#000000]">State Tax (SGST)</th>
                                        <th rowspan="2" class="py-1 px-2">Total Tax Amount (&#8377;)</th>
                                    </tr>
                                    <tr class="border-b border-[#000000] text-[9px]">
                                        <th class="py-1 px-2 border-r border-[#000000]">Rate</th>
                                        <th class="py-1 px-2 border-r border-[#000000]">Amount (&#8377;)</th>
                                        <th class="py-1 px-2 border-r border-[#000000]">Rate</th>
                                        <th class="py-1 px-2 border-r border-[#000000]">Amount (&#8377;)</th>
                                    </tr>
                                @endif
                            </thead>
                            <tbody class="font-mono text-xs divide-y divide-[#000000]">
                                @if($invoice->isInterState())
                                    <tr>
                                        <td class="py-1 px-2 border-r border-[#000000]">{{ $firstItem?->hsn_sac ?: '8517' }}</td>
                                        <td class="py-1 px-2 border-r border-[#000000]">{{ number_format($invoice->subtotal, 2) }}</td>
                                        <td class="py-1 px-2 border-r border-[#000000]">{{ number_format($invoice->igst_rate, $invoice->igst_rate == (int)$invoice->igst_rate ? 0 : 2) }}%</td>
                                        <td class="py-1 px-2 border-r border-[#000000]">{{ number_format($invoice->igst_amount, 2) }}</td>
                                        <td class="py-1 px-2 font-bold">{{ number_format($invoice->tax_amount, 2) }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td class="py-1 px-2 border-r border-[#000000]">{{ $firstItem?->hsn_sac ?: '8517' }}</td>
                                        <td class="py-1 px-2 border-r border-[#000000]">{{ number_format($invoice->subtotal, 2) }}</td>
                                        <td class="py-1 px-2 border-r border-[#000000]">{{ number_format($invoice->cgst_rate, $invoice->cgst_rate == (int)$invoice->cgst_rate ? 0 : 2) }}%</td>
                                        <td class="py-1 px-2 border-r border-[#000000]">{{ number_format($invoice->cgst_amount, 2) }}</td>
                                        <td class="py-1 px-2 border-r border-[#000000]">{{ number_format($invoice->sgst_rate, $invoice->sgst_rate == (int)$invoice->sgst_rate ? 0 : 2) }}%</td>
                                        <td class="py-1 px-2 border-r border-[#000000]">{{ number_format($invoice->sgst_amount, 2) }}</td>
                                        <td class="py-1 px-2 font-bold">{{ number_format($invoice->tax_amount, 2) }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                @endif

                <!-- 6. Bank Details, Terms & Signatures -->
                <div class="border-t-[1.5px] border-[#000000] flex flex-col sm:flex-row print:flex-row text-xs">
                    <!-- Left: Bank Details & Terms -->
                    <div class="p-3 sm:p-3.5 w-full sm:w-[58%] print:w-[58%] text-[11px] leading-relaxed space-y-2">
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
                    <div class="p-3 sm:p-3.5 w-full sm:w-[42%] print:w-[42%] border-t-[1.5px] sm:border-t-0 sm:border-l-[1.5px] print:border-t-0 print:border-l-[1.5px] border-[#000000] flex flex-col justify-between text-center min-h-[140px]">
                        <div>
                            <span class="text-xs text-[#555555]">Customer's Seal &amp; Signature</span>
                        </div>

                        <div class="pt-6">
                            @if($sigUrl)
                                <div class="h-10 flex items-center justify-center mb-1">
                                    <img src="{{ $sigUrl }}" class="max-h-10 max-w-[120px] object-contain" alt="Signature">
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
    </div>

</body>
</html>
