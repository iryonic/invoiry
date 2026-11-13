@extends(auth()->check() ? 'layouts.app' : 'layouts.public')

@section('title', 'Terms and Conditions · INVOIRY')
@section('page-title', 'Terms and Conditions')
@section('page-subtitle', 'Commercial Master Service Agreement, Retail Terminal Operating Standards & Legal Policies')

@section('content')
<div class="space-y-6">

    <!-- Hero Card -->
    <div class="card-surface p-4 sm:p-8 space-y-3 border border-[#E6E6E6] shadow-2xs">
        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-[#EFF8FF] border border-[#B2DDFF] text-[#175CD3] text-xs font-mono font-bold">
            <span class="w-2 h-2 rounded-full bg-[#175CD3] animate-pulse"></span>
            <span>MASTER SERVICE AGREEMENT &bull; EFFECTIVE {{ date('F Y') }}</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-[#111111] tracking-tight">Terms and Conditions of Service</h1>
        <p class="text-xs sm:text-sm text-[#666666] leading-relaxed max-w-3xl">
            Please read these Terms and Conditions ("Terms", "Agreement") carefully before subscribing to, provisioning, or operating any retail store terminal on the INVOIRY Point of Sale & Inventory Platform ("INVOIRY", "Service").
        </p>
    </div>

    <!-- Quick Navigation Index -->
    <div class="card-surface p-4 sm:p-5 border border-[#E6E6E6] shadow-2xs">
        <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider mb-3">Agreement Sections</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-xs">
            <a href="#term-1" class="p-2 rounded-lg bg-[#F7F7F6] hover:bg-[#EAEAEA] text-[#111111] font-semibold transition">1. Acceptance & Access</a>
            <a href="#term-2" class="p-2 rounded-lg bg-[#F7F7F6] hover:bg-[#EAEAEA] text-[#111111] font-semibold transition">2. Account Responsibility</a>
            <a href="#term-3" class="p-2 rounded-lg bg-[#F7F7F6] hover:bg-[#EAEAEA] text-[#111111] font-semibold transition">3. IMEI & Hardware Laws</a>
            <a href="#term-4" class="p-2 rounded-lg bg-[#F7F7F6] hover:bg-[#EAEAEA] text-[#111111] font-semibold transition">4. GST & Tax Disclaimer</a>
            <a href="#term-5" class="p-2 rounded-lg bg-[#F7F7F6] hover:bg-[#EAEAEA] text-[#111111] font-semibold transition">5. Khata Credit Ledger</a>
            <a href="#term-6" class="p-2 rounded-lg bg-[#F7F7F6] hover:bg-[#EAEAEA] text-[#111111] font-semibold transition">6. Thermal Printing & POS</a>
            <a href="#term-7" class="p-2 rounded-lg bg-[#F7F7F6] hover:bg-[#EAEAEA] text-[#111111] font-semibold transition">7. Service SLA & Uptime</a>
            <a href="#term-8" class="p-2 rounded-lg bg-[#F7F7F6] hover:bg-[#EAEAEA] text-[#111111] font-semibold transition">8. Liability & Governance</a>
        </div>
    </div>

    <!-- Agreement Content Sections -->
    <div class="space-y-6">

        <!-- 1 -->
        <section id="term-1" class="card-surface p-4 sm:p-8 space-y-3 border border-[#E6E6E6] shadow-2xs">
            <div class="flex items-center gap-2 text-xs font-bold font-mono text-[#175CD3] uppercase tracking-wider">
                <span>01.</span>
                <span>Acceptance & Subscription Terms</span>
            </div>
            <h2 class="text-lg sm:text-xl font-bold text-[#111111]">1. Subscription, Grant of License & Authorization</h2>
            <p class="text-xs sm:text-sm text-[#666666] leading-relaxed">
                By creating a store account, activating a trial evaluation, or signing in as a tenant administrator, manager, or cashier, you agree to be bound by these Terms. INVOIRY grants your registered retail entity a revocable, non-exclusive, non-transferable, multi-device cloud software license to operate electronic inventory management, POS billing, and customer credit ledger tracking for your business.
            </p>
        </section>

        <!-- 2 -->
        <section id="term-2" class="card-surface p-4 sm:p-8 space-y-3 border border-[#E6E6E6] shadow-2xs">
            <div class="flex items-center gap-2 text-xs font-bold font-mono text-[#175CD3] uppercase tracking-wider">
                <span>02.</span>
                <span>Terminal Credentials & Account Security</span>
            </div>
            <h2 class="text-lg sm:text-xl font-bold text-[#111111]">2. Cashier Terminal Integrity & Role-Based Access</h2>
            <p class="text-xs sm:text-sm text-[#666666] leading-relaxed">
                The Tenant Administrator is solely responsible for creating and provisioning authorized staff users (Managers, Cashiers, Technicians), assigning appropriate RBAC permissions, and monitoring counter transactions.
            </p>
            <ul class="list-disc list-inside space-y-1 text-xs text-[#666666]">
                <li><strong>Counter Lock Discipline:</strong> Cashiers must utilize the `/lock` screen when stepping away from active retail terminals.</li>
                <li><strong>Credential Sharing:</strong> Multiple employees must not share a single administrative account. Each cashier should operate under an individualized profile for audit logging accountability.</li>
                <li><strong>Compromise Notification:</strong> The store administrator must immediately revoke sessions and update credentials if unauthorized access is suspected.</li>
            </ul>
        </section>

        <!-- 3 -->
        <section id="term-3" class="card-surface p-4 sm:p-8 space-y-3 border border-[#E6E6E6] shadow-2xs">
            <div class="flex items-center gap-2 text-xs font-bold font-mono text-[#175CD3] uppercase tracking-wider">
                <span>03.</span>
                <span>Hardware Serialization & Verification</span>
            </div>
            <h2 class="text-lg sm:text-xl font-bold text-[#111111]">3. IMEI, Serial Tracking & Stolen Hardware Responsibility</h2>
            <p class="text-xs sm:text-sm text-[#666666] leading-relaxed">
                INVOIRY provides dual-SIM IMEI and serial recording capabilities for inward purchases and outgoing sales. 
            </p>
            <div class="p-3.5 bg-[#FFFBEB] border border-[#FDE68A] text-[#92400E] rounded-xl text-xs space-y-1">
                <span class="font-bold block">Legal Notice on Used Electronics Inwarding:</span>
                <p>The store owner holds exclusive legal responsibility for verifying seller identity proof (Aadhaar, Voter ID, Passport) and ensuring that devices inwarded from walk-in sellers or third-party liquidators are not stolen, blacklisted, or subject to law enforcement inquiries. INVOIRY does not operate an independent police registry and serves strictly as your internal commercial record-keeping system.</p>
            </div>
        </section>

        <!-- 4 -->
        <section id="term-4" class="card-surface p-4 sm:p-8 space-y-3 border border-[#E6E6E6] shadow-2xs">
            <div class="flex items-center gap-2 text-xs font-bold font-mono text-[#175CD3] uppercase tracking-wider">
                <span>04.</span>
                <span>Tax Engine & Fiscal Compliance</span>
            </div>
            <h2 class="text-lg sm:text-xl font-bold text-[#111111]">4. Goods and Services Tax (GST) Disclaimer</h2>
            <p class="text-xs sm:text-sm text-[#666666] leading-relaxed">
                The platform includes an automated GST billing engine that computes intra-state (CGST + SGST) and inter-state (IGST) breakdowns, tax-inclusive MRP factoring, and HSN/SAC codes (e.g. 8517 for cellular telecommunications hardware).
            </p>
            <p class="text-xs text-[#666666] leading-relaxed">
                <strong>Disclaimer of Tax Advisory:</strong> INVOIRY is an automated computation tool and does not provide legal, accounting, or fiscal advisory services. Store owners are solely responsible for setting accurate tax rates (e.g., 18%, 12%, 5%), declaring accurate GSTIN numbers, maintaining consecutive invoice counters, and filing monthly GSTR returns with statutory revenue departments.
            </p>
        </section>

        <!-- 5 -->
        <section id="term-5" class="card-surface p-4 sm:p-8 space-y-3 border border-[#E6E6E6] shadow-2xs">
            <div class="flex items-center gap-2 text-xs font-bold font-mono text-[#175CD3] uppercase tracking-wider">
                <span>05.</span>
                <span>Customer Khata (Credit Ledger)</span>
            </div>
            <h2 class="text-lg sm:text-xl font-bold text-[#111111]">5. Customer Credit Ledger (Khata) & Settlement</h2>
            <p class="text-xs sm:text-sm text-[#666666] leading-relaxed">
                The Khata feature records deferred customer payments and partial billing balances. All credit arrangements, repayment timelines, and credit limits are strictly private commercial arrangements between the store and the customer. INVOIRY does not underwrite debt, collect payments on behalf of retail stores, or report credit scores to bureaus.
            </p>
        </section>

        <!-- 6 -->
        <section id="term-6" class="card-surface p-4 sm:p-8 space-y-3 border border-[#E6E6E6] shadow-2xs">
            <div class="flex items-center gap-2 text-xs font-bold font-mono text-[#175CD3] uppercase tracking-wider">
                <span>06.</span>
                <span>Thermal Hardware & ESC/POS Formatting</span>
            </div>
            <h2 class="text-lg sm:text-xl font-bold text-[#111111]">6. Thermal POS Printing & Local Peripherals</h2>
            <p class="text-xs sm:text-sm text-[#666666] leading-relaxed">
                The platform formats receipts for standard 80mm and 58mm thermal receipt printers via browser CSS `@page` media and ESC/POS styling standards. The performance and quality of thermal output depends on the store's physical printer hardware, driver configuration, and paper quality.
            </p>
        </section>

        <!-- 7 -->
        <section id="term-7" class="card-surface p-4 sm:p-8 space-y-3 border border-[#E6E6E6] shadow-2xs">
            <div class="flex items-center gap-2 text-xs font-bold font-mono text-[#175CD3] uppercase tracking-wider">
                <span>07.</span>
                <span>SLA & Maintenance Windows</span>
            </div>
            <h2 class="text-lg sm:text-xl font-bold text-[#111111]">7. Service Level Availability (SLA) & Maintenance</h2>
            <p class="text-xs sm:text-sm text-[#666666] leading-relaxed">
                INVOIRY targets a <strong>99.9% uptime</strong> standard for core billing operations. During scheduled cloud server upgrades or database migrations, the platform may briefly enter Emergency Maintenance Mode (HTTP 503). During maintenance, public access is temporarily redirected, and Super Administrators maintain exclusive platform access to complete system patching before restoring terminal availability.
            </p>
        </section>

        <!-- 8 -->
        <section id="term-8" class="card-surface p-4 sm:p-8 space-y-3 border border-[#E6E6E6] shadow-2xs">
            <div class="flex items-center gap-2 text-xs font-bold font-mono text-[#175CD3] uppercase tracking-wider">
                <span>08.</span>
                <span>Limitation of Liability</span>
            </div>
            <h2 class="text-lg sm:text-xl font-bold text-[#111111]">8. Limitation of Liability & Termination</h2>
            <p class="text-xs sm:text-sm text-[#666666] leading-relaxed">
                To the maximum extent permitted by applicable commercial law, INVOIRY shall not be liable for any indirect, incidental, punitive, or consequential damages resulting from store network loss, cashier miscalculations, hardware failure, or lost business revenue. Either party may terminate a subscription by providing written notice or letting an active subscription period expire without renewal.
            </p>
            <div class="p-4 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs text-[#666666]">
                For legal inquiries, contract amendments, or commercial licensing agreements, contact <span class="font-mono text-[#111111]">legal@invoiry.com</span>.
            </div>
        </section>

    </div>
</div>
@endsection

