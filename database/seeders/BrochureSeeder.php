<?php

namespace Database\Seeders;

use App\Models\BrochureModule;
use App\Models\BrochurePlan;
use App\Models\BrochureScreenshot;
use App\Models\BrochureSetting;
use Illuminate\Database\Seeder;

class BrochureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Settings & Branding
        $settings = [
            'product_name' => 'INVOIRY',
            'product_tagline' => 'Enterprise POS, Hardware Tracking & Fiscal Ledger Platform',
            'document_title' => 'INVOIRY — Product Overview, Modular Architecture & Commercial Plans',
            'document_subtitle' => 'Official Enterprise Features & Pricing Specification Brochure',
            'version' => '2.4.0',
            'hero_headline' => 'Next-Generation Retail POS Built for Electronics, Telecom & Hardware Enterprises',
            'hero_subheadline' => 'Seamless point-of-sale billing, dual-SIM IMEI lifecycle governance, automated GST reconciliation, thermal receipt output, and real-time customer credit ledgers — all in one unified, multi-tenant cloud workspace.',
            'primary_color' => '#111111',
            'accent_color' => '#B7FF00',
            'secondary_color' => '#079455',
            'contact_phone_1' => '6006801960',
            'contact_phone_2' => '6005958161',
            'contact_email' => 'hire.iry@gmail.com',
            'contact_website' => 'https://github.com/iryonic/invoiry.git',
            'contact_address' => 'INVOIRY Technologies Hub, Tech Boulevard, J&K / New Delhi, India',
            'overview_what_it_does' => 'INVOIRY is a high-performance, multi-tenant Point of Sale (POS) and inventory ERP system engineered specifically for electronics retailers, smartphone dealerships, computer hardware stores, and multi-outlet merchant chains. It merges rapid counter checkout with mandatory dual-IMEI tracking, tax-compliant GST invoicing, instant thermal printing, and integrated Khata ledger management.',
            'overview_problems_solved' => "• Eliminates stolen device liabilities through mandatory Primary & Secondary IMEI verification.\n• Solves multi-rate GST confusion with automated Intra-State (CGST+SGST) and Inter-State (IGST) math.\n• Prevents cash-flow leakage by tying partial payments directly to real-time Customer Khata ledgers.\n• Ends hardware counter bottlenecks via native ESC/POS thermal printing (58mm/80mm) with zero driver lag.\n• Protects sensitive margins and counter terminals with role-based access control and instant emergency /lock mode.",
            'overview_target_audience' => 'Smartphone & Mobile Outlets, Computer & IT Hardware Retailers, Consumer Electronics Showrooms, Authorized Brand Dealerships, Multi-Branch Franchise Chains, and Independent Electronics Wholesalers.',
            'overview_key_benefits' => "1. Zero-Friction Counter POS: Complete checkout in under 15 seconds with camera & USB barcode scanning.\n2. Dual IMEI & Serial Security: Complete hardware provenance tracking from inward purchase to end-consumer sale.\n3. Flawless GST Engine: Switch between Tax-Inclusive (B2C) and Tax-Exclusive (B2B) billing with 1-click state compliance.\n4. Dual-Mode Printing: Instant thermal slip receipts or detailed A4 Tax Invoices with UPI dynamic QR codes.\n5. Customer Khata Credit Engine: Automated partial payment receipts, outstanding balance reminders, and due dates.\n6. Multi-Tenant Cloud Architecture: Total database isolation, customizable form fields, and central Super Admin control.",
            'terms_notes' => "• All subscription tiers include automatic database migrations, security patches, and cloud backups.\n• Commercial thermal printers supported include all standard 58mm & 80mm ESC/POS USB, Ethernet, and Bluetooth terminals.\n• Hardware IMEI data is stored with tamper-evident audit logs complying with telecom trade regulations.\n• Custom API integrations, custom domains, and dedicated private server deployments available on Enterprise tiers.",
            'show_cover' => '1',
            'show_overview' => '1',
            'show_modules' => '1',
            'show_screenshots' => '1',
            'show_pricing' => '1',
            'show_specifications' => '1',
            'show_contact' => '1',
        ];

        foreach ($settings as $key => $val) {
            BrochureSetting::set($key, $val);
        }

        // 2. Modules
        BrochureModule::truncate();
        BrochureScreenshot::truncate();

        $m1 = BrochureModule::create([
            'title' => 'Rapid POS Billing & Instant Checkout',
            'subtitle' => 'High-Speed Counter Billing Engine',
            'badge' => 'CORE TERMINAL',
            'icon' => 'shopping-cart',
            'description' => 'Engineered for fast-paced retail counters where speed and accuracy are paramount. Cashiers can scan barcodes with handheld USB scanners or device cameras, select items with live stock validation, apply item-level discounts, and generate bills in seconds.',
            'features' => [
                'Live barcode & camera scanner with zero lag input response',
                'Tax Inclusive vs Tax Exclusive dynamic calculation mode',
                'Automated CGST + SGST (Intra-State) and IGST (Inter-State) splitting',
                'Dynamic UPI payment QR code generated on every invoice',
                'Partial payment recording with instant Khata credit synchronization',
                'Automated invoice numbering with store-custom prefixes',
            ],
            'sort_order' => 1,
            'is_enabled' => true,
        ]);

        $m2 = BrochureModule::create([
            'title' => 'Dual-SIM IMEI & Hardware Serial Tracking',
            'subtitle' => 'Statutory Telecom & Electronics Compliance',
            'badge' => 'HARDWARE GOVERNANCE',
            'icon' => 'device-mobile',
            'description' => 'Comprehensive hardware identity governance designed specifically for mobile phones, tablets, laptops, and serial-numbered electronics. Eliminates stolen hardware liability and tracks warranty lifecycle from inwarding to final consumer checkout.',
            'features' => [
                'Dual-SIM IMEI tracking (IMEI-1 and IMEI-2) on every handset unit',
                'Unique serial number logging for laptops, peripherals, and smart accessories',
                'Warranty status tracking with automated expiration date calculation',
                'Condition grading at inwarding (Brand New, Refurbished, Pre-Owned, Open Box)',
                'Stolen device protection with permanent hardware audit trail',
                'Technical specification tags (Storage, RAM, Color, Chipset, Model variant)',
            ],
            'sort_order' => 2,
            'is_enabled' => true,
        ]);

        $m3 = BrochureModule::create([
            'title' => 'Intelligent Inventory & Margin Ledger',
            'subtitle' => 'Real-Time Stock Lifecycle & Supplier Inwarding',
            'badge' => 'INVENTORY ERP',
            'icon' => 'archive',
            'description' => 'Complete stock inwarding and tracking system. Track purchase costs against selling prices to protect gross margins, manage supplier bills, monitor low-stock thresholds, and perform bulk batch operations with one click.',
            'features' => [
                'Comprehensive inward stock entry linked to registered supplier profiles',
                'Purchase price vs selling price margin calculation and profit indicators',
                'Stock availability lifecycle (In Stock, Sold, Returned, Defective)',
                'Interactive stock picker modal during billing with live stock validation',
                'Bulk action bar: batch price adjustment, category updates, and deletion',
                'Brand, Category, Storage, and Model multi-filter search engine',
            ],
            'sort_order' => 3,
            'is_enabled' => true,
        ]);

        $m4 = BrochureModule::create([
            'title' => 'Customer Khata & Udhar Credit Engine',
            'subtitle' => 'Digital Ledger for Partial & Credit Billing',
            'badge' => 'CREDIT MANAGEMENT',
            'icon' => 'users',
            'description' => 'Built directly for Indian retail practices where trusted customers purchase on credit or partial down payment. Keeps a flawless, audit-ready Khata ledger tracking total invoiced, amount collected, and pending dues with scheduled due dates.',
            'features' => [
                'Unified customer profile with phone, email, GSTIN, and ID proof validation',
                'Real-time Khata ledger: Total Invoiced, Collected, and Net Outstanding Due',
                'Partial payment settlement modal with instant digital receipt generation',
                'Scheduled due dates with overdue balance alerts and settlement history',
                'Zero-dollar checkout handling: automatically creates clean credit invoices',
                'Searchable Khata balance directory with quick phone dialing and WhatsApp sharing',
            ],
            'sort_order' => 4,
            'is_enabled' => true,
        ]);

        $m5 = BrochureModule::create([
            'title' => 'Dual Printing Engine (ESC/POS & A4 Tax Bill)',
            'subtitle' => 'High-Speed Thermal Receipts & Formal Tax Invoices',
            'badge' => 'PRINT ENGINE',
            'icon' => 'printer',
            'description' => 'Flexible dual-mode document printing supporting both compact 58mm/80mm thermal receipt slips for point-of-sale speed and formal A4 PDF Tax Invoices for corporate, GST, and high-value warranty documentation.',
            'features' => [
                'High-speed 58mm & 80mm ESC/POS thermal receipt formatting',
                'One-click automated browser print trigger with zero printer setup friction',
                'Full A4 Tax Invoice PDF generation with brand logo, bank details, and signature',
                'UPI QR Code dynamically rendered on both thermal slips and A4 invoices',
                'Statutory tax declarations, custom store terms, and return policy footer',
                'Downloadable vector PDF with embedded fonts for crisp customer sharing',
            ],
            'sort_order' => 5,
            'is_enabled' => true,
        ]);

        $m6 = BrochureModule::create([
            'title' => 'Multi-Tenancy & Governance Architecture',
            'subtitle' => 'Centralized Administration & Fine-Grained RBAC',
            'badge' => 'ENTERPRISE SAAS',
            'icon' => 'shield-check',
            'description' => 'Multi-tenant infrastructure providing total data isolation between retail businesses. Super Admins have complete centralized control over store subscriptions, global users, RBAC permissions, audit logs, and transaction form fields.',
            'features' => [
                'Complete tenant database row-level isolation and security',
                'Dynamic form field governance: toggle IMEI, specs, warranty per store',
                'Granular Role-Based Access Control (Admin, Cashier, Inventory Manager)',
                'Instant emergency /lock screen for counter terminal protection',
                'Real-time maintenance mode with authorized Super Admin bypass',
                'Immutable security audit log capturing IP address, user agent, and actions',
            ],
            'sort_order' => 6,
            'is_enabled' => true,
        ]);

        // 3. Screenshots (linking real screenshots taken from live app)
        BrochureScreenshot::create([
            'module_id' => $m1->id,
            'title' => 'Point of Sale & Rapid Billing Checkout',
            'caption' => 'Cashiers configure items, toggle GST tax inclusion/exclusion, scan barcodes, and settle bills with live UPI QR payment.',
            'image_path' => 'storage/brochure/screenshots/pos_billing.png',
            'sort_order' => 1,
            'is_enabled' => true,
        ]);

        BrochureScreenshot::create([
            'module_id' => $m1->id,
            'title' => 'Store Operations & Revenue Analytics Dashboard',
            'caption' => 'Real-time sales velocity, today collections, pending Khata dues, inventory levels, and transaction charts.',
            'image_path' => 'storage/brochure/screenshots/dashboard.png',
            'sort_order' => 2,
            'is_enabled' => true,
        ]);

        BrochureScreenshot::create([
            'module_id' => $m2->id,
            'title' => 'Hardware Inventory & Dual-IMEI Tracking',
            'caption' => 'Granular inward stock inventory recording Primary IMEI, Secondary IMEI, Serial number, and warranty expiry dates.',
            'image_path' => 'storage/brochure/screenshots/inventory_items.png',
            'sort_order' => 3,
            'is_enabled' => true,
        ]);

        BrochureScreenshot::create([
            'module_id' => $m4->id,
            'title' => 'Customer Directory & Khata Credit Ledger',
            'caption' => 'Centralized customer profiles with running credit ledgers, pending due balances, and partial payment history.',
            'image_path' => 'storage/brochure/screenshots/customers_khata.png',
            'sort_order' => 4,
            'is_enabled' => true,
        ]);

        BrochureScreenshot::create([
            'module_id' => $m6->id,
            'title' => 'Super Admin Multi-Tenant Store Governance',
            'caption' => 'Centralized management of retail stores, subscription lifecycles, global staff users, and granular RBAC permissions.',
            'image_path' => 'storage/brochure/screenshots/superadmin_tenants.png',
            'sort_order' => 5,
            'is_enabled' => true,
        ]);

        BrochureScreenshot::create([
            'module_id' => $m6->id,
            'title' => 'Terminal Counter Security & Emergency Lock',
            'caption' => 'Instant counter protection screen with user avatar, masked session state, and secure PIN/password resumption.',
            'image_path' => 'storage/brochure/screenshots/terminal_lock.png',
            'sort_order' => 6,
            'is_enabled' => true,
        ]);

        // 4. Pricing Plans
        BrochurePlan::truncate();

        BrochurePlan::create([
            'name' => 'Starter Terminal',
            'price' => '₹499',
            'period' => 'per month (or ₹4,999/yr)',
            'billing_note' => 'Ideal for single retail counters & independent mobile repair shops',
            'badge' => 'ESSENTIAL',
            'is_popular' => false,
            'description' => 'Complete point-of-sale billing and hardware inventory for a single retail store counter.',
            'features' => [
                '1 Retail Store Terminal',
                'Up to 2 Staff / Cashier Accounts',
                'Dual-SIM IMEI & Serial Number Tracking',
                'Fast POS Billing with Barcode Scanner Support',
                'Standard GST Tax Calculation (Intra/Inter-State)',
                '58mm Thermal Receipt Printing',
                'Customer Directory & Khata (up to 500 customers)',
                'Standard A4 PDF Invoices with Custom Logo',
                'Standard Email Support',
            ],
            'limitations' => 'Limited to 1 store location and 2 staff logins. Thermal printing optimized for 58mm.',
            'sort_order' => 1,
            'is_enabled' => true,
        ]);

        BrochurePlan::create([
            'name' => 'Professional Retail Pro',
            'price' => '₹1,299',
            'period' => 'per month (or ₹11,999/yr)',
            'billing_note' => 'Most popular choice for growing electronics & multi-counter shops',
            'badge' => 'MOST POPULAR',
            'is_popular' => true,
            'description' => 'Advanced retail management engine with unlimited staff, thermal 80mm support, and full Khata reconciliation.',
            'features' => [
                'Up to 2 Store Outlets Included',
                'Unlimited Staff Accounts with Custom RBAC Roles',
                'Unlimited Invoices, Customers & Inventory Items',
                'Dual-SIM IMEI & Serial Hardware Provenance Logging',
                'Automated Intra-State (CGST+SGST) and Inter-State (IGST) Engine',
                'Both 58mm & 80mm ESC/POS Thermal + Full A4 Tax PDFs',
                'Dynamic UPI QR Code Generation on Every Bill',
                'Complete Customer Khata Ledger with Partial Payment Receipts',
                'Dynamic Form Field Governance (Toggle IMEI, Specs per store)',
                'Emergency /lock Screen Terminal Protection',
                'Priority Phone & WhatsApp Support (6006801960 / 6005958161)',
            ],
            'limitations' => 'Includes up to 2 physical retail branches. Additional branches can be attached at ₹499/store.',
            'sort_order' => 2,
            'is_enabled' => true,
        ]);

        BrochurePlan::create([
            'name' => 'Enterprise Franchise Suite',
            'price' => '₹2,999',
            'period' => 'per month (or ₹29,999/yr)',
            'billing_note' => 'Designed for retail chains, dealer networks & multi-store brands',
            'badge' => 'ENTERPRISE',
            'is_popular' => false,
            'description' => 'Unrestricted multi-outlet ERP suite with dedicated database isolation, custom branding, and 99.9% uptime SLA.',
            'features' => [
                'Unlimited Store Outlets & Branch Counters',
                'Dedicated Super Admin Platform Hub with Multi-Store Governance',
                'Tenant Database Isolation & Custom Domain Support',
                'Unlimited Staff, Roles, Cashiers, and Inventory SKUs',
                'Custom Invoice Colors, Signatures, and White-Label Branding',
                'Advanced Customer Khata Aging & Automated Due Overdue Alerts',
                'Complete Immutable Audit Log (IP Address, User Agent, Timestamp)',
                'Bulk CSV Data Migration & Inventory Reconciliation Engine',
                '99.9% Uptime Service Level Agreement (SLA)',
                'Dedicated 24/7 Account Executive & On-Site Setup Assistance',
            ],
            'limitations' => 'Includes dedicated setup onboarding and priority SLAs. Custom SLA agreements supported.',
            'sort_order' => 3,
            'is_enabled' => true,
        ]);
    }
}
