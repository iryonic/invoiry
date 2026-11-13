<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $settings['document_title'] ?? 'INVOIRY Features & Pricing' }}</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 12mm 14mm 14mm 14mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.45;
            color: #1a1a1a;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        .page {
            page-break-after: always;
            position: relative;
            min-height: 270mm;
        }

        .page:last-child {
            page-break-after: avoid;
        }

        .avoid-break {
            page-break-inside: avoid;
        }

        /* Typography */
        h1, h2, h3, h4, h5, p {
            margin: 0 0 6px 0;
        }

        .text-mono {
            font-family: 'Courier New', Courier, monospace;
        }

        .text-bold {
            font-weight: bold;
        }

        .text-muted {
            color: #666666;
        }

        .text-white {
            color: #ffffff;
        }

        .text-lime {
            color: #B7FF00;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* Tables for Layout */
        table.layout {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin: 0;
            padding: 0;
        }

        table.layout td {
            vertical-align: top;
            padding: 0;
        }

        /* Section Headings */
        .section-header {
            border-bottom: 2px solid #111111;
            padding-bottom: 5px;
            margin-bottom: 12px;
        }

        .section-tag {
            font-size: 7pt;
            font-weight: bold;
            color: #079455;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .section-title {
            font-size: 15pt;
            font-weight: bold;
            color: #111111;
            letter-spacing: -0.3px;
        }

        .section-desc {
            font-size: 8.5pt;
            color: #555555;
            margin-top: 2px;
        }

        /* Cards & Boxes */
        .card {
            background-color: #F8F9FA;
            border: 1px solid #E6E6E6;
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 10px;
        }

        .card-dark {
            background-color: #111111;
            color: #ffffff;
            border-radius: 6px;
            padding: 12px 14px;
            margin-bottom: 10px;
        }

        .badge {
            display: inline-block;
            font-size: 6.5pt;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-dark {
            background-color: #111111;
            color: #B7FF00;
        }

        .badge-lime {
            background-color: #B7FF00;
            color: #111111;
        }

        .badge-gray {
            background-color: #EFEFEF;
            color: #333333;
        }

        /* Data Tables */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-top: 8px;
        }

        table.data-table th {
            background-color: #111111;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 6px 8px;
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table.data-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #EAEAEA;
        }

        table.data-table tr:nth-child(even) td {
            background-color: #F9FAFB;
        }

        /* Page Footer */
        .doc-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #E6E6E6;
            padding-top: 6px;
            font-size: 7pt;
            color: #888888;
        }

        /* Checklist */
        .check-item {
            margin-bottom: 3px;
            font-size: 8pt;
            color: #333333;
        }

        .check-mark {
            color: #079455;
            font-weight: bold;
            margin-right: 4px;
        }
    </style>
