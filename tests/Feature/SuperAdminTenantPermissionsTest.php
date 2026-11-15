<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Services\RbacPermissionManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminTenantPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $tenantAdmin;

    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->superAdmin = User::where('role', 'super_admin')->first()
            ?? User::factory()->create(['role' => 'super_admin']);

        $this->tenant = Tenant::first() ?? Tenant::factory()->create();
        $this->tenantAdmin = User::where('role', 'tenant_admin')->where('tenant_id', $this->tenant->id)->first()
            ?? User::factory()->create(['role' => 'tenant_admin', 'tenant_id' => $this->tenant->id]);
    }

    public function test_super_admin_can_view_tenant_permissions_page_with_new_functionalities(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get(route('superadmin.tenants.permissions', $this->tenant));

        $response->assertOk();
        $response->assertSee('RBAC Governance');
        $response->assertSee('Enterprise Feature Architecture');
        $response->assertSee('Communications &amp; Email System', false);
        $response->assertSee('Email Invoices, Receipts &amp; Payment Reminders', false);
        $response->assertSee('Adjust Stock Quantities &amp; View Movement History', false);
        $response->assertSee('Quick Presets');
        $response->assertSee('Full Enterprise');
        $response->assertSee('Retail POS (No Mail)');
    }

    public function test_super_admin_can_update_tenant_permissions_to_disable_mail(): void
    {
        $this->actingAs($this->superAdmin);

        // Get default permissions and remove 'mail'
        $permissions = RbacPermissionManager::getDefaultPermissions();
        unset($permissions['mail']);

        $response = $this->put(route('superadmin.tenants.permissions.update', $this->tenant), [
            'permissions' => $permissions,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->tenant->refresh();
        $this->assertFalse(RbacPermissionManager::tenantHasPermission($this->tenant, 'mail.view'));

        // Tenant admin should now be restricted from accessing /mail
        $this->actingAs($this->tenantAdmin);
        $mailResponse = $this->get(route('mail.dashboard'));
        $mailResponse->assertForbidden();
    }

    public function test_super_admin_can_enable_mail_and_tenant_can_access(): void
    {
        $this->actingAs($this->superAdmin);

        $permissions = RbacPermissionManager::getDefaultPermissions();

        $response = $this->put(route('superadmin.tenants.permissions.update', $this->tenant), [
            'permissions' => $permissions,
        ]);

        $response->assertRedirect();
        $this->tenant->refresh();

        $this->assertTrue(RbacPermissionManager::tenantHasPermission($this->tenant, 'mail.view'));

        // Tenant admin can access /mail
        $this->actingAs($this->tenantAdmin);
        $mailResponse = $this->get(route('mail.dashboard'));
        $mailResponse->assertOk();
    }
}
