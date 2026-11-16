<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\RepairJob;
use App\Models\SparePart;
use App\Models\Tenant;
use App\Models\User;
use App\Services\BusinessTypeManager;
use App\Services\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessTypeWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected Tenant $sellingTenant;

    protected Tenant $repairTenant;

    protected Tenant $bothTenant;

    protected User $sellingAdmin;

    protected User $repairAdmin;

    protected User $bothAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->superAdmin = User::where('role', 'super_admin')->first()
            ?? User::factory()->create(['role' => 'super_admin']);

        // 1. Selling-only tenant
        $this->sellingTenant = Tenant::create([
            'name' => 'City Mobile Sales',
            'slug' => 'city-mobile-sales',
            'business_type' => BusinessTypeManager::TYPE_SELLING,
            'is_active' => true,
        ]);
        $this->sellingAdmin = User::create([
            'tenant_id' => $this->sellingTenant->id,
            'name' => 'Selling Admin',
            'email' => 'sales@example.com',
            'password' => bcrypt('password123'),
            'role' => 'tenant_admin',
            'is_active' => true,
        ]);

        // 2. Repair-only tenant
        $this->repairTenant = Tenant::create([
            'name' => 'Metro Mobile Repairs',
            'slug' => 'metro-mobile-repairs',
            'business_type' => BusinessTypeManager::TYPE_REPAIR,
            'repair_job_prefix' => 'REP-',
            'repair_job_counter' => 1,
            'is_active' => true,
        ]);
        $this->repairAdmin = User::create([
            'tenant_id' => $this->repairTenant->id,
            'name' => 'Repair Admin',
            'email' => 'repair@example.com',
            'password' => bcrypt('password123'),
            'role' => 'tenant_admin',
            'is_active' => true,
        ]);

        // 3. Both tenant
        $this->bothTenant = Tenant::create([
            'name' => 'Universal Mobile Care & Store',
            'slug' => 'universal-mobile-care',
            'business_type' => BusinessTypeManager::TYPE_BOTH,
            'repair_job_prefix' => 'JOB-',
            'repair_job_counter' => 1,
            'is_active' => true,
        ]);
        $this->bothAdmin = User::create([
            'tenant_id' => $this->bothTenant->id,
            'name' => 'Both Admin',
            'email' => 'both@example.com',
            'password' => bcrypt('password123'),
            'role' => 'tenant_admin',
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        TenantContext::set(null);
        parent::tearDown();
    }

    public function test_super_admin_can_create_tenant_with_business_type(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->post(route('superadmin.tenants.store'), [
            'name' => 'Speedy Phone Fix',
            'phone' => '9876543210',
            'email' => 'contact@speedyfix.com',
            'business_type' => 'repair',
            'plan' => 'pro',
            'repair_job_prefix' => 'FIX-',
            'admin_name' => 'Fix Master',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('superadmin.tenants.index'));

        $this->assertDatabaseHas('tenants', [
            'name' => 'Speedy Phone Fix',
            'business_type' => 'repair',
            'repair_job_prefix' => 'FIX-',
        ]);
    }

    public function test_super_admin_can_update_tenant_business_type(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->put(route('superadmin.tenants.update', $this->sellingTenant), [
            'name' => 'City Mobile Sales & Care',
            'email' => 'sales@example.com',
            'phone' => '9876543210',
            'plan' => 'enterprise',
            'business_type' => 'both',
            'repair_job_prefix' => 'CMS-',
        ]);

        $response->assertRedirect(route('superadmin.tenants.show', $this->sellingTenant));

        $this->sellingTenant->refresh();
        $this->assertEquals('both', $this->sellingTenant->business_type);
        $this->assertEquals('CMS-', $this->sellingTenant->repair_job_prefix);
        $this->assertTrue($this->sellingTenant->supportsRepair());
        $this->assertTrue($this->sellingTenant->supportsSelling());
    }

    public function test_selling_only_tenant_cannot_access_repair_routes(): void
    {
        TenantContext::set($this->sellingTenant);
        $this->actingAs($this->sellingAdmin);

        // Accessing repair jobs should be redirected to items
        $response = $this->get(route('repairs.index'));
        $response->assertRedirect(route('items.index'));
        $response->assertSessionHas('error');

        // Accessing spare parts should be redirected to items
        $responseSpare = $this->get(route('spare-parts.index'));
        $responseSpare->assertRedirect(route('items.index'));
        $responseSpare->assertSessionHas('error');
    }

    public function test_repair_only_tenant_cannot_access_device_inventory_routes(): void
    {
        TenantContext::set($this->repairTenant);
        $this->actingAs($this->repairAdmin);

        // Accessing device catalog should be redirected to repairs
        $response = $this->get(route('items.index'));
        $response->assertRedirect(route('repairs.index'));
        $response->assertSessionHas('error');

        // Accessing device stock management should be redirected to repairs
        $responseStock = $this->get(route('stock.index'));
        $responseStock->assertRedirect(route('repairs.index'));
        $responseStock->assertSessionHas('error');
    }

    public function test_both_tenant_can_access_both_selling_and_repair_routes(): void
    {
        TenantContext::set($this->bothTenant);
        $this->actingAs($this->bothAdmin);

        // Can access selling devices catalog
        $responseDevices = $this->get(route('items.index'));
        $responseDevices->assertOk();

        // Can access repair jobs
        $responseRepairs = $this->get(route('repairs.index'));
        $responseRepairs->assertOk();

        // Can access spare parts catalogue
        $responseSpare = $this->get(route('spare-parts.index'));
        $responseSpare->assertOk();
    }

    public function test_spare_parts_catalogue_creation_and_stock_adjustment(): void
    {
        TenantContext::set($this->repairTenant);
        $this->actingAs($this->repairAdmin);

        // Create spare part
        $response = $this->post(route('spare-parts.store'), [
            'name' => 'iPhone 13 OLED Screen Replacement',
            'category' => 'Display / Folder',
            'brand' => 'Apple',
            'compatible_models' => 'iPhone 13, iPhone 13 Pro',
            'sku' => 'SCR-IPH13-OLED',
            'barcode' => '8901234567890',
            'purchase_price' => 2800.00,
            'selling_price' => 4500.00,
            'stock_quantity' => 10,
            'min_stock_level' => 2,
            'location' => 'Bin B-12',
        ]);

        $response->assertRedirect(route('spare-parts.index'));

        $this->assertDatabaseHas('spare_parts', [
            'tenant_id' => $this->repairTenant->id,
            'name' => 'iPhone 13 OLED Screen Replacement',
            'sku' => 'SCR-IPH13-OLED',
            'stock_quantity' => 10,
        ]);

        $part = SparePart::where('tenant_id', $this->repairTenant->id)->first();
        $this->assertNotNull($part);

        // Adjust stock (add 5 units)
        $adjustResponse = $this->post(route('spare-parts.adjust', $part), [
            'adjustment_type' => 'manual_add',
            'quantity' => 5,
            'reason' => 'Received fresh shipment from vendor',
        ]);

        $adjustResponse->assertRedirect();
        $part->refresh();
        $this->assertEquals(15, $part->stock_quantity);

        $this->assertDatabaseHas('spare_part_stock_movements', [
            'spare_part_id' => $part->id,
            'type' => 'manual_add',
            'quantity_change' => 5,
            'quantity_after' => 15,
        ]);
    }

    public function test_repair_job_full_lifecycle_and_spare_parts_consumption(): void
    {
        TenantContext::set($this->repairTenant);
        $this->actingAs($this->repairAdmin);

        // 1. Prepare Customer and Spare Part
        $customer = Customer::create([
            'tenant_id' => $this->repairTenant->id,
            'name' => 'Amit Verma',
            'phone' => '9876500001',
        ]);

        $sparePart = SparePart::create([
            'tenant_id' => $this->repairTenant->id,
            'name' => 'Galaxy S22 Ultra Battery 5000mAh',
            'category' => 'Battery',
            'brand' => 'Samsung',
            'purchase_price' => 1200.00,
            'selling_price' => 2200.00,
            'stock_quantity' => 8,
            'is_active' => true,
        ]);

        // 2. Register New Repair Job with ₹500 advance
        $createResponse = $this->post(route('repairs.store'), [
            'customer_id' => $customer->id,
            'device_brand' => 'Samsung',
            'device_model' => 'Galaxy S22 Ultra',
            'imei' => '354890123456789',
            'device_color' => 'Phantom Black',
            'reported_problem' => 'Battery draining in 2 hours and device overheating',
            'device_condition' => 'Minor scuffs on frame, screen perfect',
            'priority' => 'high',
            'estimated_cost' => 3000.00,
            'advance_paid' => 500.00,
            'payment_method' => 'cash',
        ]);

        $job = RepairJob::where('tenant_id', $this->repairTenant->id)->latest()->first();
        $this->assertNotNull($job);
        $this->assertEquals('received', $job->status);
        $this->assertEquals(500.00, $job->amount_paid);

        // Verify initial activity log
        $this->assertDatabaseHas('repair_job_activities', [
            'repair_job_id' => $job->id,
            'activity_type' => 'created',
            'to_status' => 'received',
        ]);

        // 3. Add Spare Part into Repair Job (Consumes stock)
        $addPartResponse = $this->post(route('repairs.parts.add', $job), [
            'spare_part_id' => $sparePart->id,
            'quantity' => 1,
            'unit_price' => 2200.00,
        ]);

        $addPartResponse->assertRedirect();

        $sparePart->refresh();
        $this->assertEquals(7, $sparePart->stock_quantity); // Stock deducted from 8 to 7

        $this->assertDatabaseHas('spare_part_stock_movements', [
            'spare_part_id' => $sparePart->id,
            'repair_job_id' => $job->id,
            'type' => 'repair_consumed',
            'quantity_change' => -1,
            'quantity_after' => 7,
        ]);

        $job->refresh();
        $this->assertEquals(2200.00, $job->parts_total);
        $this->assertEquals(2200.00, $job->total_amount);
        $this->assertEquals(1700.00, $job->due_balance); // 2200 - 500 advance

        // 4. Update Labour and Diagnostic Charges
        $chargesResponse = $this->post(route('repairs.charges', $job), [
            'labour_charge' => 500.00,
            'additional_charges' => 100.00,
            'discount' => 100.00,
            'tax_amount' => 0.00,
        ]);

        $chargesResponse->assertRedirect();
        $job->refresh();
        $this->assertEquals(2700.00, $job->total_amount); // 2200 + 500 + 100 - 100
        $this->assertEquals(2200.00, $job->due_balance); // 2700 - 500 paid

        // 5. Update Status through Workflow
        $this->post(route('repairs.status', $job), [
            'status' => 'in_progress',
            'notes' => 'Old battery detached, bench cleaning under progress',
        ]);
        $job->refresh();
        $this->assertEquals('in_progress', $job->status);

        $this->post(route('repairs.status', $job), [
            'status' => 'completed',
            'notes' => 'New battery installed, full charge tested 100%',
        ]);
        $job->refresh();
        $this->assertEquals('completed', $job->status);
        $this->assertNotNull($job->completed_at);

        // 6. Record Final Payment Settlement
        $payResponse = $this->post(route('repairs.payments', $job), [
            'amount' => 2200.00,
            'payment_method' => 'upi',
            'notes' => 'Final settlement on pickup',
        ]);
        $payResponse->assertRedirect();
        $job->refresh();
        $this->assertEquals(2700.00, $job->amount_paid);
        $this->assertEquals(0.00, $job->due_balance);

        // 7. Mark as Delivered
        $this->post(route('repairs.status', $job), [
            'status' => 'delivered',
            'notes' => 'Device delivered to customer Amit Verma',
        ]);
        $job->refresh();
        $this->assertEquals('delivered', $job->status);
        $this->assertNotNull($job->delivered_at);

        // 8. Generate Official Repair Invoice
        $invResponse = $this->post(route('repairs.generate-invoice', $job));
        $invResponse->assertRedirect();

        $invoice = Invoice::where('repair_job_id', $job->id)->first();
        $this->assertNotNull($invoice);
        $this->assertEquals('repair', $invoice->invoice_type);
        $this->assertEquals(2700.00, (float) $invoice->grand_total);
        $this->assertEquals('paid', $invoice->payment_status);
    }

    public function test_cancelling_repair_job_restores_consumed_spare_parts_stock(): void
    {
        TenantContext::set($this->repairTenant);
        $this->actingAs($this->repairAdmin);

        $customer = Customer::create([
            'tenant_id' => $this->repairTenant->id,
            'name' => 'Pooja Sharma',
            'phone' => '9876500002',
        ]);

        $sparePart = SparePart::create([
            'tenant_id' => $this->repairTenant->id,
            'name' => 'OnePlus 10 Pro Charging Port Flex',
            'category' => 'Charging Flex',
            'brand' => 'OnePlus',
            'purchase_price' => 450.00,
            'selling_price' => 950.00,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);

        $job = RepairJob::create([
            'tenant_id' => $this->repairTenant->id,
            'customer_id' => $customer->id,
            'job_number' => 'REP-00099',
            'device_brand' => 'OnePlus',
            'device_model' => '10 Pro',
            'reported_problem' => 'Loose USB-C connector',
            'status' => 'in_progress',
        ]);

        // Consume 1 unit of charging flex
        $this->post(route('repairs.parts.add', $job), [
            'spare_part_id' => $sparePart->id,
            'quantity' => 1,
            'unit_price' => 950.00,
        ]);

        $sparePart->refresh();
        $this->assertEquals(4, $sparePart->stock_quantity);

        // Cancel repair job -> parts must be restored
        $this->post(route('repairs.status', $job), [
            'status' => 'cancelled',
            'notes' => 'Customer cancelled repair before soldering',
        ]);

        $sparePart->refresh();
        $this->assertEquals(5, $sparePart->stock_quantity); // Restored back to 5

        $this->assertDatabaseHas('spare_part_stock_movements', [
            'spare_part_id' => $sparePart->id,
            'repair_job_id' => $job->id,
            'type' => 'repair_restored',
            'quantity_change' => 1,
            'quantity_after' => 5,
        ]);
    }

    public function test_print_job_sheet_and_gate_pass_renders_successfully(): void
    {
        TenantContext::set($this->repairTenant);
        $this->actingAs($this->repairAdmin);

        $customer = Customer::create([
            'tenant_id' => $this->repairTenant->id,
            'name' => 'Rajesh Kumar',
            'phone' => '9876500003',
        ]);

        $job = RepairJob::create([
            'tenant_id' => $this->repairTenant->id,
            'customer_id' => $customer->id,
            'job_number' => 'REP-00100',
            'device_brand' => 'Google',
            'device_model' => 'Pixel 7',
            'reported_problem' => 'Camera glass cracked',
            'status' => 'completed',
            'warranty_days' => 45,
        ]);

        // Job sheet print
        $sheetResponse = $this->get(route('repairs.job-sheet', $job));
        $sheetResponse->assertOk();
        $sheetResponse->assertSee('DEVICE INTAKE JOB SHEET');
        $sheetResponse->assertSee('REP-00100');
        $sheetResponse->assertSee('Pixel 7');

        // Delivery gate pass print
        $passResponse = $this->get(route('repairs.delivery-receipt', $job));
        $passResponse->assertOk();
        $passResponse->assertSee('DEVICE DELIVERY RECEIPT');
        $passResponse->assertSee('REP-00100');
        $passResponse->assertSee('45 days warranty');
    }
}
