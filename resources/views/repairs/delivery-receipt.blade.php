<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gate Pass #{{ $repairJob->job_number }} · {{ $tenant->name ?? 'Workshop' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #F7F7F6;
            color: #111111;
        }

        .mono {
            font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, monospace;
        }

        @media screen {
            .print-page {
                max-width: 210mm;
                min-height: 297mm;
                margin: 20px auto;
                background: white;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
                border: 1px solid #E6E6E6;
                border-radius: 8px;
                padding: 12mm 15mm;
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
            }
            .print-page {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 10mm 12mm !important;
                border: none !important;
                box-shadow: none !important;
            }
            @page {
                size: A4;
                margin: 8mm;
            }
        }
    </style>
</head>
<body class="p-2 sm:p-4">

    <!-- Top Action Bar -->
    <div class="no-print max-w-4xl mx-auto mb-4 flex items-center justify-between p-3 bg-white rounded-xl border border-gray-200 shadow-xs">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-[#ECFDF3] text-[#027A48]">
                Gate Pass &amp; Delivery Receipt
            </span>
            <span class="text-xs text-gray-500 font-mono">Job #{{ $repairJob->job_number }}</span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-[#111111] hover:bg-black text-white text-xs font-bold rounded-lg flex items-center gap-1.5 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Print Gate Pass</span>
            </button>
            <button onclick="window.close()" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition cursor-pointer">
                Close
            </button>
        </div>
    </div>

    <!-- Printable Delivery Container -->
    <div class="print-page bg-white space-y-6 text-[#111111]">

        <!-- Header -->
        <div class="flex justify-between items-start border-b-2 border-[#111111] pb-4">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-[#111111] uppercase">{{ $tenant->name ?? 'Mobile Service Centre' }}</h1>
                <p class="text-xs text-[#555555] mt-0.5">{{ $tenant->address ?? 'Smartphone Sales & Repair Workshop' }}</p>
                <div class="text-xs text-[#555555] mt-1 space-x-3">
                    @if($tenant->phone)
                        <span>Phone: <strong class="text-[#111111]">{{ $tenant->phone }}</strong></span>
                    @endif
                    @if($tenant->gst_number)
                        <span class="mono">GSTIN: <strong class="text-[#111111]">{{ $tenant->gst_number }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="text-right">
                <span class="inline-block px-3 py-1 bg-[#027A48] text-white text-xs font-black tracking-wider uppercase rounded">
                    DEVICE DELIVERY RECEIPT
                </span>
                <div class="text-xl font-bold font-mono text-[#111111] mt-2">
                    #{{ $repairJob->job_number }}
                </div>
                <div class="text-[11px] text-[#666666] mt-0.5">
                    Delivered: {{ $repairJob->delivered_at ? \Carbon\Carbon::parse($repairJob->delivered_at)->format('d/m/Y, h:i A') : now()->format('d/m/Y, h:i A') }}
                </div>
            </div>
        </div>

        <!-- Customer & Device Overview Grid -->
        <div class="grid grid-cols-2 gap-4 text-xs">
            <div class="p-3 bg-neutral-50 rounded border border-neutral-200 space-y-1">
                <span class="text-[10px] font-black uppercase tracking-wider text-[#666666]">Delivered To</span>
                <div class="font-bold text-sm text-[#111111]">{{ $repairJob->customer->name ?? 'Walk-in Customer' }}</div>
                @if($repairJob->customer)
                    <div class="mono font-semibold">{{ $repairJob->customer->phone }}</div>
                    @if($repairJob->customer->address)
                        <div class="text-[#666666]">{{ $repairJob->customer->address }}</div>
                    @endif
                @endif
            </div>

            <div class="p-3 bg-neutral-50 rounded border border-neutral-200 space-y-1">
                <span class="text-[10px] font-black uppercase tracking-wider text-[#666666]">Device Details</span>
                <div class="font-bold text-sm text-[#111111]">{{ $repairJob->device_brand }} {{ $repairJob->device_model }}</div>
                <div class="mono font-semibold">
                    @if($repairJob->imei)
                        IMEI: {{ $repairJob->imei }}
                    @elseif($repairJob->serial_number)
                        S/N: {{ $repairJob->serial_number }}
                    @endif
                </div>
                <div class="text-[#666666]">Color: {{ $repairJob->device_color ?: 'N/A' }}</div>
            </div>
        </div>

        <!-- Work Summary & Spare Parts Replaced Table -->
        <div class="border border-neutral-200 rounded overflow-hidden text-xs">
            <div class="bg-neutral-100 px-3 py-2 font-bold uppercase tracking-wider text-[11px] text-[#333333]">
                Work Carried Out &amp; Replacement Components
            </div>

            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-neutral-200 bg-neutral-50 text-[11px] text-[#666666] font-semibold">
                        <th class="py-2 px-3">Description / Part Replaced</th>
                        <th class="py-2 px-3 text-center">Qty</th>
                        <th class="py-2 px-3 text-right">Unit Price</th>
                        <th class="py-2 px-3 text-right">Amount (₹)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200">
                    @forelse($repairJob->items as $item)
                        <tr>
                            <td class="py-2 px-3">
                                <span class="font-bold text-[#111111]">{{ $item->item_name }}</span>
                                @if($item->sparePart && $item->sparePart->sku)
                                    <span class="text-[10px] text-[#888888] font-mono ml-1">({{ $item->sparePart->sku }})</span>
                                @endif
                            </td>
                            <td class="py-2 px-3 text-center font-bold">{{ $item->quantity }}</td>
                            <td class="py-2 px-3 text-right font-mono">₹{{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-2 px-3 text-right font-mono font-bold">₹{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-3 px-3 text-center text-[#888888]">Service and bench diagnostics</td>
                        </tr>
                    @endforelse
                    @if($repairJob->labour_charge > 0)
                        <tr>
                            <td class="py-2 px-3 font-medium text-[#111111]">Technician Labour / Bench Service Charge</td>
                            <td class="py-2 px-3 text-center font-bold">1</td>
                            <td class="py-2 px-3 text-right font-mono">₹{{ number_format($repairJob->labour_charge, 2) }}</td>
                            <td class="py-2 px-3 text-right font-mono font-bold">₹{{ number_format($repairJob->labour_charge, 2) }}</td>
                        </tr>
                    @endif
                    @if($repairJob->additional_charges > 0)
                        <tr>
                            <td class="py-2 px-3 font-medium text-[#111111]">Diagnostic &amp; Cleaning Charges</td>
                            <td class="py-2 px-3 text-center font-bold">1</td>
                            <td class="py-2 px-3 text-right font-mono">₹{{ number_format($repairJob->additional_charges, 2) }}</td>
                            <td class="py-2 px-3 text-right font-mono font-bold">₹{{ number_format($repairJob->additional_charges, 2) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Financial Summary -->
        <div class="flex justify-end text-xs">
            <div class="w-64 space-y-1.5 p-3 bg-neutral-50 rounded border border-neutral-200">
                <div class="flex justify-between">
                    <span class="text-[#666666]">Parts &amp; Service:</span>
                    <span class="font-mono">₹{{ number_format($repairJob->parts_total + $repairJob->labour_charge + $repairJob->additional_charges, 2) }}</span>
                </div>
                @if($repairJob->discount > 0)
                    <div class="flex justify-between text-[#027A48]">
                        <span>Discount:</span>
                        <span class="font-mono">-₹{{ number_format($repairJob->discount, 2) }}</span>
                    </div>
                @endif
                @if($repairJob->tax_amount > 0)
                    <div class="flex justify-between">
                        <span class="text-[#666666]">Tax / GST:</span>
                        <span class="font-mono">₹{{ number_format($repairJob->tax_amount, 2) }}</span>
                    </div>
                @endif
                <div class="pt-1.5 border-t border-neutral-300 flex justify-between font-bold text-sm">
                    <span>Total Amount:</span>
                    <span class="font-mono">₹{{ number_format($repairJob->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between font-semibold text-[#027A48]">
                    <span>Total Paid:</span>
                    <span class="font-mono">₹{{ number_format($repairJob->amount_paid, 2) }}</span>
                </div>
                <div class="flex justify-between font-bold">
                    <span>Balance Due:</span>
                    <span class="font-mono {{ $repairJob->due_balance > 0 ? 'text-red-700' : 'text-[#027A48]' }}">
                        ₹{{ number_format($repairJob->due_balance, 2) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Warranty Coverage Notice -->
        @if($repairJob->warranty_days > 0)
            <div class="p-3 bg-blue-50 border border-blue-200 rounded text-xs text-blue-950 flex items-center justify-between">
                <div>
                    <strong>Service Warranty:</strong> This repair includes <strong>{{ $repairJob->warranty_days }} days warranty</strong> covering parts replaced and service performed.
                </div>
                <div class="text-[11px] font-mono text-blue-800 font-semibold whitespace-nowrap">
                    Valid until: {{ now()->addDays($repairJob->warranty_days)->format('d M Y') }}
                </div>
            </div>
        @endif

        <!-- Customer Acknowledgment Statement -->
        <div class="p-3 bg-neutral-50 rounded border border-neutral-200 text-xs text-[#333333] space-y-1">
            <strong>Customer Handover Declaration:</strong>
            <p class="text-[11px] text-[#555555] leading-relaxed">
                I hereby acknowledge receipt of my repaired device in good working order. I have tested all key functions (display, touch, camera, speakers, charging, calling) and verified the return of all accessories left at intake.
            </p>
        </div>

        <!-- Signatures -->
        <div class="pt-8 grid grid-cols-2 gap-12 text-xs">
            <div class="text-center pt-8 border-t border-neutral-300">
                <span class="font-bold text-[#111111] block">Customer Signature</span>
                <span class="text-[10px] text-[#666666]">Received in satisfactory condition</span>
            </div>
            <div class="text-center pt-8 border-t border-neutral-300">
                <span class="font-bold text-[#111111] block">Store / Technician Signature</span>
                <span class="text-[10px] text-[#666666]">{{ $repairJob->technician->name ?? ($tenant->name ?? 'Service Desk') }}</span>
            </div>
        </div>

    </div>

</body>
</html>

