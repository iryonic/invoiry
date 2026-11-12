<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\SystemSetting;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PlatformEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('public');
    }

    public function test_profile_view_and_update_information(): void
    {
        $user = User::where('role', 'tenant_admin')->first();

        // 1. Visit Profile
        $response = $this->actingAs($user)->get(route('profile.edit'));
        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee('My Profile');

        // 2. Update Personal Info
        $updateRes = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Updated Admin Name',
            'email' => $user->email,
        ]);
        $updateRes->assertRedirect(route('profile.edit'));
        $updateRes->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Admin Name',
        ]);
    }

    public function test_profile_photo_upload_and_removal(): void
    {
        $user = User::where('role', 'tenant_admin')->first();

        $photo = UploadedFile::fake()->image('avatar.jpg', 300, 300);

        // Upload photo
        $uploadRes = $this->from(route('profile.edit'))->actingAs($user)->post(route('profile.photo'), [
            'avatar' => $photo,
        ]);
        $uploadRes->assertRedirect(route('profile.edit'));
        $uploadRes->assertSessionHas('success');

        $user->refresh();
        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);

        // Delete photo
        $deleteRes = $this->from(route('profile.edit'))->actingAs($user)->delete(route('profile.photo.destroy'));
        $deleteRes->assertRedirect(route('profile.edit'));
        $deleteRes->assertSessionHas('success');

        $user->refresh();
        $this->assertNull($user->avatar);
    }

    public function test_profile_password_change_flow(): void
    {
        $user = User::where('role', 'tenant_admin')->first();
        $user->update(['password' => Hash::make('SecretPass123')]);

        // Fails with incorrect current password
        $failRes = $this->from(route('profile.edit'))->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'wrong-pass',
            'password' => 'NewSecretPassword123!',
            'password_confirmation' => 'NewSecretPassword123!',
        ]);
        $failRes->assertSessionHasErrors('current_password');

        // Succeeds with correct current password
        $successRes = $this->from(route('profile.edit'))->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'SecretPass123',
            'password' => 'NewSecretPassword123!',
            'password_confirmation' => 'NewSecretPassword123!',
        ]);
        $successRes->assertRedirect(route('profile.edit'));
        $successRes->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check('NewSecretPassword123!', $user->password));
    }

    public function test_session_management_and_revocation(): void
    {
        $user = User::where('role', 'tenant_admin')->first();

        // Insert mock sessions in the database sessions table
        DB::table('sessions')->insert([
            [
                'id' => 'sess_mock_1',
                'user_id' => $user->id,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0',
                'payload' => serialize(['mock' => true]),
                'last_activity' => time(),
            ],
            [
                'id' => 'sess_mock_2',
                'user_id' => $user->id,
                'ip_address' => '192.168.1.50',
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0) Safari/604.1',
                'payload' => serialize(['mock' => true]),
                'last_activity' => time() - 3600,
            ],
        ]);

        $this->assertEquals(2, DB::table('sessions')->where('user_id', $user->id)->count());

        // Revoke single session
        $res = $this->from(route('profile.edit'))->actingAs($user)->delete(route('profile.sessions.destroy', 'sess_mock_1'));
        $res->assertRedirect(route('profile.edit'));
        $res->assertSessionHas('success');

        $this->assertDatabaseMissing('sessions', ['id' => 'sess_mock_1']);
        $this->assertDatabaseHas('sessions', ['id' => 'sess_mock_2']);
    }

    public function test_lock_screen_and_terminal_unlock(): void
    {
        $user = User::where('role', 'tenant_admin')->first();
        $user->update(['password' => Hash::make('TerminalPass123')]);

        // 1. View lock screen
        $lockRes = $this->actingAs($user)->get(route('lock'));
        $lockRes->assertStatus(200);
        $lockRes->assertSee('Terminal Locked');
        $lockRes->assertSee($user->name);

        // 2. Attempt unlock with incorrect password
        $failUnlock = $this->actingAs($user)->post(route('unlock'), [
            'password' => 'invalid-password',
        ]);
        $failUnlock->assertSessionHasErrors('password');

        // 3. Unlock with valid password
        $successUnlock = $this->actingAs($user)->post(route('unlock'), [
            'password' => 'TerminalPass123',
        ]);
        $successUnlock->assertRedirect(route('dashboard'));
    }

    public function test_maintenance_mode_middleware_blocks_regular_users_but_permits_super_admin(): void
    {
        SystemSetting::set('maintenance_mode', '1', 'system');
        SystemSetting::set('maintenance_heading', 'Upgrading Cloud Servers', 'system');
        SystemSetting::set('maintenance_message', 'Emergency maintenance in progress.', 'system');

        $tenantUser = User::where('role', 'tenant_admin')->first();
        $superAdmin = User::where('role', 'super_admin')->first();

        // Regular user receives 503
        $blockedRes = $this->actingAs($tenantUser)->get(route('dashboard'));
        $blockedRes->assertStatus(503);
        $blockedRes->assertSee('Upgrading Cloud Servers');
        $blockedRes->assertSee('Emergency maintenance in progress.');

        // Super Admin bypasses maintenance mode
        $allowedRes = $this->actingAs($superAdmin)->get(route('superadmin.index'));
        $allowedRes->assertStatus(200);

        // Reset maintenance mode
        SystemSetting::set('maintenance_mode', '0', 'system');
    }

    public function test_central_version_setting_updates_footer_and_login_screen(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        // Update central version in Super Admin settings
        $response = $this->actingAs($superAdmin)->put(route('superadmin.settings.update'), [
            'app_version' => 'v2.8.4-pro',
            'platform_name' => 'Invoiry Enterprise',
            'support_email' => 'support@example.com',
            'default_trial_days' => 30,
        ]);
        $response->assertRedirect();

        // 1. Verify in Login screen (must be guest)
        auth()->logout();
        $loginRes = $this->get(route('login'));
        $loginRes->assertStatus(200);
        $loginRes->assertSee('INVOIRY v2.8.4-pro');

        // 2. Verify in Global Footer
        $tenantUser = User::where('role', 'tenant_admin')->first();
        $dashboardRes = $this->actingAs($tenantUser)->get(route('dashboard'));
        $dashboardRes->assertStatus(200);
        $dashboardRes->assertSee('v2.8.4-pro');
    }

    public function test_bulk_actions_on_items(): void
    {
        $user = User::where('role', 'tenant_admin')->first();
        $tenant = $user->tenant;

        $item1 = Item::create([
            'tenant_id' => $tenant->id,
            'item_name' => 'Bulk Test Phone 1',
            'condition' => 'new',
            'purchase_price' => 10000,
            'purchase_date' => now(),
            'status' => 'in_stock',
        ]);

        $item2 = Item::create([
            'tenant_id' => $tenant->id,
            'item_name' => 'Bulk Test Phone 2',
            'condition' => 'used',
            'purchase_price' => 8000,
            'purchase_date' => now(),
            'status' => 'in_stock',
        ]);

        // Bulk mark as sold
        $res = $this->actingAs($user)->post(route('items.bulk'), [
            'ids' => [$item1->id, $item2->id],
            'action' => 'mark_sold',
        ]);
        $res->assertRedirect();
        $res->assertSessionHas('success');

        $this->assertEquals('sold', $item1->fresh()->status);
        $this->assertEquals('sold', $item2->fresh()->status);

        // Bulk delete
        $delRes = $this->actingAs($user)->post(route('items.bulk'), [
            'ids' => [$item1->id, $item2->id],
            'action' => 'delete',
        ]);
        $delRes->assertRedirect();
        $delRes->assertSessionHas('success');

        $this->assertDatabaseMissing('items', ['id' => $item1->id]);
        $this->assertDatabaseMissing('items', ['id' => $item2->id]);
    }

    public function test_bulk_actions_on_customers(): void
    {
        $user = User::where('role', 'tenant_admin')->first();
        $tenant = $user->tenant;

        $c1 = Customer::create([
            'tenant_id' => $tenant->id,
            'name' => 'Customer With No Bill',
            'phone' => '9998887771',
        ]);

        $c2 = Customer::create([
            'tenant_id' => $tenant->id,
            'name' => 'Customer With Active Bill',
            'phone' => '9998887772',
        ]);

        // Attach an invoice to c2
        Invoice::create([
            'tenant_id' => $tenant->id,
            'customer_id' => $c2->id,
            'invoice_number' => 'INV-TEST-999',
            'invoice_date' => now(),
            'subtotal' => 1000,
            'tax_amount' => 180,
            'grand_total' => 1180,
            'amount_paid' => 1180,
            'payment_status' => 'paid',
            'created_by' => $user->id,
        ]);

        // Bulk delete
        $res = $this->actingAs($user)->post(route('customers.bulk'), [
            'ids' => [$c1->id, $c2->id],
            'action' => 'delete',
        ]);
        $res->assertRedirect();
        $res->assertSessionHas('success');

        // c1 should be deleted, c2 should be protected because of billing history
        $this->assertDatabaseMissing('customers', ['id' => $c1->id]);
        $this->assertDatabaseHas('customers', ['id' => $c2->id]);
    }

    public function test_bulk_actions_on_tenants_and_users_by_super_admin(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        // 1. Bulk tenants
        $tenant = Tenant::create([
            'name' => 'Bulk Store Test',
            'slug' => 'bulk-store-test',
            'email' => 'bulkstore@test.com',
            'phone' => '9123456789',
            'is_active' => true,
        ]);

        $suspendRes = $this->actingAs($superAdmin)->post(route('superadmin.tenants.bulk'), [
            'ids' => [$tenant->id],
            'action' => 'suspend',
        ]);
        $suspendRes->assertRedirect();
        $this->assertFalse((bool) $tenant->fresh()->is_active);

        // 2. Bulk users
        $testUser = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Bulk Worker',
            'email' => 'worker@bulk.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'is_active' => true,
        ]);

        $bulkUserRes = $this->actingAs($superAdmin)->post(route('superadmin.users.bulk'), [
            'ids' => [$testUser->id, $superAdmin->id], // Attempts to include superAdmin
            'action' => 'suspend',
        ]);
        $bulkUserRes->assertRedirect();

        $this->assertFalse((bool) $testUser->fresh()->is_active);
        $this->assertTrue((bool) $superAdmin->fresh()->is_active); // Super admin was protected!
    }
}
