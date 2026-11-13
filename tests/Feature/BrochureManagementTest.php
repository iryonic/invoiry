<?php

namespace Tests\Feature;

use App\Models\BrochureGeneration;
use App\Models\BrochureModule;
use App\Models\BrochurePlan;
use App\Models\BrochureScreenshot;
use App\Models\BrochureSetting;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\BrochureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrochureManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $regularUser;

    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(BrochureSeeder::class);

        $this->tenant = Tenant::firstOrCreate(
            ['slug' => 'test-brochure-store'],
            [
                'name' => 'Brochure Test Electronics',
                'email' => 'brochure@store.com',
                'phone' => '9876543210',
                'plan' => 'enterprise',
                'is_active' => true,
            ]
        );

        $this->superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@invoice.com'],
            [
                'name' => 'Platform Super Admin',
                'password' => bcrypt('password'),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        $this->regularUser = User::firstOrCreate(
            ['email' => 'cashier@teststore.com'],
            [
                'name' => 'Store Cashier',
                'password' => bcrypt('password'),
                'role' => 'cashier',
                'tenant_id' => $this->tenant->id,
                'is_active' => true,
            ]
        );
    }

    /**
     * Test guests cannot access the brochure management dashboard.
     */
    public function test_guests_cannot_access_brochure_management(): void
    {
        $response = $this->get(route('superadmin.brochure.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test regular tenant users cannot access brochure management.
     */
    public function test_regular_users_cannot_access_brochure_management(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('superadmin.brochure.index'));
        $response->assertStatus(403);
    }

    /**
     * Test super admin can access brochure management view.
     */
    public function test_super_admin_can_access_brochure_management(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.brochure.index'));

        $response->assertOk();
        $response->assertSee('Features &amp; Pricing PDF Management', false);
        $response->assertSee('INVOIRY');
        $response->assertSee('6006801960');
        $response->assertSee('hire.iry@gmail.com');
        $response->assertSee('Rapid POS Billing');
        $response->assertSee('Dual-SIM IMEI');
    }

    /**
     * Test super admin can update brochure general settings.
     */
    public function test_super_admin_can_update_brochure_settings(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('superadmin.brochure.settings.update'), [
            'product_name' => 'INVOIRY ENTERPRISE',
            'product_tagline' => 'Updated Commercial Tagline',
            'document_title' => 'INVOIRY Features Document 2026',
            'version' => '2.5.0',
            'hero_headline' => 'Leading Retail POS for Electronics',
            'contact_phone_1' => '6006801960',
            'contact_phone_2' => '6005958161',
            'contact_email' => 'hire.iry@gmail.com',
            'show_cover' => '1',
            'show_overview' => '1',
            'show_modules' => '1',
            'show_pricing' => '1',
            'show_contact' => '1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('INVOIRY ENTERPRISE', BrochureSetting::get('product_name'));
        $this->assertEquals('2.5.0', BrochureSetting::get('version'));
        $this->assertEquals('6006801960', BrochureSetting::get('contact_phone_1'));
    }

    /**
     * Test super admin can create, update and delete modules.
     */
    public function test_super_admin_can_manage_modules(): void
    {
        // 1. Create module
        $createResponse = $this->actingAs($this->superAdmin)->post(route('superadmin.brochure.modules.store'), [
            'title' => 'AI Inventory Forecast',
            'subtitle' => 'Machine Learning Stock Analysis',
            'badge' => 'PREDICTIVE',
            'description' => 'Predicts replenishment schedules based on seasonal sales patterns.',
            'features_text' => "Automated reorder alerts\nSupplier lead time tracking\nDead stock detection",
            'sort_order' => 10,
            'is_enabled' => '1',
        ]);

        $createResponse->assertRedirect();
        $createResponse->assertSessionHas('success');

        $module = BrochureModule::where('title', 'AI Inventory Forecast')->first();
        $this->assertNotNull($module);
        $this->assertCount(3, $module->features);

        // 2. Update module
        $updateResponse = $this->actingAs($this->superAdmin)->put(route('superadmin.brochure.modules.update', $module), [
            'title' => 'AI Inventory & Demand Forecasting',
            'subtitle' => 'Smart Demand Insights',
            'badge' => 'AI ERP',
            'description' => 'Updated predictive analytics description.',
            'features_text' => "Feature 1\nFeature 2",
            'sort_order' => 10,
            'is_enabled' => '1',
        ]);

        $updateResponse->assertRedirect();
        $module->refresh();
        $this->assertEquals('AI Inventory & Demand Forecasting', $module->title);

        // 3. Delete module
        $deleteResponse = $this->actingAs($this->superAdmin)->delete(route('superadmin.brochure.modules.destroy', $module));
        $deleteResponse->assertRedirect();
        $this->assertNull(BrochureModule::find($module->id));
    }

    /**
     * Test super admin can manage commercial pricing plans.
     */
    public function test_super_admin_can_manage_pricing_plans(): void
    {
        // 1. Create plan
        $response = $this->actingAs($this->superAdmin)->post(route('superadmin.brochure.plans.store'), [
            'name' => 'Custom Dedicated Cloud',
            'price' => 'Custom Quote',
            'period' => 'billed annually',
            'badge' => 'CUSTOM',
            'is_popular' => '1',
            'description' => 'Dedicated cloud cluster with custom SLA guarantees.',
            'features_text' => "Dedicated Server\nCustom Domain\nDedicated Account Engineer",
            'sort_order' => 5,
            'is_enabled' => '1',
        ]);

        $response->assertRedirect();
        $plan = BrochurePlan::where('name', 'Custom Dedicated Cloud')->first();
        $this->assertNotNull($plan);
        $this->assertTrue($plan->is_popular);

        // 2. Delete plan
        $delResponse = $this->actingAs($this->superAdmin)->delete(route('superadmin.brochure.plans.destroy', $plan));
        $delResponse->assertRedirect();
        $this->assertNull(BrochurePlan::find($plan->id));
    }

    /**
     * Test screenshot upload and deletion.
     */
    public function test_super_admin_can_upload_and_delete_screenshot(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('test_counter_pos.png', 1280, 800);

        $response = $this->actingAs($this->superAdmin)->post(route('superadmin.brochure.screenshots.store'), [
            'title' => 'Counter POS Checkout Test',
            'caption' => 'Cashier checkout workflow simulation.',
            'image' => $file,
            'sort_order' => 10,
            'is_enabled' => '1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $screenshot = BrochureScreenshot::where('title', 'Counter POS Checkout Test')->first();
        $this->assertNotNull($screenshot);

        // Delete
        $delResponse = $this->actingAs($this->superAdmin)->delete(route('superadmin.brochure.screenshots.destroy', $screenshot));
        $delResponse->assertRedirect();
        $this->assertNull(BrochureScreenshot::find($screenshot->id));
    }

    /**
     * Test live A4 HTML preview renders successfully.
     */
    public function test_live_a4_preview_renders(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.brochure.preview'));

        $response->assertOk();
        $response->assertSee('INVOIRY');
        $response->assertSee('6006801960');
        $response->assertSee('6005958161');
        $response->assertSee('hire.iry@gmail.com');
        $response->assertSee('Rapid POS Billing');
        $response->assertSee('Dual-SIM IMEI');
        $response->assertSee('Professional Retail Pro');
        $response->assertSee('&#8377;499', false);
    }

    /**
     * Test compiling and downloading the PDF brochure.
     */
    public function test_compile_and_download_pdf_brochure(): void
    {
        // 1. Generate
        $genResponse = $this->actingAs($this->superAdmin)->post(route('superadmin.brochure.generate'), [
            'bump_version' => '1',
            'notes' => 'Automated Feature Test Build',
        ]);

        $genResponse->assertRedirect();
        $genResponse->assertSessionHas('success');

        $latestGen = BrochureGeneration::latest('id')->first();
        $this->assertNotNull($latestGen);
        $this->assertEquals('Automated Feature Test Build', $latestGen->notes);

        // 2. Download from authenticated endpoint
        $dlResponse = $this->actingAs($this->superAdmin)->get(route('superadmin.brochure.download'));
        $dlResponse->assertOk();
        $this->assertEquals('application/pdf', $dlResponse->headers->get('content-type'));

        // 3. Public download endpoint
        $pubResponse = $this->get(route('brochure.public-download'));
        $pubResponse->assertOk();
        $this->assertEquals('application/pdf', $pubResponse->headers->get('content-type'));
    }
}
