<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $invoice->invoice_number }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 3mm;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            color: #000;
            width: 74mm;
            margin: 0 auto;
            padding: 5px 0;
            line-height: 1.25;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .double-divider {
            border-top: 1px double #000;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        td {
            padding: 2px 0;
            vertical-align: top;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    @php
        $tenant = $invoice->tenant;
        $customer = $invoice->customer;
    @endphp

    <div class="no-print" style="margin-bottom: 15px; text-align: center;">
        <button onclick="window.print()" style="padding: 6px 12px; background: #000; color: #fff; border: none; font-size: 12px; cursor: pointer; border-radius: 4px;">
            Print Receipt (Ctrl+P)
        </button>
        <button onclick="window.close()" style="padding: 6px 12px; background: #eee; border: 1px solid #ccc; font-size: 12px; cursor: pointer; border-radius: 4px;">
            Close
        </button>
    </div>

    <!-- Shop Header -->
    <div class="text-center">
        <div class="bold" style="font-size: 14px;">{{ $tenant->name }}</div>
        <div>{{ $tenant->address }}</div>
        @if($tenant->phone)<div>Ph: {{ $tenant->phone }}</div>@endif
        @if($tenant->gstin)<div>GSTIN: {{ $tenant->gstin }}</div>@endif
    </div>

    <div class="divider"></div>

    <!-- Bill Meta -->
    <table style="font-size: 10.5px;">
        <tr>
            <td>Bill No: <span class="bold">{{ $invoice->invoice_number }}</span></td>
            <td class="text-right">Date: {{ $invoice->invoice_date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td colspan="2">Cust: <span class="bold">{{ $customer->name }}</span> ({{ $customer->phone }})</td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Items Table -->
    <table>
        <thead>
            <tr class="bold">
                <td style="width: 55%;">Item / IMEI</td>
                <td style="width: 15%; text-align: center;">Qty</td>
                <td style="width: 30%;" class="text-right">Price</td>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td colspan="3" class="bold" style="padding-top: 4px;">
                        {{ $item->item_name }} @if($item->storage)({{ $item->storage }})@endif
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 9.5px; padding-left: 4px;">
                        @if($item->imei_1)IMEI: {{ $item->imei_1 }}<br>@endif
                        @if($item->warranty_status && $item->warranty_status !== 'no_warranty')
                            Warr: {{ ucwords(str_replace('_', ' ', $item->warranty_status)) }}
                        @endif
                    </td>
                    <td style="text-align: center;">1</td>
                    <td class="text-right bold">₹{{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <!-- Totals -->
    <table>
        <tr>
            <td>Subtotal:</td>
            <td class="text-right">₹{{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        @if($invoice->discount > 0)
            <tr>
                <td>Discount:</td>
                <td class="text-right">-₹{{ number_format($invoice->discount, 2) }}</td>
            </tr>
        @endif
        <tr class="bold" style="font-size: 13px;">
            <td>TOTAL:</td>
            <td class="text-right">₹{{ number_format($invoice->grand_total, 2) }}</td>
        </tr>
        <tr>
            <td>Paid ({{ strtoupper($invoice->payment_method) }}):</td>
            <td class="text-right">₹{{ number_format($invoice->amount_paid, 2) }}</td>
        </tr>
        @if($invoice->amount_due > 0)
            <tr class="bold">
                <td>BALANCE DUE:</td>
                <td class="text-right">₹{{ number_format($invoice->amount_due, 2) }}</td>
            </tr>
        @endif
    </table>

    <div class="double-divider"></div>

    @if($tenant->upi_id)
        <div class="text-center" style="font-size: 9.5px;">
            UPI ID: <span class="bold">{{ $tenant->upi_id }}</span>
        </div>
        <div class="divider"></div>
    @endif

    <div class="text-center" style="font-size: 10px;">
        <div>Thank you for your business!</div>
        <div style="font-size: 8.5px; margin-top: 3px;">Goods once sold cannot be returned.</div>
    </div>
</body>
</html>

