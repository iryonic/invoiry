<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Item;
use App\Models\Payment;
use App\Models\Supplier;
use App\Models\SystemSetting;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. Platform System Settings
        SystemSetting::set('show_login_demo_credentials', '1', 'login');
        SystemSetting::set('platform_name', 'INVOIRY', 'general');
        SystemSetting::set('support_email', 'support@invoiry.com', 'general');
        SystemSetting::set('support_phone', '+91 7006079473', 'general');
        SystemSetting::set('default_trial_days', '30', 'subscription');
        SystemSetting::set('maintenance_mode', '0', 'system');
        SystemSetting::set('announcement_banner', 'System operational. Multi-tenant retail terminal active.', 'system');
        SystemSetting::set('app_version', 'v1.0.0', 'general');
        SystemSetting::set('footer_enabled', '1', 'footer');
        SystemSetting::set('footer_copyright', '© {year} INVOIRY', 'footer');

        // 1. Super Admin
        User::create([
            'tenant_id' => null,
            'name' => 'Super Administrator',
            'email' => 'superadmin@invoice.com',
            'phone' => '9999999999',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        // 2. Tenant 1: Free Trial
        $tenant1 = Tenant::create([
            'name' => 'Free Trial Electronics',
            'slug' => 'freetrial',
            'phone' => '+91 7006079473',
            'email' => 'freetrial@gmail.com',
            'address' => 'Shop #14, City Centre Complex, Residency Road',
            'city' => 'Srinagar',
            'state' => 'Jammu & Kashmir',
            'state_code' => '01',
            'gstin' => '01ABCDE1234F1Z5',
            'upi_id' => 'freetrial@okhdfc',
            'bank_name' => 'HDFC Bank',
            'bank_account_no' => '50200070301356',
            'bank_ifsc' => 'HDFC0000357',
            'bank_branch' => 'Residency Road Branch',
            'invoice_prefix' => 'INV-',
            'invoice_counter' => 104,
            'terms_conditions' => "1. Goods once sold will not be taken back or refunded.\n2. Warranty claims are subject to company/store policy norms.\n3. Water damage or physical breakage is not covered under warranty.",
            'declaration' => 'We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.',
            'plan' => 'pro',
            'expires_at' => now()->addDays(30),
            'is_active' => true,
        ]);

        // Tenant 1 Admin User (exact credentials from user prompt!)
        $user1 = User::create([
            'tenant_id' => $tenant1->id,
            'name' => 'Free Trial Admin',
            'email' => 'freetrial@gmail.com',
            'phone' => '7006079473',
            'password' => Hash::make('freetrial'),
            'role' => 'tenant_admin',
            'is_active' => true,
        ]);

        // Tenant 1 Cashier User
        User::create([
            'tenant_id' => $tenant1->id,
            'name' => 'Store Cashier',
            'email' => 'cashier@freetrial.com',
            'phone' => '7006079470',
            'password' => Hash::make('freetrial'),
            'role' => 'cashier',
            'is_active' => true,
        ]);

        // Suppliers for Tenant 1
        $sup1 = Supplier::create([
            'tenant_id' => $tenant1->id,
            'name' => 'Apple Regional Wholesale Hub',
            'phone' => '9876540001',
            'email' => 'applehub@supplier.com',
            'id_proof_type' => 'GSTIN',
            'id_proof_number' => '01APL9876Q1Z9',
            'address' => 'Industrial Area, Phase 2, Srinagar',
            'company_name' => 'Apple Distro India',
        ]);

        $sup2 = Supplier::create([
            'tenant_id' => $tenant1->id,
            'name' => 'Tariq Gadgets Import',
            'phone' => '9876540002',
            'email' => 'tariq@gadgets.com',
            'id_proof_type' => 'Aadhaar',
            'id_proof_number' => '5432-8765-1234',
            'address' => 'Lal Chowk, Srinagar',
        ]);

        // Customers for Tenant 1
        $cust1 = Customer::create([
            'tenant_id' => $tenant1->id,
            'name' => 'Muhammad Basit',
            'phone' => '7006079473',
            'email' => 'bhatbasi09@gmail.com',
            'id_proof_type' => 'Aadhaar',
            'id_proof_number' => '8976-5432-1098',
            'address' => 'Soura, Srinagar, J&K',
            'city' => 'Srinagar',
            'state' => 'Jammu & Kashmir',
            'state_code' => '01',
        ]);

        $cust2 = Customer::create([
            'tenant_id' => $tenant1->id,
            'name' => 'Haris Malik',
            'phone' => '6006959565',
            'email' => 'haris@malik.in',
            'id_proof_type' => 'Aadhaar',
            'id_proof_number' => '3210-9876-5432',
            'address' => 'Rajbagh, Srinagar, J&K',
            'city' => 'Srinagar',
            'state' => 'Jammu & Kashmir',
            'state_code' => '01',
        ]);

        $cust3 = Customer::create([
            'tenant_id' => $tenant1->id,
            'name' => 'Furqan Ahmad',
            'phone' => '9419012345',
            'email' => 'furqan@gmail.com',
            'id_proof_type' => 'Aadhaar',
            'id_proof_number' => '4567-8901-2345',
            'address' => 'Hyderpora, Srinagar, J&K',
            'city' => 'Srinagar',
            'state' => 'Jammu & Kashmir',
            'state_code' => '01',
        ]);

        // In-Stock Inventory Items for Tenant 1
        Item::create([
            'tenant_id' => $tenant1->id,
            'supplier_id' => $sup1->id,
            'item_name' => 'iPhone 16 Pro',
            'brand' => 'Apple',
            'category' => 'Smartphones',
            'storage' => '256GB',
            'color' => 'Desert Titanium',
            'specs' => 'A18 Pro chip, 8GB RAM, Camera Control button',
            'imei_1' => '864721060123456',
            'imei_2' => '864721060123457',
            'serial_number' => 'F2LWX890K',
            'condition' => 'new',
            'status' => 'in_stock',
            'purchase_price' => 112000.00,
            'selling_price' => 124900.00,
            'warranty_status' => 'company_warranty',
            'warranty_expiry_date' => now()->addYear(),
            'purchase_date' => now()->subDays(5),
            'payment_method' => 'bank',
        ]);

        Item::create([
            'tenant_id' => $tenant1->id,
            'supplier_id' => $sup1->id,
            'item_name' => 'Samsung Galaxy S24 Ultra',
            'brand' => 'Samsung',
            'category' => 'Smartphones',
            'storage' => '512GB',
            'color' => 'Titanium Gray',
            'specs' => 'Snapdragon 8 Gen 3, 12GB RAM, S-Pen included',
            'imei_1' => '354789012345678',
            'imei_2' => '354789012345679',
            'serial_number' => 'R5CW1098KLM',
            'condition' => 'new',
            'status' => 'in_stock',
            'purchase_price' => 118000.00,
            'selling_price' => 129999.00,
            'warranty_status' => 'company_warranty',
            'warranty_expiry_date' => now()->addYear(),
            'purchase_date' => now()->subDays(7),
            'payment_method' => 'bank',
        ]);

        Item::create([
            'tenant_id' => $tenant1->id,
            'supplier_id' => $sup2->id,
            'item_name' => 'iPhone 14',
            'brand' => 'Apple',
            'category' => 'Smartphones',
            'storage' => '128GB',
            'color' => 'Midnight Blue',
            'specs' => 'Battery Health 94%, pristine condition with box',
            'imei_1' => '869876543210987',
            'imei_2' => '869876543210988',
            'serial_number' => 'G6TMN3421B',
            'condition' => 'used',
            'status' => 'in_stock',
            'purchase_price' => 38000.00,
            'selling_price' => 44500.00,
            'warranty_status' => 'store_warranty',
            'warranty_expiry_date' => now()->addMonths(6),
            'purchase_date' => now()->subDays(2),
            'payment_method' => 'upi',
        ]);

        Item::create([
            'tenant_id' => $tenant1->id,
            'supplier_id' => $sup2->id,
            'item_name' => 'OnePlus 12',
            'brand' => 'OnePlus',
            'category' => 'Smartphones',
            'storage' => '256GB',
            'color' => 'Emerald Green',
            'specs' => '16GB RAM, 100W SuperVOOC charger in box',
            'imei_1' => '861234567890123',
            'imei_2' => '861234567890124',
            'serial_number' => 'OP12EMG871',
            'condition' => 'refurbished',
            'status' => 'in_stock',
            'purchase_price' => 42000.00,
            'selling_price' => 49000.00,
            'warranty_status' => 'store_warranty',
            'warranty_expiry_date' => now()->addMonths(3),
            'purchase_date' => now()->subDays(4),
            'payment_method' => 'cash',
        ]);

        // Sold Items & Generated Invoices
        $soldPhone1 = Item::create([
            'tenant_id' => $tenant1->id,
            'supplier_id' => $sup1->id,
            'item_name' => 'iPhone 16',
            'brand' => 'Apple',
            'category' => 'Smartphones',
            'storage' => '128GB',
            'color' => 'Black',
            'specs' => 'A18 chip, Super Retina XDR display',
            'imei_1' => '864721056884832',
            'imei_2' => '864721056884824',
            'serial_number' => 'K9P0128BLK',
            'condition' => 'new',
            'status' => 'sold',
            'purchase_price' => 68000.00,
            'selling_price' => 74900.00,
            'warranty_status' => 'company_warranty',
            'warranty_expiry_date' => now()->addYear(),
            'purchase_date' => now()->subDays(10),
            'payment_method' => 'bank',
        ]);

        $inv1 = Invoice::create([
            'tenant_id' => $tenant1->id,
            'customer_id' => $cust1->id,
            'user_id' => $user1->id,
            'invoice_number' => 'INV-00101',
            'invoice_date' => now()->subDays(3),
            'due_date' => now()->subDays(3),
            'subtotal' => 74900.00,
            'discount' => 900.00,
            'tax_amount' => 11288.14,
            'cgst_amount' => 5644.07,
            'sgst_amount' => 5644.07,
            'igst_amount' => 0.00,
            'grand_total' => 74000.00,
            'amount_paid' => 74000.00,
            'payment_method' => 'upi',
            'payment_status' => 'paid',
            'is_gst' => true,
            'notes' => 'Customer opted for UPI scan at counter. Original box handed over.',
        ]);

        InvoiceItem::create([
            'invoice_id' => $inv1->id,
            'item_id' => $soldPhone1->id,
            'item_name' => 'iPhone 16',
            'storage' => '128GB',
            'color' => 'Black',
            'specs' => 'A18 chip, Dual Camera',
            'imei_1' => '864721056884832',
            'imei_2' => '864721056884824',
            'serial_number' => 'K9P0128BLK',
            'hsn_sac' => '8517',
            'quantity' => 1,
            'unit_price' => 74900.00,
            'purchase_cost' => 68000.00,
            'tax_rate' => 18.00,
            'tax_amount' => 11288.14,
            'total_price' => 74000.00,
            'warranty_status' => 'company_warranty',
            'warranty_expiry_date' => now()->addYear(),
            'notes' => '1 Year Apple Official Warranty',
        ]);

        Payment::create([
            'tenant_id' => $tenant1->id,
            'invoice_id' => $inv1->id,
            'customer_id' => $cust1->id,
            'amount' => 74000.00,
            'payment_method' => 'upi',
            'reference_id' => 'UPI/432198765432',
            'payment_date' => now()->subDays(3),
            'notes' => 'Paid via GPay UPI',
        ]);

        // Invoice 2: Furqan (Partial payment / Credit)
        $soldPhone2 = Item::create([
            'tenant_id' => $tenant1->id,
            'supplier_id' => $sup2->id,
            'item_name' => 'iPhone 13',
            'brand' => 'Apple',
            'category' => 'Smartphones',
            'storage' => '128GB',
            'color' => 'Starlight White',
            'specs' => '88% Battery Health, Charger and Cable provided',
            'imei_1' => '862098765432109',
            'imei_2' => '862098765432110',
            'serial_number' => 'H7TYU9812A',
            'condition' => 'used',
            'status' => 'sold',
            'purchase_price' => 28000.00,
            'selling_price' => 34000.00,
            'warranty_status' => 'store_warranty',
            'warranty_expiry_date' => now()->addMonths(6),
            'purchase_date' => now()->subDays(15),
            'payment_method' => 'cash',
        ]);

        $inv2 = Invoice::create([
            'tenant_id' => $tenant1->id,
            'customer_id' => $cust3->id,
            'user_id' => $user1->id,
            'invoice_number' => 'INV-00102',
            'invoice_date' => now()->subDay(),
            'due_date' => now()->addDays(7),
            'subtotal' => 34000.00,
            'discount' => 0.00,
            'tax_amount' => 0.00,
            'grand_total' => 34000.00,
            'amount_paid' => 20000.00,
            'payment_method' => 'cash',
            'payment_status' => 'partial',
            'is_gst' => false,
            'notes' => 'Customer paid ₹20,000 cash; balance ₹14,000 due next week.',
        ]);

        InvoiceItem::create([
            'invoice_id' => $inv2->id,
            'item_id' => $soldPhone2->id,
            'item_name' => 'iPhone 13',
            'storage' => '128GB',
            'color' => 'Starlight White',
            'specs' => '88% Battery Health',
            'imei_1' => '862098765432109',
            'imei_2' => '862098765432110',
            'serial_number' => 'H7TYU9812A',
            'hsn_sac' => '8517',
            'quantity' => 1,
            'unit_price' => 34000.00,
            'purchase_cost' => 28000.00,
            'tax_rate' => 0.00,
            'tax_amount' => 0.00,
            'total_price' => 34000.00,
            'warranty_status' => 'store_warranty',
            'warranty_expiry_date' => now()->addMonths(6),
            'notes' => '6 Months Store Testing Warranty',
        ]);

        Payment::create([
            'tenant_id' => $tenant1->id,
            'invoice_id' => $inv2->id,
            'customer_id' => $cust3->id,
            'amount' => 20000.00,
            'payment_method' => 'cash',
            'reference_id' => 'CASH-REC-102',
            'payment_date' => now()->subDay(),
            'notes' => 'Partial cash advance',
        ]);

        // 3. Tenant 2: Apex Tech Studio (To prove multi-tenancy isolation!)
        $tenant2 = Tenant::create([
            'name' => 'Apex Tech Studio',
            'slug' => 'apextech',
            'phone' => '+91 98765 11223',
            'email' => 'apex@gmail.com',
            'address' => 'Unit 402, High Street Mall',
            'city' => 'Delhi',
            'state' => 'Delhi',
            'state_code' => '07',
            'gstin' => '07APEX9988Z1Q2',
            'upi_id' => 'apextech@icici',
            'bank_name' => 'HDFC Bank',
            'bank_account_no' => '50100234567890',
            'bank_ifsc' => 'HDFC0001234',
            'bank_branch' => 'High Street Mall',
            'invoice_prefix' => 'APEX-',
            'invoice_counter' => 1,
            'terms_conditions' => 'Standard terms apply. 7-day replacement.',
            'plan' => 'enterprise',
            'is_active' => true,
        ]);

        User::create([
            'tenant_id' => $tenant2->id,
            'name' => 'Apex Store Manager',
            'email' => 'apex@gmail.com',
            'phone' => '9876511223',
            'password' => Hash::make('password'),
            'role' => 'tenant_admin',
            'is_active' => true,
        ]);

        // Initial Audit Logs
        AuditLog::create([
            'user_id' => 1,
            'tenant_id' => null,
            'event' => 'system.initialized',
            'description' => 'System seed completed. Multi-tenant database initialized.',
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subHours(2),
        ]);

        AuditLog::create([
            'user_id' => 1,
            'tenant_id' => $tenant1->id,
            'event' => 'tenant.created',
            'description' => 'Store "Free Trial Electronics" created with Pro Plan.',
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subHour(),
        ]);

        $this->call(EmailTemplateSeeder::class);
    }
}