</head>
<body>

    {{-- =========================================================================
         PAGE 1: COVER PAGE
         ========================================================================= --}}
    @if($settings['show_cover'] ?? true)
    <div class="page" style="background-color: #FFFFFF;">
        <!-- Top Obsidian Brand Banner -->
        <div style="background-color: #111111; color: #ffffff; padding: 26px 24px; border-radius: 8px; margin-bottom: 18px;">
            <table class="layout">
                <tr>
                    <td>
                        <span class="badge badge-lime" style="margin-bottom: 8px; display: inline-block;">
                            OFFICIAL PRODUCT SPECIFICATION BROCHURE
                        </span>
                        <h1 style="font-size: 32pt; font-weight: bold; color: #FFFFFF; letter-spacing: -1px; line-height: 1.1; margin: 6px 0 4px 0;">
                            {{ $settings['product_name'] ?? 'INVOIRY' }}
                        </h1>
                        <p style="font-size: 11pt; color: #B7FF00; font-weight: bold; margin: 0 0 10px 0;">
                            {{ $settings['product_tagline'] ?? 'Enterprise POS, Hardware Tracking & Fiscal Ledger Platform' }}
                        </p>
                    </td>
                    <td style="width: 130px; text-align: right; vertical-align: top;">
                        <div style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 8px; text-align: center;">
                            <span style="font-size: 7pt; color: #AAAAAA; text-transform: uppercase; letter-spacing: 1px;">Release Ver.</span>
                            <div style="font-size: 13pt; font-weight: bold; color: #B7FF00; font-family: monospace;">v{{ $settings['version'] ?? '2.4.0' }}</div>
                            <span style="font-size: 6.5pt; color: #888888;">2026 EDITION</span>
                        </div>
                    </td>
                </tr>
            </table>

            <div style="border-top: 1px solid rgba(255,255,255,0.12); margin-top: 14px; padding-top: 12px;">
                <p style="font-size: 9pt; color: #D1D5DB; line-height: 1.5; margin: 0;">
                    {{ $settings['hero_subheadline'] ?? 'Seamless point-of-sale billing, dual-SIM IMEI lifecycle governance, automated GST reconciliation, thermal receipt output, and real-time customer credit ledgers — all in one unified, multi-tenant cloud workspace.' }}
                </p>
            </div>
        </div>

        <!-- Hero Headline & Core Differentiator Grid -->
        <div style="margin-bottom: 16px;">
            <h2 style="font-size: 14pt; font-weight: bold; color: #111111; line-height: 1.35; margin-bottom: 8px;">
                {{ $settings['hero_headline'] ?? 'Next-Generation Retail POS Built for Electronics, Telecom & Hardware Enterprises' }}
            </h2>
            <p style="font-size: 8.5pt; color: #555555; line-height: 1.45; margin-bottom: 14px;">
                Engineered from the ground up to solve the operational bottlenecks of retail counters: rapid camera & barcode checkout, statutory telecom IMEI tracking, automatic split GST math, instant thermal slip printing, and multi-tenant chain governance.
            </p>

            <!-- 4 Pillar Feature Cards (2x2 table) -->
            <table class="layout" style="margin-bottom: 14px;">
                <tr>
                    <td style="width: 48.5%; padding-right: 1.5%;">
                        <div class="card" style="border-left: 3px solid #111111; min-height: 60px;">
                            <span class="badge badge-dark">POINT OF SALE</span>
                            <h4 style="font-size: 9.5pt; font-weight: bold; margin: 4px 0 2px 0;">Rapid POS Billing & Barcode</h4>
                            <p style="font-size: 7.5pt; color: #555555; margin: 0;">Zero-lag barcode & camera scanning, item discounts, and dynamic UPI QR generation.</p>
                        </div>
                    </td>
                    <td style="width: 48.5%; padding-left: 1.5%;">
                        <div class="card" style="border-left: 3px solid #079455; min-height: 60px;">
                            <span class="badge" style="background: #ECFDF3; color: #027A48;">HARDWARE COMPLIANCE</span>
                            <h4 style="font-size: 9.5pt; font-weight: bold; margin: 4px 0 2px 0;">Dual IMEI & Serial Governance</h4>
                            <p style="font-size: 7.5pt; color: #555555; margin: 0;">Mandatory Primary & Secondary IMEI logging to prevent stolen hardware liability.</p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 48.5%; padding-right: 1.5%; padding-top: 8px;">
                        <div class="card" style="border-left: 3px solid #2563EB; min-height: 60px;">
                            <span class="badge" style="background: #EFF8FF; color: #175CD3;">TAX ENGINE</span>
                            <h4 style="font-size: 9.5pt; font-weight: bold; margin: 4px 0 2px 0;">Automated GST Split Math</h4>
                            <p style="font-size: 7.5pt; color: #555555; margin: 0;">1-click toggle between Inclusive & Exclusive tax with CGST/SGST and IGST rules.</p>
                        </div>
                    </td>
                    <td style="width: 48.5%; padding-left: 1.5%; padding-top: 8px;">
                        <div class="card" style="border-left: 3px solid #D97706; min-height: 60px;">
                            <span class="badge" style="background: #FEF0C7; color: #B54708;">CREDIT LEDGER</span>
                            <h4 style="font-size: 9.5pt; font-weight: bold; margin: 4px 0 2px 0;">Customer Khata Credit Engine</h4>
                            <p style="font-size: 7.5pt; color: #555555; margin: 0;">Partial down payment receipts, running balance tracking, and overdue reminders.</p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Featured Application Visual Preview -->
        @php
            $heroScreenshot = $screenshots->firstWhere('title', 'Point of Sale & Rapid Billing Checkout') ?? $screenshots->first();
        @endphp
        @if($heroScreenshot && $heroScreenshot->absolute_path)
            <div style="border: 1px solid #D1D5DB; border-radius: 6px; overflow: hidden; background: #000; margin-bottom: 12px;">
                <div style="background: #111111; color: #ffffff; padding: 4px 10px; font-size: 7pt; font-weight: bold;">
                    LIVE PRODUCTION INTERFACE PREVIEW — {{ $heroScreenshot->title }}
                </div>
                <img src="{{ $heroScreenshot->absolute_path }}" style="width: 100%; height: auto; max-height: 180px; display: block;" alt="INVOIRY POS Screenshot">
            </div>
        @endif

        <!-- Cover Footer Contact Strip -->
        <div class="doc-footer">
            <table class="layout">
                <tr>
                    <td style="width: 60%;">
                        <strong>{{ $settings['product_name'] ?? 'INVOIRY' }} Technologies</strong> &bull;
                        Direct Inquiries: {{ $settings['contact_phone_1'] ?? '6006801960' }} / {{ $settings['contact_phone_2'] ?? '6005958161' }}
                    </td>
                    <td style="width: 40%; text-align: right;">
                        {{ $settings['contact_email'] ?? 'hire.iry@gmail.com' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
    @endif


    {{-- =========================================================================
         PAGE 2: PRODUCT OVERVIEW & VALUE PROPOSITION
         ========================================================================= --}}
    @if($settings['show_overview'] ?? true)
    <div class="page">
        <div class="section-header">
            <span class="section-tag">01 / EXECUTIVE OVERVIEW</span>
            <h2 class="section-title">Product Capabilities & Commercial Value</h2>
            <p class="section-desc">Why modern electronics and hardware retailers trust INVOIRY for daily counter billing and fiscal compliance.</p>
        </div>

        <!-- What It Does Box -->
        <div class="card" style="background-color: #FAFAFA; border-left: 3px solid #111111; margin-bottom: 12px;">
            <h3 style="font-size: 10pt; font-weight: bold; margin-bottom: 4px;">What INVOIRY Does</h3>
            <p style="font-size: 8pt; line-height: 1.45; color: #333333; margin: 0;">
                {{ $settings['overview_what_it_does'] ?? 'INVOIRY is a high-performance, multi-tenant Point of Sale (POS) and inventory ERP system engineered specifically for electronics retailers, smartphone dealerships, computer hardware stores, and multi-outlet merchant chains.' }}
            </p>
        </div>

        <!-- Two Columns: Target Businesses & Problems Solved -->
        <table class="layout" style="margin-bottom: 12px;">
            <tr>
                <td style="width: 48.5%; padding-right: 1.5%;">
                    <div class="card" style="min-height: 160px;">
                        <h4 style="font-size: 9pt; font-weight: bold; color: #111111; border-bottom: 1px solid #E6E6E6; padding-bottom: 4px; margin-bottom: 6px;">
                            Target Retailers & Businesses
                        </h4>
                        <div style="font-size: 7.5pt; color: #444444; line-height: 1.4;">
                            <p style="margin-bottom: 4px;">• <strong>Smartphone & Telecom Dealers:</strong> Inward multi-brand handsets with Dual IMEI.</p>
                            <p style="margin-bottom: 4px;">• <strong>Computer & IT Hardware Outlets:</strong> Track unique serial numbers on laptops & parts.</p>
                            <p style="margin-bottom: 4px;">• <strong>Consumer Electronics Showrooms:</strong> High-ticket warranties and statutory GST bills.</p>
                            <p style="margin-bottom: 4px;">• <strong>Multi-Store Franchise Chains:</strong> Centralized inventory with independent cashiers.</p>
                            <p style="margin-bottom: 0;">• <strong>Electronics Repair & Resellers:</strong> Record device inwarding condition grading.</p>
                        </div>
                    </div>
                </td>
                <td style="width: 48.5%; padding-left: 1.5%;">
                    <div class="card" style="min-height: 160px; background-color: #FFFDF5; border-color: #FDE68A;">
                        <h4 style="font-size: 9pt; font-weight: bold; color: #92400E; border-bottom: 1px solid #FDE68A; padding-bottom: 4px; margin-bottom: 6px;">
                            Critical Retail Problems Solved
                        </h4>
                        <div style="font-size: 7.5pt; color: #78350F; line-height: 1.4;">
                            @php
                                $problemLines = explode("\n", $settings['overview_problems_solved'] ?? '');
                            @endphp
                            @foreach($problemLines as $line)
                                @if(trim($line))
                                    <p style="margin-bottom: 4px;">{{ trim($line) }}</p>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- 6 Core Differentiating Pillars -->
        <div style="margin-bottom: 12px;">
            <h3 style="font-size: 9.5pt; font-weight: bold; color: #111111; margin-bottom: 6px;">
                Key Operational Differentiators
            </h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Capability</th>
                        <th style="width: 45%;">Commercial Impact</th>
                        <th style="width: 30%;">Store Benefit</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Zero-Friction POS</strong></td>
                        <td>Instant camera & handheld barcode scanning with live inventory checks.</td>
                        <td>Reduces counter queue time by 75%.</td>
                    </tr>
                    <tr>
                        <td><strong>Dual-SIM IMEI Tracking</strong></td>
                        <td>Every inwarded handset binds IMEI-1 & IMEI-2 to the final customer invoice.</td>
                        <td>Eliminates stolen device disputes.</td>
                    </tr>
                    <tr>
                        <td><strong>Dynamic Tax Split Engine</strong></td>
                        <td>Auto-splits CGST/SGST for intra-state and IGST for inter-state sales.</td>
                        <td>100% audit-proof tax filing.</td>
                    </tr>
                    <tr>
                        <td><strong>Dual Print Output</strong></td>
                        <td>High-speed 58mm/80mm thermal receipts + formal A4 Tax Invoices.</td>
                        <td>Zero driver configuration lag.</td>
                    </tr>
                    <tr>
                        <td><strong>Customer Khata Ledger</strong></td>
                        <td>Records partial payments, down payments, and pending dues with due dates.</td>
                        <td>Plugs cash-flow revenue leaks.</td>
                    </tr>
                    <tr>
                        <td><strong>Terminal /lock Security</strong></td>
                        <td>Emergency counter lock masks sensitive data when cashier steps away.</td>
                        <td>Protects retail store margin secrecy.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="doc-footer">
            <table class="layout">
                <tr>
                    <td style="width: 50%;">INVOIRY Commercial Brochure &bull; Section 01</td>
                    <td style="width: 50%; text-align: right;">Sales Desk: 6006801960 / 6005958161</td>
                </tr>
            </table>
        </div>
    </div>
    @endif


    {{-- =========================================================================
         PAGE 3: SOFTWARE MODULES & ARCHITECTURE (PART 1)
         ========================================================================= --}}
    @if($settings['show_modules'] ?? true)
    <div class="page">
        <div class="section-header">
            <span class="section-tag">02 / MODULAR ARCHITECTURE (PART 1)</span>
            <h2 class="section-title">Core Counter Operations & Compliance</h2>
            <p class="section-desc">Deep technical capabilities built specifically for hardware retail workflows.</p>
        </div>

        @php
            $part1Modules = $modules->take(3);
        @endphp

        @foreach($part1Modules as $index => $mod)
            <div class="card avoid-break" style="margin-bottom: 12px; border-left: 3px solid #111111;">
                <table class="layout">
                    <tr>
                        <td>
                            @if($mod->badge)
                                <span class="badge badge-dark" style="margin-bottom: 3px;">{{ $mod->badge }}</span>
                            @endif
                            <h3 style="font-size: 10.5pt; font-weight: bold; color: #111111; margin: 2px 0 2px 0;">
                                {{ $index + 1 }}. {{ $mod->title }}
                            </h3>
                            @if($mod->subtitle)
                                <div style="font-size: 7.5pt; color: #079455; font-weight: bold; margin-bottom: 4px;">
                                    {{ $mod->subtitle }}
                                </div>
                            @endif
                            <p style="font-size: 8pt; color: #444444; line-height: 1.4; margin-bottom: 6px;">
                                {{ $mod->description }}
                            </p>

                            <!-- Feature Bullet Points in 2 Columns -->
                            @if(!empty($mod->features))
                                <table class="layout" style="margin-top: 4px;">
                                    <tr>
                                        <td style="width: 50%; padding-right: 6px;">
                                            @foreach(array_slice($mod->features, 0, ceil(count($mod->features) / 2)) as $feat)
                                                <div class="check-item"><span class="check-mark">&#10003;</span> {{ $feat }}</div>
                                            @endforeach
                                        </td>
                                        <td style="width: 50%; padding-left: 6px;">
                                            @foreach(array_slice($mod->features, ceil(count($mod->features) / 2)) as $feat)
                                                <div class="check-item"><span class="check-mark">&#10003;</span> {{ $feat }}</div>
                                            @endforeach
                                        </td>
                                    </tr>
                                </table>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        @endforeach

        <div class="doc-footer">
            <table class="layout">
                <tr>
                    <td style="width: 50%;">INVOIRY Technical Specifications &bull; Section 02-A</td>
                    <td style="width: 50%; text-align: right;">Official Enterprise Architecture</td>
                </tr>
            </table>
        </div>
    </div>


    {{-- =========================================================================
         PAGE 4: SOFTWARE MODULES & ARCHITECTURE (PART 2)
         ========================================================================= --}}
    <div class="page">
        <div class="section-header">
            <span class="section-tag">02 / MODULAR ARCHITECTURE (PART 2)</span>
            <h2 class="section-title">Ledger, Print Engine & Multi-Tenancy</h2>
            <p class="section-desc">Empowering multi-store merchants with centralized governance and cash-flow control.</p>
        </div>

        @php
            $part2Modules = $modules->slice(3);
        @endphp

        @foreach($part2Modules as $index => $mod)
            <div class="card avoid-break" style="margin-bottom: 12px; border-left: 3px solid #111111;">
                <table class="layout">
                    <tr>
                        <td>
                            @if($mod->badge)
                                <span class="badge badge-dark" style="margin-bottom: 3px;">{{ $mod->badge }}</span>
                            @endif
                            <h3 style="font-size: 10.5pt; font-weight: bold; color: #111111; margin: 2px 0 2px 0;">
                                {{ $index + 4 }}. {{ $mod->title }}
                            </h3>
                            @if($mod->subtitle)
                                <div style="font-size: 7.5pt; color: #079455; font-weight: bold; margin-bottom: 4px;">
                                    {{ $mod->subtitle }}
                                </div>
                            @endif
                            <p style="font-size: 8pt; color: #444444; line-height: 1.4; margin-bottom: 6px;">
                                {{ $mod->description }}
                            </p>

                            <!-- Feature Bullet Points in 2 Columns -->
                            @if(!empty($mod->features))
                                <table class="layout" style="margin-top: 4px;">
                                    <tr>
                                        <td style="width: 50%; padding-right: 6px;">
                                            @foreach(array_slice($mod->features, 0, ceil(count($mod->features) / 2)) as $feat)
                                                <div class="check-item"><span class="check-mark">&#10003;</span> {{ $feat }}</div>
                                            @endforeach
                                        </td>
                                        <td style="width: 50%; padding-left: 6px;">
                                            @foreach(array_slice($mod->features, ceil(count($mod->features) / 2)) as $feat)
                                                <div class="check-item"><span class="check-mark">&#10003;</span> {{ $feat }}</div>
                                            @endforeach
                                        </td>
                                    </tr>
                                </table>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        @endforeach

        <div class="doc-footer">
            <table class="layout">
                <tr>
                    <td style="width: 50%;">INVOIRY Technical Specifications &bull; Section 02-B</td>
                    <td style="width: 50%; text-align: right;">Direct Inquiries: hire.iry@gmail.com</td>
                </tr>
            </table>
        </div>
    </div>
    @endif


    {{-- =========================================================================
         PAGE 5: REAL APPLICATION SCREENSHOTS SHOWCASE
         ========================================================================= --}}
    @if($settings['show_screenshots'] ?? true)
    <div class="page">
        <div class="section-header">
            <span class="section-tag">03 / APPLICATION SHOWCASE</span>
            <h2 class="section-title">Production Interface & User Workflows</h2>
            <p class="section-desc">Authentic screenshots captured directly from active INVOIRY production retail environments.</p>
        </div>

        @php
            $gallery = $screenshots->where('is_enabled', true)->take(4);
        @endphp

        <table class="layout">
            @foreach($gallery->chunk(2) as $row)
                <tr>
                    @foreach($row as $shot)
                        <td style="width: 48.5%; padding: 4px; padding-bottom: 12px; vertical-align: top;">
                            <div style="border: 1px solid #D1D5DB; border-radius: 6px; overflow: hidden; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                                <div style="background-color: #111111; color: #ffffff; padding: 5px 8px; font-size: 7.5pt; font-weight: bold; border-bottom: 1px solid #000;">
                                    {{ $shot->title }}
                                </div>
                                @if($shot->absolute_path)
                                    <div style="background: #F2F4F7; text-align: center; max-height: 145px; overflow: hidden;">
                                        <img src="{{ $shot->absolute_path }}" style="width: 100%; height: auto; max-height: 145px; display: block;" alt="{{ $shot->title }}">
                                    </div>
                                @endif
                                <div style="padding: 6px 8px; font-size: 7pt; color: #555555; line-height: 1.35; background: #FAFAFA; border-top: 1px solid #E6E6E6;">
                                    {{ $shot->caption }}
                                </div>
                            </div>
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </table>

        <!-- Security & Audit Guarantee Box -->
        <div class="card-dark" style="margin-top: 6px;">
            <table class="layout">
                <tr>
                    <td style="width: 80%;">
                        <span class="badge badge-lime" style="margin-bottom: 3px;">100% PRODUCTION ACCURACY</span>
                        <h4 style="font-size: 8.5pt; font-weight: bold; color: #ffffff; margin: 2px 0;">All interfaces displayed are real, responsive production screens.</h4>
                        <p style="font-size: 7pt; color: #D1D5DB; margin: 0;">Designed with high-contrast tactile feedback, JetBrains Mono tabular figures, and full keyboard-driven navigation.</p>
                    </td>
                    <td style="width: 20%; text-align: right; vertical-align: middle;">
                        <span style="font-size: 11pt; color: #B7FF00; font-weight: bold;">VERIFIED</span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="doc-footer">
            <table class="layout">
                <tr>
                    <td style="width: 50%;">INVOIRY Interface Showcase &bull; Section 03</td>
                    <td style="width: 50%; text-align: right;">Support Hotline: 6006801960 / 6005958161</td>
                </tr>
            </table>
        </div>
    </div>
    @endif


    {{-- =========================================================================
         PAGE 6: COMMERCIAL PRICING PLANS & COMPARISON
         ========================================================================= --}}
    @if($settings['show_pricing'] ?? true)
    <div class="page">
        <div class="section-header">
            <span class="section-tag">04 / COMMERCIAL PLANS</span>
            <h2 class="section-title">Subscription Tiers & Commercial Pricing</h2>
            <p class="section-desc">Transparent, predictable pricing designed to scale effortlessly from a single store to national chains.</p>
        </div>

        <!-- 3 Pricing Cards -->
        <table class="layout" style="margin-bottom: 14px;">
            <tr>
                @foreach($plans as $plan)
                    <td style="width: 32%; padding: 0 4px; vertical-align: top;">
                        <div class="card avoid-break" style="min-height: 250px; {{ $plan->is_popular ? 'border: 2px solid #111111; background-color: #FFFFFF;' : 'border: 1px solid #E6E6E6;' }}">
                            @if($plan->badge)
                                <div style="text-align: center; margin-bottom: 4px;">
                                    <span class="badge {{ $plan->is_popular ? 'badge-lime' : 'badge-dark' }}">
                                        {{ $plan->badge }}
                                    </span>
                                </div>
                            @endif

                            <div style="text-align: center; border-bottom: 1px solid #EEEEEE; padding-bottom: 6px; margin-bottom: 6px;">
                                <h3 style="font-size: 10.5pt; font-weight: bold; color: #111111; margin: 0 0 2px 0;">{{ $plan->name }}</h3>
                                <div style="font-size: 15pt; font-weight: bold; color: {{ $plan->is_popular ? '#111111' : '#111111' }}; font-family: monospace;">
                                    {{ $plan->price }}
                                </div>
                                <div style="font-size: 6.5pt; color: #666666; font-weight: bold;">
                                    {{ $plan->period }}
                                </div>
                            </div>

                            <p style="font-size: 7pt; color: #555555; text-align: center; margin-bottom: 8px; min-height: 24px; line-height: 1.3;">
                                {{ $plan->description }}
                            </p>

                            <!-- Feature List -->
                            <div style="border-top: 1px solid #F0F0F0; padding-top: 6px;">
                                @if(!empty($plan->features))
                                    @foreach($plan->features as $feat)
                                        <div style="font-size: 6.8pt; color: #333333; margin-bottom: 3px; line-height: 1.25;">
                                            <span style="color: #079455; font-weight: bold;">&#10003;</span> {{ $feat }}
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            @if($plan->limitations)
                                <div style="margin-top: 6px; padding-top: 4px; border-top: 1px dashed #E6E6E6; font-size: 6pt; color: #888888; font-style: italic;">
                                    {{ $plan->limitations }}
                                </div>
                            @endif
                        </div>
                    </td>
                @endforeach
            </tr>
        </table>

        <!-- Comparison Table -->
        <div class="avoid-break">
            <h3 style="font-size: 9pt; font-weight: bold; color: #111111; margin-bottom: 4px;">Feature Comparison Matrix</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">Core Feature</th>
                        <th style="width: 20%; text-align: center;">Starter</th>
                        <th style="width: 20%; text-align: center;">Professional</th>
                        <th style="width: 20%; text-align: center;">Enterprise</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Physical Retail Outlets</td>
                        <td class="text-center">1 Store</td>
                        <td class="text-center">Up to 2 Stores</td>
                        <td class="text-center text-bold">Unlimited</td>
                    </tr>
                    <tr>
                        <td>Staff & Cashier Logins</td>
                        <td class="text-center">2 Users</td>
                        <td class="text-center text-bold">Unlimited</td>
                        <td class="text-center text-bold">Unlimited</td>
                    </tr>
                    <tr>
                        <td>Dual-SIM IMEI & Serial Number Tracking</td>
                        <td class="text-center"><span style="color: #079455;">&#10003;</span> Included</td>
                        <td class="text-center"><span style="color: #079455;">&#10003;</span> Included</td>
                        <td class="text-center"><span style="color: #079455;">&#10003;</span> Advanced Audit</td>
                    </tr>
                    <tr>
                        <td>ESC/POS Thermal Receipt Engine</td>
                        <td class="text-center">58mm Only</td>
                        <td class="text-center text-bold">58mm & 80mm</td>
                        <td class="text-center text-bold">58mm & 80mm</td>
                    </tr>
                    <tr>
                        <td>Customer Khata Credit & Partial Receipts</td>
                        <td class="text-center">500 Records</td>
                        <td class="text-center text-bold">Unlimited</td>
                        <td class="text-center text-bold">Unlimited + Aging</td>
                    </tr>
                    <tr>
                        <td>Super Admin Multi-Tenant Governance</td>
                        <td class="text-center text-muted">&mdash;</td>
                        <td class="text-center text-muted">&mdash;</td>
                        <td class="text-center"><span style="color: #079455;">&#10003;</span> Full Platform</td>
                    </tr>
                    <tr>
                        <td>Dedicated SLA & Priority Support</td>
                        <td class="text-center">Standard Email</td>
                        <td class="text-center">Phone & WhatsApp</td>
                        <td class="text-center text-bold">24/7 Dedicated Mgr</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="doc-footer">
            <table class="layout">
                <tr>
                    <td style="width: 50%;">INVOIRY Commercial Pricing &bull; Section 04</td>
                    <td style="width: 50%; text-align: right;">Toll-Free Helpline: 6006801960 / 6005958161</td>
                </tr>
            </table>
        </div>
    </div>
    @endif


    {{-- =========================================================================
         PAGE 7: TECHNICAL SPECIFICATIONS, SLA & CONTACT
         ========================================================================= --}}
    @if($settings['show_contact'] ?? true)
    <div class="page">
        <div class="section-header">
            <span class="section-tag">05 / HARDWARE COMPATIBILITY & CONTACT</span>
            <h2 class="section-title">Hardware Specifications, Onboarding & Contact</h2>
            <p class="section-desc">Certified printer hardware, enterprise onboarding roadmap, and direct implementation support.</p>
        </div>

        <!-- 2 Columns: Specifications & SLA -->
        <table class="layout" style="margin-bottom: 12px;">
            <tr>
                <td style="width: 48.5%; padding-right: 1.5%;">
                    <div class="card" style="min-height: 140px;">
                        <h4 style="font-size: 8.5pt; font-weight: bold; color: #111111; border-bottom: 1px solid #E6E6E6; padding-bottom: 3px; margin-bottom: 5px;">
                            Hardware & Device Compatibility
                        </h4>
                        <div style="font-size: 7.2pt; color: #444444; line-height: 1.4;">
                            <p style="margin-bottom: 3px;">• <strong>Thermal Printers:</strong> All 58mm & 80mm ESC/POS USB, LAN, and Bluetooth printers (Epson, TVS, NGX, Everycom, Retsol).</p>
                            <p style="margin-bottom: 3px;">• <strong>Barcode Scanners:</strong> Standard 1D/2D USB handheld guns, Bluetooth scanners, and integrated tablet cameras.</p>
                            <p style="margin-bottom: 3px;">• <strong>Operating Systems:</strong> Windows 10/11, macOS, Linux, Android POS tablets, and iPadOS.</p>
                            <p style="margin-bottom: 0;">• <strong>Browser Engines:</strong> Google Chrome, Microsoft Edge, Brave, and Safari.</p>
                        </div>
                    </div>
                </td>
                <td style="width: 48.5%; padding-left: 1.5%;">
                    <div class="card" style="min-height: 140px; background-color: #F0FDF4; border-color: #BBF7D0;">
                        <h4 style="font-size: 8.5pt; font-weight: bold; color: #166534; border-bottom: 1px solid #BBF7D0; padding-bottom: 3px; margin-bottom: 5px;">
                            Enterprise Onboarding & 99.9% SLA
                        </h4>
                        <div style="font-size: 7.2pt; color: #14532D; line-height: 1.4;">
                            <p style="margin-bottom: 3px;">• <strong>Step 1: Store Setup:</strong> Configure business name, GSTIN, UPI ID, logo & state code in 5 minutes.</p>
                            <p style="margin-bottom: 3px;">• <strong>Step 2: CSV Inventory Inwarding:</strong> Bulk import existing stock with IMEI-1 & IMEI-2.</p>
                            <p style="margin-bottom: 3px;">• <strong>Step 3: Cashier Role Assignment:</strong> Create staff accounts with granular permissions.</p>
                            <p style="margin-bottom: 0;">• <strong>Step 4: Go-Live:</strong> Live counter billing with automated daily cloud backups.</p>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Terms & Notes -->
        @if(!empty($settings['terms_notes']))
            <div class="card" style="background-color: #F8F9FA; font-size: 7pt; color: #666666; margin-bottom: 12px; line-height: 1.35;">
                <strong style="color: #111111;">Important Commercial & Fiscal Notes:</strong><br>
                {!! nl2br(e($settings['terms_notes'])) !!}
            </div>
        @endif

        <!-- Prominent Executive Contact Box -->
        <div class="card-dark" style="padding: 16px 20px; border: 2px solid #B7FF00; margin-top: 10px;">
            <table class="layout">
                <tr>
                    <td style="width: 60%; vertical-align: middle;">
                        <span class="badge badge-lime" style="margin-bottom: 4px;">GET STARTED TODAY</span>
                        <h3 style="font-size: 13pt; font-weight: bold; color: #FFFFFF; margin: 2px 0 4px 0;">
                            Ready to Transform Your Retail Store Operations?
                        </h3>
                        <p style="font-size: 7.8pt; color: #D1D5DB; margin: 0 0 8px 0; line-height: 1.4;">
                            Contact our implementation team directly to schedule a personalized live demo, receive custom multi-store volume pricing, or request on-site thermal printer setup assistance.
                        </p>
                        <div style="font-size: 7.2pt; color: #888888;">
                            Office: {{ $settings['contact_address'] ?? 'INVOIRY Technologies Hub, India' }}
                        </div>
                    </td>
                    <td style="width: 40%; vertical-align: middle; text-align: right;">
                        <div style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; padding: 12px 14px; text-align: left; display: inline-block;">
                            <div style="font-size: 7pt; color: #AAAAAA; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">Direct Phone Lines</div>
                            <div style="font-size: 11pt; font-weight: bold; color: #B7FF00; font-family: monospace; margin-bottom: 2px;">
                                {{ $settings['contact_phone_1'] ?? '6006801960' }}
                            </div>
                            <div style="font-size: 11pt; font-weight: bold; color: #B7FF00; font-family: monospace; margin-bottom: 6px;">
                                {{ $settings['contact_phone_2'] ?? '6005958161' }}
                            </div>

                            <div style="font-size: 7pt; color: #AAAAAA; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">Official Email</div>
                            <div style="font-size: 9pt; font-weight: bold; color: #FFFFFF; font-family: monospace;">
                                {{ $settings['contact_email'] ?? 'hire.iry@gmail.com' }}
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="doc-footer">
            <table class="layout">
                <tr>
                    <td style="width: 50%;">INVOIRY Product Specifications &bull; Section 05 (Closing Page)</td>
                    <td style="width: 50%; text-align: right;">Direct Phone: {{ $settings['contact_phone_1'] ?? '6006801960' }}</td>
                </tr>
            </table>
        </div>
    </div>
    @endif

</body>
</html>

