<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Job Sheet #{{ $repairJob->job_number }} · {{ $tenant->name ?? 'Workshop' }}</title>

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

    <!-- Top Floating Actions Bar -->
    <div class="no-print max-w-4xl mx-auto mb-4 flex items-center justify-between p-3 bg-white rounded-xl border border-gray-200 shadow-xs">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-[#E0F2FE] text-[#0369A1]">
                Workshop Job Sheet
            </span>
            <span class="text-xs text-gray-500 font-mono">Job #{{ $repairJob->job_number }}</span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-[#111111] hover:bg-black text-white text-xs font-bold rounded-lg flex items-center gap-1.5 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Print Document</span>
            </button>
            <button onclick="window.close()" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition cursor-pointer">
                Close
            </button>
        </div>
    </div>

    <!-- Printable Job Sheet Container -->
    <div class="print-page bg-white space-y-6 text-[#111111]">

        <!-- Header: Store Info & Document Title -->
        <div class="flex justify-between items-start border-b-2 border-[#111111] pb-4">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-[#111111] uppercase">{{ $tenant->name ?? 'Mobile Service Centre' }}</h1>
                <p class="text-xs text-[#555555] mt-0.5">{{ $tenant->address ?? 'Smartphone Sales & Repair Workshop' }}</p>
                <div class="text-xs text-[#555555] mt-1 space-x-3">
                    @if($tenant->phone)
                        <span>Phone: <strong class="text-[#111111]">{{ $tenant->phone }}</strong></span>
                    @endif
                    @if($tenant->email)
                        <span>Email: {{ $tenant->email }}</span>
                    @endif
                    @if($tenant->gst_number)
                        <span class="mono">GSTIN: <strong class="text-[#111111]">{{ $tenant->gst_number }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="text-right">
                <span class="inline-block px-3 py-1 bg-[#111111] text-white text-xs font-black tracking-wider uppercase rounded">
                    DEVICE INTAKE JOB SHEET
                </span>
                <div class="text-xl font-bold font-mono text-[#111111] mt-2">
                    #{{ $repairJob->job_number }}
                </div>
                <div class="text-[11px] text-[#666666] mt-0.5">
                    Intake: {{ $repairJob->created_at->format('d/m/Y, h:i A') }}
                </div>
            </div>
        </div>

        <!-- Customer & Device Metadata Grid -->
        <div class="grid grid-cols-2 gap-4 text-xs">
            <!-- Customer Box -->
            <div class="p-3 bg-neutral-50 rounded border border-neutral-200 space-y-1">
                <span class="text-[10px] font-black uppercase tracking-wider text-[#666666]">Customer Information</span>
                <div class="font-bold text-sm text-[#111111]">{{ $repairJob->customer->name ?? 'Walk-in Customer' }}</div>
                @if($repairJob->customer)
                    <div class="mono font-semibold">{{ $repairJob->customer->phone }}</div>
                    @if($repairJob->customer->email)
                        <div>{{ $repairJob->customer->email }}</div>
                    @endif
                    @if($repairJob->customer->address)
                        <div class="text-[#666666]">{{ $repairJob->customer->address }}</div>
                    @endif
                @endif
            </div>

            <!-- Job Spec Box -->
            <div class="p-3 bg-neutral-50 rounded border border-neutral-200 space-y-1">
                <span class="text-[10px] font-black uppercase tracking-wider text-[#666666]">Service &amp; Bench Assignment</span>
                <div class="flex justify-between">
                    <span class="text-[#666666]">Technician:</span>
                    <span class="font-bold">{{ $repairJob->technician->name ?? 'Unassigned (General Pool)' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#666666]">Priority:</span>
                    <span class="font-bold uppercase {{ $repairJob->priority === 'urgent' ? 'text-red-700' : '' }}">{{ $repairJob->priority }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#666666]">Expected Delivery:</span>
                    <span class="font-bold mono">{{ $repairJob->expected_completion_date ? \Carbon\Carbon::parse($repairJob->expected_completion_date)->format('d/m/Y') : 'To be confirmed' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#666666]">Warranty:</span>
                    <span class="font-bold">{{ $repairJob->warranty_days }} days post-delivery</span>
                </div>
            </div>
        </div>

        <!-- Device Technical Identification -->
        <div class="border border-[#111111] rounded overflow-hidden text-xs">
            <div class="bg-[#111111] text-white px-3 py-1.5 font-bold uppercase tracking-wider text-[11px] flex justify-between">
                <span>Device Intake Specifications</span>
                <span class="font-mono">{{ $repairJob->device_brand }}</span>
            </div>
            <div class="p-3 grid grid-cols-2 sm:grid-cols-4 gap-3 bg-white">
                <div>
                    <span class="text-[#666666] block text-[10px] uppercase">Device Brand &amp; Model</span>
                    <span class="font-bold text-sm">{{ $repairJob->device_brand }} {{ $repairJob->device_model }}</span>
                </div>
                <div>
                    <span class="text-[#666666] block text-[10px] uppercase">Colour</span>
                    <span class="font-medium">{{ $repairJob->device_color ?: 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-[#666666] block text-[10px] uppercase">Passcode / Pattern</span>
                    <span class="font-mono font-bold">{{ $repairJob->device_password ?: 'None' }}</span>
                </div>
                <div>
                    <span class="text-[#666666] block text-[10px] uppercase">IMEI / S/N</span>
                    <span class="mono font-semibold">{{ $repairJob->imei ?: ($repairJob->serial_number ?: 'Not provided') }}</span>
                </div>
            </div>
        </div>

        <!-- Issue & Physical Condition -->
        <div class="grid grid-cols-2 gap-4 text-xs">
            <div class="p-3 border border-neutral-300 rounded space-y-1">
                <span class="text-[10px] font-bold text-red-700 uppercase tracking-wider">Reported Problem / Symptoms</span>
                <p class="text-[#111111] font-medium leading-relaxed">{{ $repairJob->reported_problem }}</p>
            </div>

            <div class="p-3 border border-neutral-300 rounded space-y-1">
                <span class="text-[10px] font-bold text-[#666666] uppercase tracking-wider">Physical Condition at Reception</span>
                <p class="text-[#111111] leading-relaxed">{{ $repairJob->device_condition ?: 'No pre-existing damages noted.' }}</p>
                @if($repairJob->accessories_received)
                    <div class="pt-1 text-[11px] text-[#666666] border-t border-neutral-200 mt-1">
                        <strong>Accessories Left:</strong> {{ $repairJob->accessories_received }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Cost Estimate & Advance Deposit -->
        <div class="p-4 bg-neutral-50 border border-neutral-200 rounded text-xs">
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="border-r border-neutral-300">
                    <span class="text-[10px] uppercase text-[#666666] block">Estimated Total Cost</span>
                    <span class="text-base font-bold mono">₹{{ number_format($repairJob->estimated_cost, 2) }}</span>
                </div>
                <div class="border-r border-neutral-300">
                    <span class="text-[10px] uppercase text-[#666666] block">Advance Deposit Paid</span>
                    <span class="text-base font-bold mono text-[#079455]">₹{{ number_format($repairJob->amount_paid, 2) }}</span>
                </div>
                <div>
                    <span class="text-[10px] uppercase text-[#666666] block">Approx. Balance Due</span>
                    <span class="text-base font-bold mono text-[#DC2626]">
                        ₹{{ number_format(max(0, $repairJob->estimated_cost - $repairJob->amount_paid), 2) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Terms & Conditions Disclaimer -->
        <div class="border border-neutral-200 rounded p-3 text-[10px] text-[#555555] space-y-1 leading-relaxed bg-white">
            <span class="font-bold uppercase text-[#111111] block mb-1">Workshop Terms &amp; Conditions:</span>
            <ol class="list-decimal list-inside space-y-0.5">
                <li>Customers must present this original Job Sheet at the time of collecting the device.</li>
                <li>The service centre is not responsible for any loss of user data or memory content. Customers are strictly advised to keep an independent backup prior to submission.</li>
                <li>Estimated cost is subject to final hardware diagnosis. In case of unexpected component failures, customer approval will be sought before proceeding.</li>
                <li>Devices uncollected within 30 days of completion notification may incur storage fees or be disposed of to recover service expenses.</li>
                <li>Service warranty applies only to the specific parts replaced and labour performed as itemized in the final invoice.</li>
            </ol>
        </div>

        <!-- Customer & Workshop Signatures -->
        <div class="pt-6 grid grid-cols-2 gap-12 text-xs">
            <div class="text-center pt-8 border-t border-neutral-300">
                <span class="font-bold text-[#111111] block">Customer Signature</span>
                <span class="text-[10px] text-[#666666]">(Acknowledges device condition &amp; terms above)</span>
            </div>
            <div class="text-center pt-8 border-t border-neutral-300">
                <span class="font-bold text-[#111111] block">Authorized Signature</span>
                <span class="text-[10px] text-[#666666]">{{ $tenant->name ?? 'Workshop Desk' }}</span>
            </div>
        </div>

    </div>

</body>
</html>

