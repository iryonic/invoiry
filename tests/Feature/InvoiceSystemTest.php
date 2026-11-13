<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Tenant;
use App\Models\User;
use App\Services\DynamicFormConfig;
use App\Services\RbacPermissionManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InvoiceSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_page_loads_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('INVOIRY');
        $response->assertSee('Quick Evaluation Roles');
    }

    public function test_user_can_login_with_provided_credentials(): void
    {
        $response = $this->post('/login', [
            'login' => 'freetrial@gmail.com',
            'password' => 'freetrial',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_dashboard_displays_tenant_metrics(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Free Trial Electronics');
        $response->assertSee('In-Stock Phones');
        $response->assertSee('New Bill');
    }

    public function test_can_inward_new_device_to_inventory(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();

        $response = $this->actingAs($user)->post(route('items.store'), [
            'item_name' => 'Google Pixel 9 Pro',
            'brand' => 'Google',
            'storage' => '256GB',
            'color' => 'Obsidian',
            'condition' => 'new',
            'purchase_price' => 75000.00,
            'selling_price' => 84999.00,
            'warranty_status' => 'company_warranty',
            'purchase_date' => now()->format('Y-m-d'),
            'payment_method' => 'bank',
            'imei_1' => '359876543210987',
        ]);

        $response->assertRedirect(route('items.index'));
        $this->assertDatabaseHas('items', [
            'item_name' => 'Google Pixel 9 Pro',
            'imei_1' => '359876543210987',
            'status' => 'in_stock',
        ]);
    }

    public function test_can_create_invoice_and_sell_item(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $stockItem = Item::where('tenant_id', $user->tenant_id)->inStock()->first();

        $response = $this->actingAs($user)->post(route('invoices.store'), [
            'invoice_date' => now()->format('Y-m-d'),
            'payment_method' => 'upi',
            'is_gst' => 1,
            'discount' => 500.00,
            'amount_paid' => 40000.00,
            'customer_name' => 'Aaqib Lone',
            'customer_phone' => '9419123456',
            'customer_address' => 'Baramulla, J&K',
            'items' => [
                [
                    'item_id' => $stockItem->id,
                    'item_name' => $stockItem->item_name,
                    'storage' => $stockItem->storage,
                    'color' => $stockItem->color,
                    'imei_1' => $stockItem->imei_1,
                    'selling_price' => 40500.00,
                    'warranty_status' => 'store_warranty',
                ],
            ],
        ]);

        $invoice = Invoice::orderByDesc('id')->first();
        $response->assertRedirect(route('invoices.show', $invoice));

        // Ensure stock item was marked as SOLD
        $this->assertEquals('sold', $stockItem->fresh()->status);

        // Ensure invoice items and payments were created
        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'item_id' => $stockItem->id,
            'total_price' => 40500.00,
        ]);
        $this->assertDatabaseHas('payments', [
            'invoice_id' => $invoice->id,
            'amount' => 40000.00,
        ]);
    }

    public function test_can_generate_pdf_invoice(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $invoice = Invoice::where('tenant_id', $user->tenant_id)->first();

        $response = $this->actingAs($user)->get(route('invoices.pdf', $invoice));
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_can_view_thermal_receipt(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $invoice = Invoice::where('tenant_id', $user->tenant_id)->first();

        $response = $this->actingAs($user)->get(route('invoices.thermal', $invoice));
        $response->assertStatus(200);
        $response->assertSee($invoice->invoice_number);
        $response->assertSee('window.print()');
    }

    public function test_super_admin_can_impersonate_tenant(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        $tenant = Tenant::where('slug', 'freetrial')->first();

        $response = $this->actingAs($superAdmin)->get(route('superadmin.impersonate', $tenant));
        $response->assertRedirect(route('dashboard'));
        $this->assertEquals($tenant->id, session('active_tenant_id'));
    }

    public function test_multi_tenancy_isolation(): void
    {
        // Tenant 1 user
        $user1 = User::where('email', 'freetrial@gmail.com')->first();
        // Tenant 2 user
        $user2 = User::where('email', 'apex@gmail.com')->first();

        $invoice1 = Invoice::where('tenant_id', $user1->tenant_id)->first();

        // Tenant 2 user attempting to view Tenant 1's invoice should get 403 Forbidden
        $response = $this->actingAs($user2)->get(route('invoices.show', $invoice1));
        $response->assertStatus(403);
    }

    public function test_invoices_index_and_create_pages_load_successfully(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();

        $indexRes = $this->actingAs($user)->get(route('invoices.index'));
        $indexRes->assertStatus(200);
        $indexRes->assertSee('Sales Ledger');

        $createRes = $this->actingAs($user)->get(route('invoices.create'));
        $createRes->assertStatus(200);
        $createRes->assertSee('Sell Items');
        $createRes->assertSee('Taxation');
    }

    public function test_items_index_and_create_pages_load_successfully(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();

        $indexRes = $this->actingAs($user)->get(route('items.index'));
        $indexRes->assertStatus(200);
        $indexRes->assertSee('Stock Inventory');

        $createRes = $this->actingAs($user)->get(route('items.create'));
        $createRes->assertStatus(200);
        $createRes->assertSee('Inward Device');
    }

    public function test_customers_index_and_show_pages_load_successfully(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $customer = Customer::where('tenant_id', $user->tenant_id)->first();

        $indexRes = $this->actingAs($user)->get(route('customers.index'));
        $indexRes->assertStatus(200);
        $indexRes->assertSee('Customer Directory');

        $showRes = $this->actingAs($user)->get(route('customers.show', $customer));
        $showRes->assertStatus(200);
        $showRes->assertSee($customer->name);
        $showRes->assertSee('Purchase');
    }

    public function test_suppliers_index_page_loads_successfully(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();

        $response = $this->actingAs($user)->get(route('suppliers.index'));
        $response->assertStatus(200);
        $response->assertSee('Suppliers');
    }

    public function test_settings_page_loads_successfully(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();

        $response = $this->actingAs($user)->get(route('settings.index'));
        $response->assertStatus(200);
        $response->assertSee('Shop Settings');
        $response->assertSee('Store Identity');
    }

    public function test_superadmin_pages_load_successfully(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        $indexRes = $this->actingAs($superAdmin)->get(route('superadmin.index'));
        $indexRes->assertStatus(200);
        $indexRes->assertSee('Multi-Tenant Platform');

        $createRes = $this->actingAs($superAdmin)->get(route('superadmin.tenants.create'));
        $createRes->assertStatus(200);
        $createRes->assertSee('Onboard New Business Store');
    }

    public function test_superadmin_can_view_and_manage_all_tenants(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        $tenant = Tenant::where('slug', 'freetrial')->first();

        // 1. Tenants index page
        $res = $this->actingAs($superAdmin)->get(route('superadmin.tenants.index'));
        $res->assertStatus(200);
        $res->assertSee('Registered Businesses');
        $res->assertSee($tenant->name);

        // 2. Tenant show deep-dive page
        $showRes = $this->actingAs($superAdmin)->get(route('superadmin.tenants.show', $tenant));
        $showRes->assertStatus(200);
        $showRes->assertSee($tenant->name);
        $showRes->assertSee('Subscription Validity');

        // 3. Tenant edit page
        $editRes = $this->actingAs($superAdmin)->get(route('superadmin.tenants.edit', $tenant));
        $editRes->assertStatus(200);
        $editRes->assertSee('Edit '.$tenant->name);

        // 4. Extend subscription
        $extendRes = $this->actingAs($superAdmin)->post(route('superadmin.tenants.extend', $tenant), [
            'days' => 60,
        ]);
        $extendRes->assertRedirect();
        $this->assertTrue($tenant->fresh()->expires_at->isFuture());
    }

    public function test_superadmin_can_manage_global_users_and_roles(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        $tenant = Tenant::where('slug', 'freetrial')->first();

        // 1. Users index page
        $res = $this->actingAs($superAdmin)->get(route('superadmin.users.index'));
        $res->assertStatus(200);
        $res->assertSee('Global Users');

        // 2. Create user
        $createRes = $this->actingAs($superAdmin)->post(route('superadmin.users.store'), [
            'name' => 'New Regional Cashier',
            'email' => 'regcashier@test.com',
            'phone' => '9876500999',
            'role' => 'cashier',
            'tenant_id' => $tenant->id,
            'password' => 'secret123',
        ]);
        $createRes->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'regcashier@test.com', 'role' => 'cashier']);
    }

    public function test_superadmin_can_view_system_audit_logs(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        $res = $this->actingAs($superAdmin)->get(route('superadmin.audit-logs.index'));
        $res->assertStatus(200);
        $res->assertSee('Platform System Audit Logs');
    }

    public function test_superadmin_can_toggle_login_page_demo_credentials_visibility(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        // 1. Settings index page
        $res = $this->actingAs($superAdmin)->get(route('superadmin.settings.index'));
        $res->assertStatus(200);
        $res->assertSee('Platform System Settings');
        $res->assertSee('Pre-Credential');

        // 2. Turn OFF demo credentials visibility
        $updateRes = $this->actingAs($superAdmin)->put(route('superadmin.settings.update'), [
            'platform_name' => 'Enterprise POS System',
            'support_email' => 'ops@platform.com',
            'default_trial_days' => 14,
            // omitting 'show_login_demo_credentials' disables it
        ]);
        $updateRes->assertRedirect();

        // 3. Verify on login page as guest that demo credentials container is hidden
        auth()->logout();
        $loginRes = $this->get(route('login'));
        $loginRes->assertStatus(200);
        $loginRes->assertDontSee('Quick Evaluation Roles');
    }

    public function test_rbac_cashier_cannot_access_settings_or_staff_management(): void
    {
        $cashier = User::where('email', 'cashier@freetrial.com')->first();

        // Cashier trying to access settings gets 403 Forbidden
        $settingsRes = $this->actingAs($cashier)->get(route('settings.index'));
        $settingsRes->assertStatus(403);

        // Cashier trying to access staff management gets 403 Forbidden
        $staffRes = $this->actingAs($cashier)->get(route('staff.index'));
        $staffRes->assertStatus(403);

        // Cashier trying to access superadmin gets 403 Forbidden
        $superRes = $this->actingAs($cashier)->get(route('superadmin.index'));
        $superRes->assertStatus(403);
    }

    public function test_tenant_admin_can_manage_store_staff_and_roles(): void
    {
        $storeAdmin = User::where('email', 'freetrial@gmail.com')->first();

        // 1. Staff index
        $indexRes = $this->actingAs($storeAdmin)->get(route('staff.index'));
        $indexRes->assertStatus(200);
        $indexRes->assertSee('Store Staff');

        // 2. Add new cashier
        $storeRes = $this->actingAs($storeAdmin)->post(route('staff.store'), [
            'name' => 'Counter Assistant',
            'email' => 'counter@freetrial.com',
            'phone' => '9419998877',
            'role' => 'cashier',
            'password' => 'counter123',
        ]);
        $storeRes->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'counter@freetrial.com', 'role' => 'cashier']);
    }

    public function test_can_access_print_invoice_view(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $invoice = Invoice::where('tenant_id', $user->tenant_id)->first();

        $response = $this->actingAs($user)->get(route('invoices.print', $invoice));
        $response->assertStatus(200);
        $response->assertSee('Print Invoice');
        $response->assertSee($invoice->invoice_number);
        $response->assertSee('window.print()', false);
        $response->assertDontSee('ID Proof');
        $response->assertDontSee('AADHAAR');
    }

    public function test_invoice_views_do_not_contain_id_proof(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $invoice = Invoice::where('tenant_id', $user->tenant_id)->first();

        $showResponse = $this->actingAs($user)->get(route('invoices.show', $invoice));
        $showResponse->assertStatus(200);
        $showResponse->assertDontSee('ID Proof');
        $showResponse->assertDontSee('AADHAAR');

        $printResponse = $this->actingAs($user)->get(route('invoices.print', $invoice));
        $printResponse->assertStatus(200);
        $printResponse->assertDontSee('ID Proof');
        $printResponse->assertDontSee('AADHAAR');
    }

    public function test_can_download_invoice_pdf(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $invoice = Invoice::where('tenant_id', $user->tenant_id)->first();

        $response = $this->actingAs($user)->get(route('invoices.pdf', $invoice));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_can_update_invoice_branding_customizations(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();

        $response = $this->actingAs($user)->put(route('settings.update'), [
            'name' => 'Free Trial Electronics Updated',
            'invoice_prefix' => 'FTE-',
            'invoice_title' => 'TAX INVOICE & CASH MEMO',
            'invoice_color' => '#101010',
            'invoice_footer_note' => 'Thank you for shopping with us! No returns after 7 days.',
            'upi_id' => 'freetrial@okhdfcbank',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tenants', [
            'id' => $user->tenant_id,
            'name' => 'Free Trial Electronics Updated',
            'invoice_prefix' => 'FTE-',
            'invoice_title' => 'TAX INVOICE & CASH MEMO',
            'invoice_color' => '#101010',
            'invoice_footer_note' => 'Thank you for shopping with us! No returns after 7 days.',
        ]);
    }

    public function test_admin_can_upload_store_logo_for_invoice(): void
    {
        Storage::fake('public');

        $user = User::where('email', 'freetrial@gmail.com')->first();
        $logoFile = UploadedFile::fake()->image('test-logo.png', 120, 120);

        $response = $this->actingAs($user)->put(route('settings.update'), [
            'name' => 'Free Trial Electronics',
            'logo' => $logoFile,
        ]);

        $response->assertRedirect();

        $tenant = $user->tenant()->first();
        $this->assertNotNull($tenant->logo);
        Storage::disk('public')->assertExists($tenant->logo);

        // Verify the invoice view renders with the new logo
        $invoice = Invoice::where('tenant_id', $tenant->id)->first();
        $invoiceResponse = $this->actingAs($user)->get(route('invoices.show', $invoice));
        $invoiceResponse->assertStatus(200);
        $invoiceResponse->assertSee($tenant->logo_url);
    }

    public function test_can_configure_store_default_gst_settings(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();

        $response = $this->actingAs($user)->put(route('settings.update'), [
            'name' => 'Free Trial Electronics',
            'default_gst_rate' => 12.00,
            'is_tax_inclusive' => 1,
            'state_code' => '01',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tenants', [
            'id' => $user->tenant_id,
            'default_gst_rate' => 12.00,
            'is_tax_inclusive' => 1,
            'state_code' => '01',
        ]);
    }

    public function test_can_create_invoice_with_custom_gst_rate_and_tax_exclusive(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $stockItem = Item::where('tenant_id', $user->tenant_id)->inStock()->first();

        $response = $this->actingAs($user)->post(route('invoices.store'), [
            'invoice_date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'is_gst' => 1,
            'tax_rate' => 12.00,
            'is_tax_inclusive' => 0, // Base + 12% GST
            'discount' => 0.00,
            'customer_name' => 'Tariq Ahmad',
            'customer_phone' => '9876543210',
            'customer_state_code' => '01', // Intra-state (same as store 01)
            'items' => [
                [
                    'item_id' => $stockItem->id,
                    'item_name' => $stockItem->item_name,
                    'selling_price' => 10000.00,
                    'warranty_status' => 'no_warranty',
                ],
            ],
        ]);

        $invoice = Invoice::orderByDesc('id')->first();
        $response->assertRedirect(route('invoices.show', $invoice));

        // Tax exclusive 12% on 10,000 => subtotal: 10000, tax: 1200, grand total: 11200
        $this->assertEquals(10000.00, (float) $invoice->subtotal);
        $this->assertEquals(1200.00, (float) $invoice->tax_amount);
        $this->assertEquals(11200.00, (float) $invoice->grand_total);
        $this->assertEquals(12.00, (float) $invoice->tax_rate);
        $this->assertFalse((bool) $invoice->is_tax_inclusive);

        // Intra-state split: 6% CGST + 6% SGST, 0 IGST
        $this->assertEquals(600.00, (float) $invoice->cgst_amount);
        $this->assertEquals(600.00, (float) $invoice->sgst_amount);
        $this->assertEquals(0.00, (float) $invoice->igst_amount);
        $this->assertFalse($invoice->isInterState());
    }

    public function test_can_create_inter_state_invoice_with_igst_calculation(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $stockItem = Item::where('tenant_id', $user->tenant_id)->inStock()->first();

        $response = $this->actingAs($user)->post(route('invoices.store'), [
            'invoice_date' => now()->format('Y-m-d'),
            'payment_method' => 'bank',
            'is_gst' => 1,
            'tax_rate' => 18.00,
            'is_tax_inclusive' => 1, // MRP Inclusive 18%
            'discount' => 0.00,
            'customer_name' => 'Delhi Buyer Corp',
            'customer_phone' => '9111222333',
            'customer_state' => 'Delhi',
            'customer_state_code' => '07', // Inter-state (store is 01)
            'items' => [
                [
                    'item_id' => $stockItem->id,
                    'item_name' => $stockItem->item_name,
                    'selling_price' => 11800.00,
                    'warranty_status' => 'no_warranty',
                ],
            ],
        ]);

        $invoice = Invoice::orderByDesc('id')->first();
        $response->assertRedirect(route('invoices.show', $invoice));

        // MRP Inclusive 18% on 11,800 => subtotal: 10000, tax: 1800, grand total: 11800
        $this->assertEquals(10000.00, (float) $invoice->subtotal);
        $this->assertEquals(1800.00, (float) $invoice->tax_amount);
        $this->assertEquals(11800.00, (float) $invoice->grand_total);
        $this->assertTrue((bool) $invoice->is_tax_inclusive);

        // Inter-state: Full IGST 1800, 0 CGST, 0 SGST
        $this->assertEquals(0.00, (float) $invoice->cgst_amount);
        $this->assertEquals(0.00, (float) $invoice->sgst_amount);
        $this->assertEquals(1800.00, (float) $invoice->igst_amount);
        $this->assertTrue($invoice->isInterState());

        // Verify show page displays IGST row and rate
        $showResponse = $this->actingAs($user)->get(route('invoices.show', $invoice));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Integrated Tax (IGST)');
        $showResponse->assertSee('1,800.00');
    }

    public function test_superadmin_users_index_displays_edit_and_permissions_buttons(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        $targetUser = User::where('role', 'cashier')->first();

        $response = $this->actingAs($superAdmin)->get(route('superadmin.users.index'));
        $response->assertStatus(200);
        $response->assertSee(route('superadmin.users.edit', $targetUser));
        $response->assertSee(route('superadmin.users.permissions', $targetUser));
    }

    public function test_superadmin_can_edit_user_account_details(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        $targetUser = User::where('role', 'cashier')->first();

        // View edit page
        $editPage = $this->actingAs($superAdmin)->get(route('superadmin.users.edit', $targetUser));
        $editPage->assertStatus(200);
        $editPage->assertSee('Edit Account: '.$targetUser->name);
        $editPage->assertSee(route('superadmin.users.permissions', $targetUser));

        // Submit update
        $response = $this->actingAs($superAdmin)->put(route('superadmin.users.update', $targetUser), [
            'name' => 'Updated Cashier Name',
            'email' => $targetUser->email,
            'phone' => '9988776655',
            'role' => 'cashier',
            'tenant_id' => $targetUser->tenant_id,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('superadmin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Updated Cashier Name',
            'phone' => '9988776655',
        ]);
    }

    public function test_superadmin_can_configure_user_granular_permissions(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        $cashier = User::where('role', 'cashier')->first();

        // View permissions page
        $permPage = $this->actingAs($superAdmin)->get(route('superadmin.users.permissions', $cashier));
        $permPage->assertStatus(200);
        $permPage->assertSee('Permissions: '.$cashier->name);

        // Update permissions for cashier: explicitly allow items.create
        $response = $this->actingAs($superAdmin)->put(route('superadmin.users.permissions.update', $cashier), [
            'permissions' => [
                'items' => ['view' => 1, 'create' => 1, 'edit' => 0, 'delete' => 0],
                'invoices' => ['view' => 1, 'create' => 1, 'edit' => 0, 'delete' => 0, 'print' => 1, 'record_payments' => 1],
            ],
        ]);

        $response->assertRedirect();
        $cashier->refresh();

        $this->assertIsArray($cashier->permissions);
        $this->assertTrue($cashier->hasPermission('items.create'));
        $this->assertFalse($cashier->hasPermission('items.delete'));
    }

    public function test_user_can_reset_permissions_to_store_defaults(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        $cashier = User::where('role', 'cashier')->first();

        // Give custom permissions first
        $cashier->update(['permissions' => ['invoices' => ['view' => 1]]]);
        $this->assertNotNull($cashier->permissions);

        // Reset to store defaults
        $response = $this->actingAs($superAdmin)->put(route('superadmin.users.permissions.update', $cashier), [
            'reset_to_default' => 1,
        ]);

        $response->assertRedirect();
        $cashier->refresh();
        $this->assertNull($cashier->permissions);
    }

    public function test_tenant_form_fields_configuration_hides_disabled_inputs_on_tenant_forms(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $tenant = $user->tenant;

        // Default state: customer_phone is visible on invoice create
        $response = $this->actingAs($user)->get(route('invoices.create'));
        $response->assertStatus(200);
        $response->assertSee('name="customer_phone"', false);

        // Disable customer_phone in tenant's form_fields configuration
        $formFields = DynamicFormConfig::getDefaultConfig();
        $formFields['invoice']['customer_phone']['visible'] = false;
        $formFields['invoice']['customer_phone']['required'] = false;
        $tenant->update(['form_fields' => $formFields]);

        // Form should now not display customer_phone
        $response = $this->actingAs($user)->get(route('invoices.create'));
        $response->assertStatus(200);
        $response->assertDontSee('name="customer_phone"', false);

        // Test items create form with brand disabled
        $formFields['item']['brand']['visible'] = false;
        $tenant->update(['form_fields' => $formFields]);

        $itemCreateResponse = $this->actingAs($user)->get(route('items.create'));
        $itemCreateResponse->assertStatus(200);
        $itemCreateResponse->assertDontSee('name="brand"', false);
    }

    public function test_superadmin_can_update_tenant_form_fields_via_permissions_page(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $tenant = $user->tenant;

        // Superadmin opens tenant permissions page
        $pageResponse = $this->actingAs($superAdmin)->get(route('superadmin.tenants.permissions', $tenant));
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Form Fields');
        $pageResponse->assertSee('Create Bill & Invoice Form');

        // Superadmin submits updated form_fields with invoice customer_email disabled
        $updateResponse = $this->actingAs($superAdmin)->put(route('superadmin.tenants.permissions.update', $tenant), [
            'permissions' => $tenant->permissions ?? RbacPermissionManager::getDefaultPermissions(),
            'form_fields' => [
                'invoice' => [
                    'customer_name' => ['visible' => 1, 'required' => 1],
                    'customer_phone' => ['visible' => 1, 'required' => 0],
                    'customer_email' => ['visible' => 0, 'required' => 0], // disabled
                ],
            ],
        ]);

        $updateResponse->assertRedirect();
        $tenant->refresh();

        $this->assertFalse((bool) $tenant->form_fields['invoice']['customer_email']['visible']);

        // Check that tenant no longer sees customer_email input
        $tenantResponse = $this->actingAs($user)->get(route('invoices.create'));
        $tenantResponse->assertStatus(200);
        $tenantResponse->assertDontSee('name="customer_email"', false);
    }

    public function test_customer_khata_payment_can_be_recorded(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $customer = Customer::where('tenant_id', $user->tenant_id)->first();

        // Create an invoice with partial payment to have a pending balance
        $invoice = Invoice::create([
            'tenant_id' => $user->tenant_id,
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-TEST-KHATA',
            'invoice_date' => now(),
            'subtotal' => 10000,
            'tax_amount' => 0,
            'discount' => 0,
            'grand_total' => 10000,
            'amount_paid' => 6000,
            'payment_status' => 'partial',
            'payment_method' => 'cash',
        ]);

        $this->assertEquals(4000.0, $customer->pendingBalance());

        // Record a settlement payment of 2000
        $response = $this->actingAs($user)->post(route('customers.settle', $customer), [
            'amount' => 2000,
            'payment_method' => 'upi',
            'payment_date' => now()->format('Y-m-d'),
            'reference_id' => 'UPI-123456789',
            'notes' => 'Counter settlement',
        ]);

        $response->assertRedirect();
        $invoice->refresh();

        $this->assertEquals(8000.0, (float) $invoice->amount_paid);
        $this->assertEquals('partial', $invoice->payment_status);
        $this->assertEquals(2000.0, $customer->pendingBalance());
    }

    public function test_customer_profile_can_be_updated(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $customer = Customer::where('tenant_id', $user->tenant_id)->first();

        $response = $this->actingAs($user)->put(route('customers.update', $customer), [
            'name' => 'Updated Customer Name',
            'phone' => '9419998877',
            'email' => 'updated@client.com',
            'address' => 'Residency Road, Srinagar',
            'city' => 'Srinagar',
            'state' => 'Jammu and Kashmir',
            'gstin' => '01ABCDE9999F1Z5',
            'notes' => 'VIP Khata member',
        ]);

        $response->assertRedirect();
        $customer->refresh();

        $this->assertEquals('Updated Customer Name', $customer->name);
        $this->assertEquals('9419998877', $customer->phone);
        $this->assertEquals('VIP Khata member', $customer->notes);
    }

    public function test_dual_sim_cross_column_duplicate_imei_is_prevented(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $existing = Item::where('tenant_id', $user->tenant_id)->inStock()->first();

        // Attempting to inward a phone whose IMEI 1 matches an existing phone's IMEI 1
        $res1 = $this->actingAs($user)->post(route('items.store'), [
            'item_name' => 'Duplicate IMEI Phone',
            'condition' => 'new',
            'purchase_price' => 50000,
            'imei_1' => $existing->imei_1,
        ]);
        $res1->assertSessionHas('error');

        // Attempting to inward a phone where IMEI 2 matches an existing phone's IMEI 1
        $res2 = $this->actingAs($user)->post(route('items.store'), [
            'item_name' => 'Duplicate IMEI 2 Phone',
            'condition' => 'new',
            'purchase_price' => 50000,
            'imei_1' => '999988887777666',
            'imei_2' => $existing->imei_1,
        ]);
        $res2->assertSessionHas('error');
    }

    public function test_bulk_delete_items_protects_sold_and_invoiced_devices(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $soldItem = Item::where('tenant_id', $user->tenant_id)->sold()->first();
        $inStockItem = Item::where('tenant_id', $user->tenant_id)->inStock()->first();

        $this->assertNotNull($soldItem);
        $this->assertNotNull($inStockItem);

        $response = $this->actingAs($user)->post(route('items.bulk'), [
            'ids' => [$soldItem->id, $inStockItem->id],
            'action' => 'delete',
        ]);

        $response->assertRedirect();
        // Sold item must still exist in DB
        $this->assertDatabaseHas('items', ['id' => $soldItem->id]);
        // In-stock item was deleted
        $this->assertDatabaseMissing('items', ['id' => $inStockItem->id]);
    }

    public function test_mobile_bottom_nav_renders_superadmin_controls_for_superadmin(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        $response = $this->actingAs($superAdmin)->get(route('superadmin.index'));

        $response->assertStatus(200);
        $response->assertSee('Console');
        $response->assertSee('Tenants');
        $response->assertSee('+ Tenant');
    }
}
