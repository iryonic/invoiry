@extends(auth()->check() ? 'layouts.app' : 'layouts.public')

@section('title', 'Privacy Policy · INVOIRY')
@section('page-title', 'Privacy Policy')
@section('page-subtitle', 'Enterprise Data Protection, Multi-Tenant Isolation & Hardware Privacy Standards')

@section('content')
<div class="space-y-6">

    <!-- Hero Card -->
    <div class="card-surface p-6 sm:p-8 space-y-3 border border-[#E6E6E6] shadow-2xs">
        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-[#ECFDF3] border border-[#A6F4C5] text-[#027A48] text-xs font-mono font-bold">
            <span class="w-2 h-2 rounded-full bg-[#079455] animate-pulse"></span>
            <span>DATA PROTECTION STANDARD &bull; UPDATED {{ date('F Y') }}</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-[#111111] tracking-tight">INVOIRY Platform Privacy Policy</h1>
        <p class="text-xs sm:text-sm text-[#666666] leading-relaxed max-w-3xl">
            This Privacy Policy sets forth how INVOIRY ("we", "our", or "the Platform") collects, isolates, encrypts, and processes store-level operational records, customer credit ledgers (Khata), electronic hardware identifiers (IMEI/Serial), and transaction logs across our multi-tenant cloud retail terminal.
        </p>
    </div>

    <!-- Quick Navigation Index -->
    <div class="card-surface p-4 sm:p-5 border border-[#E6E6E6] shadow-2xs">
        <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider mb-3">Policy Sections</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-xs">
            <a href="#section-1" class="p-2 rounded-lg bg-[#F7F7F6] hover:bg-[#EAEAEA] text-[#111111] font-semibold transition">1. Scope & Tenancy</a>
            <a href="#section-2" class="p-2 rounded-lg bg-[#F7F7F6] hover:bg-[#EAEAEA] text-[#111111] font-semibold transition">2. Data We Collect</a>
            <a href="#section-3" class="p-2 rounded-lg bg-[#F7F7F6] hover:bg-[#EAEAEA] text-[#111111] font-semibold transition">3. Multi-Tenant Isolation</a>
            <a href="#section-4" class="p-2 rounded-lg bg-[#F7F7F6] hover:bg-[#EAEAEA] text-[#111111] font-semibold transition">4. Hardware Identifiers</a>
            <a href="#section-5" class="p-2 rounded-lg bg-[#F7F7F6] hover:bg-[#EAEAEA] text-[#111111] font-semibold transition">5. Camera & Barcode</a>
            <a href="#section-6" class="p-2 rounded-lg bg-[#F7F7F6] hover:bg-[#EAEAEA] text-[#111111] font-semibold transition">6. Ledger & Khata</a>
            <a href="#section-7" class="p-2 rounded-lg bg-[#F7F7F6] hover:bg-[#EAEAEA] text-[#111111] font-semibold transition">7. Storage & Security</a>
            <a href="#section-8" class="p-2 rounded-lg bg-[#F7F7F6] hover:bg-[#EAEAEA] text-[#111111] font-semibold transition">8. Store Owner Rights</a>
        </div>
    </div>

    <!-- Main Policy Content -->
    <div class="space-y-6">

        <!-- Section 1 -->
        <section id="section-1" class="card-surface p-6 sm:p-8 space-y-3 border border-[#E6E6E6] shadow-2xs">
            <div class="flex items-center gap-2 text-xs font-bold font-mono text-[#079455] uppercase tracking-wider">
                <span>01.</span>
                <span>Scope & Multi-Tenant Operating Model</span>
            </div>
            <h2 class="text-lg sm:text-xl font-bold text-[#111111]">1. Enterprise Architecture & Architectural Isolation</h2>
            <p class="text-xs sm:text-sm text-[#666666] leading-relaxed">
                INVOIRY operates as a multi-tenant business software service designed specifically for modern electronics stores, retail device dealers, repair terminals, and wholesale distributors. In our multi-tenant model, each registered business ("Tenant") is allocated an isolated database boundary governed by unique foreign keys, automated tenant scope middleware, and cryptographic session isolation.
            </p>
            <div class="p-3.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs text-[#111111] font-mono">
                <strong>Strict Boundary Guarantee:</strong> Data stored by Store A is logically impossible to query, inspect, or aggregate by Store B or non-superadmin personnel. Cross-tenant leakage is systematically blocked at both the database query engine and application request lifecycle layers.
            </div>
        </section>

        <!-- Section 2 -->
        <section id="section-2" class="card-surface p-6 sm:p-8 space-y-4 border border-[#E6E6E6] shadow-2xs">
            <div class="flex items-center gap-2 text-xs font-bold font-mono text-[#079455] uppercase tracking-wider">
                <span>02.</span>
                <span>Information Architecture</span>
            </div>
            <h2 class="text-lg sm:text-xl font-bold text-[#111111]">2. Categories of Information Processed</h2>
            <p class="text-xs sm:text-sm text-[#666666] leading-relaxed">
                To execute retail billing, thermal receipt printing, tax computations, and customer ledger management, INVOIRY processes the following data categories:
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                <div class="p-4 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl space-y-1.5">
                    <h4 class="text-xs font-bold text-[#111111] flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#079455]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span>Store Business Profile</span>
                    </h4>
                    <p class="text-xs text-[#666666] leading-relaxed">
                        Store legal trade name, branch location, state, state code, GSTIN / Tax Identification number, counter contact numbers, custom invoice prefixes, and thermal receipt header remarks.
                    </p>
                </div>

                <div class="p-4 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl space-y-1.5">
                    <h4 class="text-xs font-bold text-[#111111] flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#079455]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        <span>Hardware Identifiers (IMEI/Serial)</span>
                    </h4>
                    <p class="text-xs text-[#666666] leading-relaxed">
                        Electronic device specifications, brand, model, storage, color, hardware condition (New, Used, Refurbished), Primary IMEI 1, Secondary IMEI 2 (Dual-SIM), and manufacturer serial numbers.
                    </p>
                </div>

                <div class="p-4 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl space-y-1.5">
                    <h4 class="text-xs font-bold text-[#111111] flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#079455]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span>Customer & Credit Ledger (Khata)</span>
                    </h4>
                    <p class="text-xs text-[#666666] leading-relaxed">
                        Customer full name, phone number, physical delivery address, state code, national identity proof references (Aadhaar / Voter ID), outstanding ledger credit balances, and counter payment settlements.
                    </p>
                </div>

                <div class="p-4 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl space-y-1.5">
                    <h4 class="text-xs font-bold text-[#111111] flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#079455]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Financial Billing & Tax Records</span>
                    </h4>
                    <p class="text-xs text-[#666666] leading-relaxed">
                        Invoice dates, line items, MRP subtotal, applied cash discounts, CGST, SGST, IGST tax breakdown, payment method (Cash, UPI, Card, Bank, Credit), and receipt audit trails.
                    </p>
                </div>
            </div>
        </section>

        <!-- Section 3 -->
        <section id="section-3" class="card-surface p-6 sm:p-8 space-y-3 border border-[#E6E6E6] shadow-2xs">
            <div class="flex items-center gap-2 text-xs font-bold font-mono text-[#079455] uppercase tracking-wider">
                <span>03.</span>
                <span>Camera & Optical Barcode Engine</span>
            </div>
            <h2 class="text-lg sm:text-xl font-bold text-[#111111]">3. Barcode Scanner & Camera Stream Privacy</h2>
            <p class="text-xs sm:text-sm text-[#666666] leading-relaxed">
                When using the box barcode / IMEI camera scanning feature on mobile or desktop terminals, INVOIRY requests browser camera permissions via the standard HTML5 `getUserMedia` API.
            </p>
            <ul class="list-disc list-inside space-y-1 text-xs text-[#666666]">
                <li><strong>Local Frame Processing:</strong> Optical recognition (1D Barcode, Code 128, EAN, QR Code) is executed <em>strictly in memory inside the local web browser</em>.</li>
                <li><strong>Zero Video Transmission:</strong> Video feeds, snapshots, camera streams, and audio are <em>never recorded, uploaded, or transmitted</em> to INVOIRY servers or third parties.</li>
                <li><strong>Instant Camera Teardown:</strong> As soon as a valid barcode is decoded or the scanner modal is closed, the hardware camera stream is explicitly stopped and released.</li>
            </ul>
        </section>

        <!-- Section 4 -->
        <section id="section-4" class="card-surface p-6 sm:p-8 space-y-3 border border-[#E6E6E6] shadow-2xs">
            <div class="flex items-center gap-2 text-xs font-bold font-mono text-[#079455] uppercase tracking-wider">
                <span>04.</span>
                <span>Statutory Compliance & GST Records</span>
            </div>
            <h2 class="text-lg sm:text-xl font-bold text-[#111111]">4. Tax Records & Immutable Financial Ledgers</h2>
            <p class="text-xs sm:text-sm text-[#666666] leading-relaxed">
                In compliance with commercial accounting statutes and Goods and Services Tax (GST) regulations, generated invoices, sales ledgers, and credit notes are treated as historical legal audit documents. Once generated, invoices maintain consecutive numbering and cannot be silently erased. In accordance with tax retention mandates, billing and transaction records are preserved for the statutory minimum period required by retail laws (typically 6 to 8 years).
            </p>
        </section>

        <!-- Section 5 -->
        <section id="section-5" class="card-surface p-6 sm:p-8 space-y-3 border border-[#E6E6E6] shadow-2xs">
            <div class="flex items-center gap-2 text-xs font-bold font-mono text-[#079455] uppercase tracking-wider">
                <span>05.</span>
                <span>Security & Infrastructure</span>
            </div>
            <h2 class="text-lg sm:text-xl font-bold text-[#111111]">5. Encryption, Access Controls & Session Protection</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div class="p-3 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg space-y-1">
                    <span class="font-bold text-[#111111] block">Password Security</span>
                    <p class="text-[#666666]">Stored using modern salted cryptographic hashing algorithms (Argon2id/Bcrypt). Plaintext passwords are never logged.</p>
                </div>
                <div class="p-3 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg space-y-1">
                    <span class="font-bold text-[#111111] block">Terminal Lock & Lockout</span>
                    <p class="text-[#666666]">Single-click `/lock` screen enables cashiers to step away from physical counters without terminating active sessions.</p>
                </div>
                <div class="p-3 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg space-y-1">
                    <span class="font-bold text-[#111111] block">Audit Logging</span>
                    <p class="text-[#666666]">Sensitive operations (user creation, permission changes, stock deletions) are recorded in the central Super Admin Audit Log.</p>
                </div>
            </div>
        </section>

        <!-- Section 6 -->
        <section id="section-6" class="card-surface p-6 sm:p-8 space-y-3 border border-[#E6E6E6] shadow-2xs">
            <div class="flex items-center gap-2 text-xs font-bold font-mono text-[#079455] uppercase tracking-wider">
                <span>06.</span>
                <span>Tenant Governance</span>
            </div>
            <h2 class="text-lg sm:text-xl font-bold text-[#111111]">6. Store Owner Rights & Support Contact</h2>
            <p class="text-xs sm:text-sm text-[#666666] leading-relaxed">
                As a registered store administrator, you maintain full control over your staff roles, permissions, dynamic form visibility, and customer ledger data. For queries regarding your data residency, export requests, or tenant administration:
            </p>
            <div class="p-4 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs space-y-1 text-[#111111]">
                <p><strong>INVOIRY Cloud Security & Privacy Operations</strong></p>
                <p class="text-[#666666]">Email: <span class="font-mono text-[#111111]">privacy@invoiry.com</span> &bull; Support: <span class="font-mono text-[#111111]">support@invoiry.com</span></p>
                <p class="text-[#666666]">Platform Master Console: <a href="{{ route('superadmin.index') }}" class="text-[#079455] font-bold hover:underline">Platform Administration</a></p>
            </div>
        </section>

    </div>
</div>
@endsection

