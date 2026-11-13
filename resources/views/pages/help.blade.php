@extends(auth()->check() ? 'layouts.app' : 'layouts.public')

@section('title', 'Help & Documentation · INVOIRY')
@section('page-title', 'Help & Documentation')
@section('page-subtitle', 'Complete Operational Guide, POS Billing Engine, Hardware Inwarding & Terminal Manual')

@section('content')
<div class="space-y-8">

    <!-- Hero Header -->
    <div class="card-surface p-6 sm:p-10 space-y-4 border border-[#E6E6E6] shadow-2xs">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#050505] text-[#B7FF00] text-xs font-mono font-bold tracking-wide">
                <span class="w-2 h-2 rounded-full bg-[#B7FF00] animate-pulse"></span>
                <span>INVOIRY RETAIL MANUAL &bull; VERSION 2.8</span>
            </span>
            <span class="text-xs text-[#666666] font-mono">Terminal Operations & Technical Reference</span>
        </div>
        <h1 class="text-2xl sm:text-4xl font-black text-[#111111] tracking-tight">System Documentation & Operational Manual</h1>
        <p class="text-xs sm:text-sm text-[#666666] leading-relaxed max-w-3xl">
            Welcome to the comprehensive operating manual for INVOIRY. This guide covers daily counter operations, serialized IMEI hardware management, automated GST billing calculations, thermal ESC/POS printing, and customer credit ledger (Khata) management.
        </p>

        <!-- Quick Jump Table of Contents Grid -->
        <div class="pt-3 border-t border-[#E6E6E6]">
            <p class="text-xs font-bold text-[#111111] uppercase tracking-wider mb-3">Quick Navigation Index</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5 text-xs">
                <a href="#module-quickstart" class="p-2.5 rounded-xl bg-[#F7F7F6] hover:bg-[#EAEAEA] text-[#111111] font-semibold flex items-center gap-2 transition">
                    <span>🚀</span> <span>1. Quick Start</span>
                </a>
                <a href="#module-inventory" class="p-2.5 rounded-xl bg-[#F7F7F6] hover:bg-[#EAEAEA] text-[#111111] font-semibold flex items-center gap-2 transition">
                    <span>📱</span> <span>2. Device Stock</span>
                </a>
                <a href="#module-billing" class="p-2.5 rounded-xl bg-[#F7F7F6] hover:bg-[#EAEAEA] text-[#111111] font-semibold flex items-center gap-2 transition">
                    <span>🧾</span> <span>3. POS Billing</span>
                </a>
                <a href="#module-gst" class="p-2.5 rounded-xl bg-[#F7F7F6] hover:bg-[#EAEAEA] text-[#111111] font-semibold flex items-center gap-2 transition">
                    <span>⚖️</span> <span>4. GST Engine</span>
                </a>
                <a href="#module-printing" class="p-2.5 rounded-xl bg-[#F7F7F6] hover:bg-[#EAEAEA] text-[#111111] font-semibold flex items-center gap-2 transition">
                    <span>🖨️</span> <span>5. Thermal Print</span>
                </a>
                <a href="#module-khata" class="p-2.5 rounded-xl bg-[#F7F7F6] hover:bg-[#EAEAEA] text-[#111111] font-semibold flex items-center gap-2 transition">
                    <span>📒</span> <span>6. Khata Ledger</span>
                </a>
                <a href="#module-rbac" class="p-2.5 rounded-xl bg-[#F7F7F6] hover:bg-[#EAEAEA] text-[#111111] font-semibold flex items-center gap-2 transition">
                    <span>👥</span> <span>7. Staff & Roles</span>
                </a>
                <a href="#module-security" class="p-2.5 rounded-xl bg-[#F7F7F6] hover:bg-[#EAEAEA] text-[#111111] font-semibold flex items-center gap-2 transition">
                    <span>🔒</span> <span>8. Security & Lock</span>
                </a>
            </div>
        </div>
    </div>

    <!-- MODULE 1: QUICK START -->
    <section id="module-quickstart" class="card-surface p-6 sm:p-8 space-y-4 border border-[#E6E6E6] shadow-2xs">
        <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#050505] text-[#B7FF00] flex items-center justify-center font-black text-sm">01</div>
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-[#111111]">Terminal Onboarding & Store Configuration</h2>
                    <p class="text-xs text-[#666666]">Essential steps before generating your first customer invoice</p>
                </div>
            </div>
            <span class="text-[11px] font-mono font-bold text-[#079455] bg-[#ECFDF3] px-2.5 py-1 rounded-full border border-[#A6F4C5]">INITIAL SETUP</span>
        </div>

        <div class="space-y-4 text-xs sm:text-sm text-[#444444] leading-relaxed">
            <p>Before launching retail counter operations, ensure your Store Profile is configured under <strong>Store Admin &rarr; Store Profile & UPI</strong>:</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="p-4 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl space-y-1">
                    <span class="font-bold text-[#111111] block">1. Business Name & City</span>
                    <p class="text-xs text-[#666666]">Appears on the top of thermal slips, customer receipts, and browser tab headers.</p>
                </div>
                <div class="p-4 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl space-y-1">
                    <span class="font-bold text-[#111111] block">2. State & State Code</span>
                    <p class="text-xs text-[#666666]">Crucial for GST math. E.g., State Code `01` (J&K), `07` (Delhi), `27` (Maharashtra). Controls CGST/SGST vs IGST.</p>
                </div>
                <div class="p-4 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl space-y-1">
                    <span class="font-bold text-[#111111] block">3. Counter UPI VPA</span>
                    <p class="text-xs text-[#666666]">Enter your store UPI ID (e.g. `store@okaxis`). Generates instant dynamic QR codes with exact bill amounts.</p>
                </div>
            </div>

            <div class="p-4 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs space-y-1 text-[#111111]">
                <strong>Invoice Numbering Convention:</strong> Each store has a customizable invoice prefix (e.g. `INV-`, `POS-`, `ET-`) and an automated sequential 5-digit zero-padded counter (`INV-00001`, `INV-00002`). This sequence is guaranteed unique per business tenant.
            </div>
        </div>
    </section>

    <!-- MODULE 2: INVENTORY & STOCK -->
    <section id="module-inventory" class="card-surface p-6 sm:p-8 space-y-4 border border-[#E6E6E6] shadow-2xs">
        <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#050505] text-[#B7FF00] flex items-center justify-center font-black text-sm">02</div>
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-[#111111]">Stock Inwarding & Hardware Serialization</h2>
                    <p class="text-xs text-[#666666]">Managing incoming inventory, IMEI 1 & 2 validation, and barcode scanning</p>
                </div>
            </div>
            <span class="text-[11px] font-mono font-bold text-[#175CD3] bg-[#EFF8FF] px-2.5 py-1 rounded-full border border-[#B2DDFF]">INVENTORY</span>
        </div>

        <div class="space-y-4 text-xs sm:text-sm text-[#444444] leading-relaxed">
            <h4 class="font-bold text-[#111111] text-xs uppercase tracking-wider">Inward Device Step-by-Step</h4>
            <ol class="list-decimal list-inside space-y-2 text-xs">
                <li>Navigate to <strong>Inventory &rarr; Inward Purchase</strong> or click <em>Inward Device</em>.</li>
                <li><strong>Barcode Camera Scan:</strong> Click <em>Scan Box Barcode</em>. Point your device camera at the retail box IMEI barcode. The system uses real-time canvas decoding to populate the IMEI automatically.</li>
                <li><strong>Dual-SIM IMEI Validation:</strong> Enter IMEI 1 (Primary) and optional IMEI 2 (Secondary SIM). INVOIRY enforces cross-column uniqueness in active stock so duplicate devices cannot be entered into inventory.</li>
                <li><strong>Condition Grades:</strong> Select <code>Brand New (Sealed)</code>, <code>Pre-Owned (Used)</code>, or <code>Refurbished</code>.</li>
                <li><strong>Supplier Tracking:</strong> Choose an existing distributor or fill in walk-in seller details (Name, Phone, Aadhaar / ID proof) to record supplier history on the fly.</li>
            </ol>

            <div class="p-3.5 bg-[#ECFDF3] border border-[#A6F4C5] rounded-xl text-xs text-[#027A48]">
                <strong>Audit Trail Protection on Stock:</strong> INVOIRY protects inventory integrity. If an item has already been billed on an invoice, the system prevents it from being deleted via bulk action or single delete to preserve historical cost-of-goods-sold and warranty records.
            </div>
        </div>
    </section>

    <!-- MODULE 3: POS BILLING ENGINE -->
    <section id="module-billing" class="card-surface p-6 sm:p-8 space-y-4 border border-[#E6E6E6] shadow-2xs">
        <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#050505] text-[#B7FF00] flex items-center justify-center font-black text-sm">03</div>
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-[#111111]">Point of Sale (POS) Billing Engine</h2>
                    <p class="text-xs text-[#666666]">Generating customer bills, cash vs credit sales, and real-time calculation</p>
                </div>
            </div>
            <span class="text-[11px] font-mono font-bold text-[#B45309] bg-[#FFFBEB] px-2.5 py-1 rounded-full border border-[#FDE68A]">CORE BILLING</span>
        </div>

        <div class="space-y-4 text-xs sm:text-sm text-[#444444] leading-relaxed">
            <h4 class="font-bold text-[#111111] text-xs uppercase tracking-wider">Fast-Paced Checkout Workflow</h4>
            <div class="space-y-3 text-xs">
                <div class="p-3.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl space-y-1">
                    <span class="font-bold text-[#111111] block">1. Customer Selection</span>
                    <p class="text-[#666666]">Choose an existing customer from the dropdown to auto-fill contact information, or type a new customer's name and mobile number. A customer ledger record is created automatically.</p>
                </div>

                <div class="p-3.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl space-y-1">
                    <span class="font-bold text-[#111111] block">2. Item Selection & Scanning</span>
                    <p class="text-[#666666]">Select an in-stock phone from the quick picker, or scan the device IMEI. The selling price, storage, color, and warranty status automatically populate the row. You can add multiple rows for accessories or chargers.</p>
                </div>

                <div class="p-3.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl space-y-1">
                    <span class="font-bold text-[#111111] block">3. Amount Paid & Credit / Khata Handling</span>
                    <p class="text-[#666666]">
                        <strong>Empty = Unpaid / Credit:</strong> If the customer paid nothing at checkout, leave the <em>Amount Paid</em> field empty. The system automatically records <strong>₹0.00</strong> paid and records the full bill as balance due on the customer's Khata.<br>
                        <strong>Pay Full Button:</strong> Click the green <em>Pay Full (₹...)</em> button to populate the exact grand total with one click.<br>
                        <strong>Partial Payment:</strong> Enter any token amount (e.g. ₹5,000). The remaining balance is tracked as unpaid debt.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- MODULE 4: GST ENGINE -->
    <section id="module-gst" class="card-surface p-6 sm:p-8 space-y-4 border border-[#E6E6E6] shadow-2xs">
        <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#050505] text-[#B7FF00] flex items-center justify-center font-black text-sm">04</div>
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-[#111111]">Automated GST Math & Tax Logic</h2>
                    <p class="text-xs text-[#666666]">Intra-State (CGST + SGST) vs Inter-State (IGST) and MRP Inclusive Math</p>
                </div>
            </div>
            <span class="text-[11px] font-mono font-bold text-[#079455] bg-[#ECFDF3] px-2.5 py-1 rounded-full border border-[#A6F4C5]">TAX COMPLIANCE</span>
        </div>

        <div class="space-y-4 text-xs sm:text-sm text-[#444444] leading-relaxed">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl space-y-2">
                    <h4 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Tax Inclusive (MRP) Billing</h4>
                    <p class="text-xs text-[#666666]">Standard for retail electronics counters where sticker price includes GST:</p>
                    <div class="p-2.5 bg-white border border-[#E6E6E6] rounded font-mono text-[11px] text-[#111111]">
                        Subtotal = Selling Price &divide; (1 + Tax Rate / 100)<br>
                        Tax Amount = Selling Price &minus; Subtotal
                    </div>
                    <p class="text-[11px] text-[#666666]">Example: ₹11,800 @ 18% GST &rarr; Subtotal: ₹10,000, GST: ₹1,800. Customer pays ₹11,800.</p>
                </div>

                <div class="p-4 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl space-y-2">
                    <h4 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Tax Exclusive Billing</h4>
                    <p class="text-xs text-[#666666]">Standard for B2B transactions where tax is added on top of base quote:</p>
                    <div class="p-2.5 bg-white border border-[#E6E6E6] rounded font-mono text-[11px] text-[#111111]">
                        Tax Amount = Base Price &times; (Tax Rate / 100)<br>
                        Grand Total = Base Price + Tax Amount
                    </div>
                    <p class="text-[11px] text-[#666666]">Example: ₹10,000 @ 18% GST &rarr; GST: ₹1,800, Grand Total: ₹11,800.</p>
                </div>
            </div>

            <div class="p-3.5 bg-[#EFF8FF] border border-[#B2DDFF] rounded-xl text-xs text-[#175CD3] space-y-1">
                <strong>Automated Place of Supply Routing:</strong>
                <p>If Customer State Code matches Store State Code (e.g. Both `01`), INVOIRY automatically splits tax into <strong>Central Tax (CGST 9%)</strong> + <strong>State Tax (SGST 9%)</strong>. If Customer State Code differs (e.g. Customer is `07` Delhi, Store is `01`), the system automatically routes to <strong>Integrated Tax (IGST 18%)</strong>.</p>
            </div>
        </div>
    </section>

    <!-- MODULE 5: THERMAL PRINTING -->
    <section id="module-printing" class="card-surface p-6 sm:p-8 space-y-4 border border-[#E6E6E6] shadow-2xs">
        <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#050505] text-[#B7FF00] flex items-center justify-center font-black text-sm">05</div>
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-[#111111]">Receipt Printing & Thermal Slip Configuration</h2>
                    <p class="text-xs text-[#666666]">A4 Tax Invoices, 80mm & 58mm POS thermal slips, and WhatsApp sharing</p>
                </div>
            </div>
            <span class="text-[11px] font-mono font-bold text-[#666666] bg-[#F2F4F7] px-2.5 py-1 rounded-full border border-[#EAECF0]">HARDWARE POS</span>
        </div>

        <div class="space-y-4 text-xs sm:text-sm text-[#444444] leading-relaxed">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="p-4 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl space-y-1.5">
                    <span class="font-bold text-[#111111] block">Standard A4 Tax Invoice</span>
                    <p class="text-xs text-[#666666]">Complete full-page legal invoice with customer GSTIN, HSN codes, authorized signature line, and store terms.</p>
                </div>

                <div class="p-4 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl space-y-1.5">
                    <span class="font-bold text-[#111111] block">80mm / 58mm Thermal Slip</span>
                    <p class="text-xs text-[#666666]">Compact ESC/POS styling with dashed cut lines, bold totals, IMEI serial notes, and payment status stamps.</p>
                </div>

                <div class="p-4 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl space-y-1.5">
                    <span class="font-bold text-[#111111] block">Instant WhatsApp Share</span>
                    <p class="text-xs text-[#666666]">Click the green WhatsApp button on any invoice to dispatch a pre-composed bill summary directly to the customer's phone.</p>
                </div>
            </div>

            <div class="p-3.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs space-y-1 text-[#111111]">
                <strong>Thermal Printer Driver Tip:</strong> In your browser print preview (Chrome / Edge), set <em>Margins: None</em> and uncheck <em>Headers and Footers</em> to achieve clean, gap-free thermal receipts on POS roll printers.
            </div>
        </div>
    </section>

    <!-- MODULE 6: KHATA CREDIT LEDGER -->
    <section id="module-khata" class="card-surface p-6 sm:p-8 space-y-4 border border-[#E6E6E6] shadow-2xs">
        <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#050505] text-[#B7FF00] flex items-center justify-center font-black text-sm">06</div>
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-[#111111]">Customer Credit Ledger (Khata)</h2>
                    <p class="text-xs text-[#666666]">Managing credit balances, partial payments, and ledger settlement</p>
                </div>
            </div>
            <span class="text-[11px] font-mono font-bold text-[#D97706] bg-[#FFFBEB] px-2.5 py-1 rounded-full border border-[#FDE68A]">CREDIT LEDGER</span>
        </div>

        <div class="space-y-4 text-xs sm:text-sm text-[#444444] leading-relaxed">
            <p>Under <strong>Contacts & Ledger &rarr; Customer Credit (Khata)</strong>, you can monitor the total outstanding balance owed to your store:</p>

            <ul class="list-disc list-inside space-y-1.5 text-xs text-[#666666]">
                <li><strong>Khata Formula:</strong> <code>Pending Due = Total Purchases &minus; Total Payments Received</code>.</li>
                <li><strong>Recording a Settle Payment:</strong> Open the customer's ledger page and click <em>Record Settle Payment</em>. Enter the amount received, payment method (Cash, UPI, Bank), and optional transaction reference. The system automatically reconciles oldest unpaid invoices first.</li>
                <li><strong>Customer Statement:</strong> Complete chronological log of every purchase, invoice date, payment receipt, and running credit balance.</li>
            </ul>
        </div>
    </section>

    <!-- MODULE 7: STAFF & ROLES -->
    <section id="module-rbac" class="card-surface p-6 sm:p-8 space-y-4 border border-[#E6E6E6] shadow-2xs">
        <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#050505] text-[#B7FF00] flex items-center justify-center font-black text-sm">07</div>
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-[#111111]">Staff Management & Role-Based Permissions</h2>
                    <p class="text-xs text-[#666666]">Restricting cashier access, configuring form visibility, and managing permissions</p>
                </div>
            </div>
            <span class="text-[11px] font-mono font-bold text-[#111111] bg-[#F7F7F6] px-2.5 py-1 rounded-full border border-[#E6E6E6]">RBAC SECURITY</span>
        </div>

        <div class="space-y-4 text-xs sm:text-sm text-[#444444] leading-relaxed">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="p-3.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl space-y-1">
                    <span class="font-bold text-[#111111] block">Tenant Admin</span>
                    <p class="text-xs text-[#666666]">Full store master authority. Manages store profile, GSTIN, staff users, custom invoice prefix, and financial ledgers.</p>
                </div>

                <div class="p-3.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl space-y-1">
                    <span class="font-bold text-[#111111] block">Store Manager</span>
                    <p class="text-xs text-[#666666]">Can inward devices, edit pricing, manage stock, create bills, and record khata settlement payments.</p>
                </div>

                <div class="p-3.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl space-y-1">
                    <span class="font-bold text-[#111111] block">Cashier</span>
                    <p class="text-xs text-[#666666]">Front-counter billing access. Can search inventory, create customer bills, print receipts, and record customer details.</p>
                </div>
            </div>

            <div class="p-3.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs space-y-1 text-[#111111]">
                <strong>Dynamic Form Field Visibility:</strong> The Super Admin or Store Admin can toggle specific fields on or off (e.g. Hiding Aadhaar number or customer address for express fast-checkout terminals).
            </div>
        </div>
    </section>

    <!-- MODULE 8: SECURITY & LOCK -->
    <section id="module-security" class="card-surface p-6 sm:p-8 space-y-4 border border-[#E6E6E6] shadow-2xs">
        <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#050505] text-[#B7FF00] flex items-center justify-center font-black text-sm">08</div>
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-[#111111]">Security, Counter Lock & Emergency Maintenance</h2>
                    <p class="text-xs text-[#666666]">Protecting counters, password toggles, and platform maintenance mode</p>
                </div>
            </div>
            <span class="text-[11px] font-mono font-bold text-[#DC2626] bg-[#FEF2F2] px-2.5 py-1 rounded-full border border-[#FECACA]">SECURITY</span>
        </div>

        <div class="space-y-4 text-xs sm:text-sm text-[#444444] leading-relaxed">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl space-y-1.5">
                    <h4 class="text-xs font-bold text-[#111111]">Quick Counter Lock (`/lock`)</h4>
                    <p class="text-xs text-[#666666]">
                        Click the lock icon in the sidebar or bottom bar when stepping away from the cash counter. The terminal locks instantly. Enter your staff password to unlock and resume your active workspace without losing input data.
                    </p>
                </div>

                <div class="p-4 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl space-y-1.5">
                    <h4 class="text-xs font-bold text-[#111111]">Password Visibility Toggles</h4>
                    <p class="text-xs text-[#666666]">
                        Every password field across the system (Login, Lock, Register, Reset, and Staff Management) includes a tactile show/hide eye toggle button to prevent typing mistakes on touchscreens.
                    </p>
                </div>
            </div>

            <div class="p-3.5 bg-[#FFFBEB] border border-[#FDE68A] text-[#92400E] rounded-xl text-xs space-y-1">
                <strong>Emergency Maintenance Mode (HTTP 503):</strong>
                <p>When platform maintenance mode is enabled by the Super Admin, the public system renders an emergency status screen with pure white branding. Only authenticated Super Admins can log in to bypass maintenance mode, inspect store terminals, and revoke maintenance mode from the settings console.</p>
            </div>
        </div>
    </section>

</div>
@endsection

