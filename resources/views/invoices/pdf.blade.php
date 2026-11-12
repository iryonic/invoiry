<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }} &bull; Tax Invoice</title>
    <style>
        @page {
            margin: 10mm 12mm 10mm 12mm;
            size: a4 portrait;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9.5px;
            color: #000000;
            line-height: 1.35;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td, th {
            vertical-align: top;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .mono {
            font-family: 'DejaVu Sans Mono', monospace, 'Courier New';
        }

        /* Classic Black Border Grid Layout — Image 4 */
        .main-box {
            border: 1.5px solid #000000;
            width: 100%;
            background-color: #ffffff;
        }
        .border-bottom {
            border-bottom: 1.5px solid #000000;
        }
        .border-top {
            border-top: 1.5px solid #000000;
        }
        .border-right {
            border-right: 1.5px solid #000000;
        }
        .border-left {
            border-left: 1.5px solid #000000;
        }

        .header-table td {
            padding: 7px 10px;
        }
        .shop-name {
            font-size: 15px;
            font-weight: bold;
            color: #000000;
            line-height: 1.2;
        }
        .shop-meta {
            font-size: 9px;
            color: #111111;
            margin-top: 2px;
            line-height: 1.35;
        }
        .title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #000000;
        }
        .subtitle {
            font-size: 8.5px;
            color: #444444;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Line Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table th {
            background-color: #f2f2f2;
            border-bottom: 1px solid #000000;
            border-right: 1px solid #000000;
            padding: 5px 6px;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #000000;
        }
        .items-table td {
            border-right: 1px solid #000000;
            padding: 5px 6px;
            font-size: 9px;
            color: #000000;
        }
        .items-table th:last-child, .items-table td:last-child {
            border-right: none;
        }

        /* GST Breakdown Table */
        .tax-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .tax-table th {
            background-color: #f2f2f2;
            border: 1px solid #000000;
            padding: 3px 5px;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .tax-table td {
            border: 1px solid #000000;
            padding: 3px 5px;
            font-size: 8.5px;
            text-align: center;
        }

        .footer-table td {
            padding: 7px 10px;
        }

        .store-logo {
            max-height: 42px;
            max-width: 80px;
            object-fit: contain;
            margin-bottom: 3px;
        }
        .sig-image {
            max-height: 34px;
            max-width: 100px;
            object-fit: contain;
            margin-bottom: 3px;
        }
    </style>
</head>
<body>

    @php
        $tenant = $invoice->tenant;
        $customer = $invoice->customer;
        $logoBase64 = $tenant->logo_base64;
        $sigBase64 = $tenant->signature_base64;
        $firstItem = $invoice->items->first();
    @endphp

    <div class="main-box">

        <!-- 1. Top Header: Store Info (Left) & Retail Invoice Meta (Right) -->
        <table class="header-table border-bottom">
            <tr>
                <td style="width: 58%;">
                    <table>
                        <tr>
                            @if($logoBase64)
                                <td style="width: 50px; padding: 0 8px 0 0; vertical-align: top;">
                                    <img src="{{ $logoBase64 }}" class="store-logo" alt="Logo">
                                </td>
                            @endif
                            <td style="vertical-align: top; padding: 0;">
                                <div class="shop-name">{{ strtoupper($tenant->name) }}</div>
                                @if($tenant->invoice_title && $tenant->invoice_title !== 'Tax Invoice')
                                    <div style="font-size: 8.5px; font-weight: bold; color: #444444; text-transform: uppercase;">{{ $tenant->invoice_title }}</div>
                                @endif
                            </td>
                        </tr>
                    </table>

                    <div class="shop-meta">
                        @if($tenant->address){{ $tenant->address }}{{ $tenant->city ? ', ' . $tenant->city : '' }}{{ $tenant->state ? ', ' . $tenant->state : '' }}<br>@endif
                        @if($tenant->phone)Phone: <strong>{{ $tenant->phone }}</strong>@endif
                        @if($tenant->email) &bull; Email: {{ $tenant->email }}@endif
                        <br>
                        GSTIN/UIN: <strong>{{ $tenant->gstin ?: 'UNREGISTERED' }}</strong><br>
                        State: <strong>{{ $tenant->state ?? 'Jammu & Kashmir' }}</strong>, Code: <strong>{{ $tenant->state_code ?? '01' }}</strong>
                    </div>
                </td>

                <td style="width: 42%; text-align: right;" class="border-left">
                    <div class="title">{{ $tenant->invoice_title ?: ($invoice->is_gst ? 'RETAIL INVOICE' : 'RETAIL CASH MEMO') }}</div>
                    <div class="subtitle">(ORIGINAL FOR RECIPIENT)</div>

                    <div style="margin-top: 5px; font-size: 11px;">
                        Invoice No: <strong class="mono" style="font-size: 13px;">{{ $invoice->invoice_number }}</strong>
                    </div>
                    <div style="font-size: 9.5px; margin-top: 2px;">Dated: <strong>{{ $invoice->invoice_date->format('d-m-Y') }}</strong></div>
                    <div style="font-size: 9.5px; margin-top: 2px;">Mode of Payment: <strong>{{ strtoupper($invoice->payment_method) }}</strong></div>
                </td>
            </tr>
        </table>

        <!-- 2. Details of Receiver (Billed To) -->
        <table class="header-table border-bottom" style="background-color: #fafafa;">
            <tr>
                <td style="width: 100%;">
                    <div style="font-size: 8.5px; text-transform: uppercase; font-weight: bold; color: #444444;">DETAILS OF RECEIVER (BILLED TO):</div>
                    <div style="font-size: 11.5px; font-weight: bold; margin-top: 2px;">{{ strtoupper($customer->name) }}</div>
                    <div style="font-size: 9.5px; margin-top: 1px;">
                        Phone: <strong>{{ $customer->phone }}</strong>
                        @if($customer->email) &bull; Email: {{ $customer->email }}@endif
                    </div>
                    @if($customer->address)
                        <div style="font-size: 9.5px;">Address: {{ $customer->address }}</div>
                    @endif
                    <div style="font-size: 9.5px;">State: {{ $customer->state ?? $tenant->state }}@if($customer->state_code ?? $tenant->state_code), Code: {{ $customer->state_code ?? $tenant->state_code }}@endif</div>
                </td>
            </tr>
        </table>

        <!-- 3. Line Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;" class="text-center">S.N</th>
                    <th style="width: 49%;" class="text-left">DESCRIPTION OF GOODS</th>
                    <th style="width: 11%;" class="text-center">HSN/SAC</th>
                    <th style="width: 7%;" class="text-center">QTY</th>
                    <th style="width: 13%;" class="text-right">RATE (&#8377;)</th>
                    <th style="width: 15%;" class="text-right">AMOUNT (&#8377;)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $idx => $item)
                    @php
                        $itemBase = $item->tax_amount > 0 ? ($item->total_price - $item->tax_amount) : $item->unit_price;
                        $unitBase = $itemBase / max(1, $item->quantity);
                    @endphp
                    <tr>
                        <td class="text-center mono">{{ $idx + 1 }}</td>
                        <td>
                            <strong>{{ $item->item_name }}</strong>
                            @if($item->storage || $item->color)
                                <span>({{ trim(($item->storage ?? '') . ' ' . ($item->color ?? '')) }})</span>
                            @endif

                            <div style="font-size: 8.5px; color: #333333; margin-top: 2px;" class="mono">
                                @if($item->imei_1)IMEI 1: <strong>{{ $item->imei_1 }}</strong><br>@endif
                                @if($item->imei_2)IMEI 2: {{ $item->imei_2 }}<br>@endif
                                @if($item->serial_number)SERIAL NO: {{ $item->serial_number }}<br>@endif
                                @if($item->warranty_status)
                                    Warranty: {{ ucwords(str_replace('_', ' ', $item->warranty_status)) }}
                                    @if($item->warranty_expiry_date) (Valid till {{ $item->warranty_expiry_date->format('d-m-Y') }})@endif
                                @endif
                            </div>
                        </td>
                        <td class="text-center mono">{{ $item->hsn_sac ?: '8517' }}</td>
                        <td class="text-center mono">{{ $item->quantity }} Pcs</td>
                        <td class="text-right mono">{{ number_format($unitBase, 2) }}</td>
                        <td class="text-right mono font-bold">{{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach

                <!-- Minimum spacing row -->
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        </table>

        <!-- 4. Amount Chargeable in Words & Totals Box -->
        <table class="items-table border-top">
            <tr>
                <td colspan="4" style="width: 65%; padding: 6px 8px; vertical-align: top;">
                    <div style="font-size: 9px; color: #444444;"><strong>Amount Chargeable (in words):</strong></div>
                    <div style="font-style: italic; font-weight: bold; font-size: 10px; margin-top: 2px; color: #000000;">
                        {{ $invoice->amountInWords() }}
                    </div>
                </td>
                <td style="width: 15%; text-align: right; font-weight: bold; padding: 4px 6px;">Subtotal:</td>
                <td style="width: 20%; text-align: right; font-weight: bold; padding: 4px 6px;" class="mono">&#8377;{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if($invoice->discount > 0)
                <tr>
                    <td colspan="4" style="padding: 2px 8px;">&nbsp;</td>
                    <td style="text-align: right; color: #b91c1c; padding: 2px 6px;">Discount:</td>
                    <td style="text-align: right; color: #b91c1c; padding: 2px 6px;" class="mono font-bold">-&#8377;{{ number_format($invoice->discount, 2) }}</td>
                </tr>
            @endif
            @if($invoice->tax_amount > 0)
                <tr>
                    <td colspan="4" style="padding: 2px 8px;">&nbsp;</td>
                    <td style="text-align: right; color: #334155; padding: 2px 6px;">Total Tax (GST):</td>
                    <td style="text-align: right; color: #334155; padding: 2px 6px;" class="mono font-bold">&#8377;{{ number_format($invoice->tax_amount, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td colspan="4" style="padding: 2px 8px;">&nbsp;</td>
                <td style="text-align: right; font-weight: bold; font-size: 11px; background-color: #f2f2f2; padding: 5px 6px;">Grand Total:</td>
                <td style="text-align: right; font-weight: bold; font-size: 11px; background-color: #f2f2f2; padding: 5px 6px;" class="mono">&#8377;{{ number_format($invoice->grand_total, 2) }}</td>
            </tr>
            <tr>
                <td colspan="4" style="padding: 2px 8px;">&nbsp;</td>
                <td style="text-align: right; font-size: 9px; padding: 3px 6px;">Amount Paid:</td>
                <td style="text-align: right; font-size: 9px; padding: 3px 6px;" class="mono">&#8377;{{ number_format($invoice->amount_paid, 2) }}</td>
            </tr>
            @if($invoice->amount_due > 0)
                <tr>
                    <td colspan="4" style="padding: 2px 8px;">&nbsp;</td>
                    <td style="text-align: right; font-weight: bold; color: #b45309; padding: 3px 6px;">Balance Due:</td>
                    <td style="text-align: right; font-weight: bold; color: #b45309; padding: 3px 6px;" class="mono">&#8377;{{ number_format($invoice->amount_due, 2) }}</td>
                </tr>
            @endif
        </table>

        <!-- 5. GST Breakdown Table (if GST is enabled) -->
        @if($invoice->is_gst && $invoice->tax_amount > 0)
            <div style="padding: 6px 8px; border-top: 1px solid #000000; background-color: #ffffff;">
                <table class="tax-table">
                    <thead>
                        @if($invoice->isInterState())
                            <tr>
                                <th>HSN/SAC</th>
                                <th>Taxable Value (&#8377;)</th>
                                <th colspan="2">Integrated Tax (IGST)</th>
                                <th>Total Tax Amount (&#8377;)</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>Rate</th>
                                <th>Amount (&#8377;)</th>
                                <th></th>
                            </tr>
                        @else
                            <tr>
                                <th rowspan="2">HSN/SAC</th>
                                <th rowspan="2">Taxable Value (&#8377;)</th>
                                <th colspan="2">Central Tax (CGST)</th>
                                <th colspan="2">State Tax (SGST)</th>
                                <th rowspan="2">Total Tax Amount (&#8377;)</th>
                            </tr>
                            <tr>
                                <th>Rate</th>
                                <th>Amount (&#8377;)</th>
                                <th>Rate</th>
                                <th>Amount (&#8377;)</th>
                            </tr>
                        @endif
                    </thead>
                    <tbody class="mono">
                        @if($invoice->isInterState())
                            <tr>
                                <td>{{ $firstItem?->hsn_sac ?: '8517' }}</td>
                                <td>{{ number_format($invoice->subtotal, 2) }}</td>
                                <td>{{ number_format($invoice->igst_rate, $invoice->igst_rate == (int)$invoice->igst_rate ? 0 : 2) }}%</td>
                                <td>{{ number_format($invoice->igst_amount, 2) }}</td>
                                <td class="font-bold">{{ number_format($invoice->tax_amount, 2) }}</td>
                            </tr>
                        @else
                            <tr>
                                <td>{{ $firstItem?->hsn_sac ?: '8517' }}</td>
                                <td>{{ number_format($invoice->subtotal, 2) }}</td>
                                <td>{{ number_format($invoice->cgst_rate, $invoice->cgst_rate == (int)$invoice->cgst_rate ? 0 : 2) }}%</td>
                                <td>{{ number_format($invoice->cgst_amount, 2) }}</td>
                                <td>{{ number_format($invoice->sgst_rate, $invoice->sgst_rate == (int)$invoice->sgst_rate ? 0 : 2) }}%</td>
                                <td>{{ number_format($invoice->sgst_amount, 2) }}</td>
                                <td class="font-bold">{{ number_format($invoice->tax_amount, 2) }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endif

        <!-- 6. Bank Details, Terms & Signatures -->
        <table class="footer-table border-top">
            <tr>
                <!-- Left: Bank Details & Terms -->
                <td style="width: 58%; font-size: 9px; vertical-align: top; line-height: 1.35;">
                    @if($tenant->bank_name || $tenant->bank_account_no)
                        <strong style="font-size: 9.5px;">Bank Details:</strong><br>
                        Bank: {{ $tenant->bank_name ?: 'Jammu & Kashmir Bank Ltd.' }}<br>
                        A/C No: <strong class="mono">{{ $tenant->bank_account_no ?: '0123040100098765' }}</strong><br>
                        IFSC Code: <strong class="mono">{{ $tenant->bank_ifsc ?: 'JAKA0RESIDENCY' }}</strong> &bull; Branch: {{ $tenant->bank_branch ?: 'Residency Road Branch' }}<br>
                        @if($tenant->upi_id)UPI ID: <strong>{{ $tenant->upi_id }}</strong><br>@endif
                        <br>
                    @endif

                    <strong style="font-size: 9.5px;">Terms &amp; Conditions:</strong><br>
                    @if($tenant->terms_conditions)
                        {!! nl2br(e($tenant->terms_conditions)) !!}
                    @else
                        1. Goods once sold will not be taken back or refunded.<br>
                        2. Warranty claims are subject to company/store policy norms.<br>
                        3. Water damage or physical breakage is not covered under warranty.
                    @endif
                    <br><br>
                    <strong style="font-size: 9.5px;">Declaration:</strong><br>
                    {{ $tenant->declaration ?: 'We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.' }}
                    @if($tenant->invoice_footer_note)
                        <br><strong style="color: #000000;">{{ $tenant->invoice_footer_note }}</strong>
                    @endif
                </td>

                <!-- Right: Customer Seal & Authorized Signatory -->
                <td style="width: 42%; text-align: center; vertical-align: bottom;" class="border-left">
                    <div style="margin-bottom: 45px;">
                        <span style="font-size: 9.5px; color: #444444;">Customer's Seal &amp; Signature</span>
                    </div>

                    <div>
                        <div style="height: 34px;">
                            @if($sigBase64)
                                <img src="{{ $sigBase64 }}" class="sig-image" alt="Signature">
                            @endif
                        </div>
                        <strong style="font-size: 10.5px;">For {{ $tenant->name }}</strong><br>
                        <div style="font-size: 8.5px; color: #555555; margin-top: 1px;">(Authorised Signatory)</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Bottom Computer Generated Note -->
    <div style="text-align: center; font-size: 8px; color: #666666; margin-top: 6px;">
        This is a Computer Generated Tax Invoice &bull; Valid without physical signature where permissible
    </div>

</body>
</html>
