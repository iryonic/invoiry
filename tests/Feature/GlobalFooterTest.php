<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalFooterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_footer_appears_on_authenticated_workspace_pages(): void
    {
        $user = User::where('role', 'tenant_admin')->first();

        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('globalPageFooter');
        $response->assertSee('INVOIRY');
        $response->assertSee('v1.0.0');
    }

    public function test_footer_does_not_appear_on_authentication_pages(): void
    {
        $authRoutes = [
            route('login'),
            route('register'),
            route('password.request'),
        ];

        foreach ($authRoutes as $url) {
            $response = $this->get($url);
            $response->assertStatus(200);
            $response->assertDontSee('globalPageFooter');
            $response->assertDontSee('Global Page Footer');
        }
    }

    public function test_super_admin_can_view_and_update_footer_settings(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        // 1. Visit super admin settings page and check footer management tab
        $viewRes = $this->actingAs($superAdmin)->get(route('superadmin.settings.index'));
        $viewRes->assertStatus(200);
        $viewRes->assertSee('Global Page Footer');
        $viewRes->assertSee('Global Page Footer Configuration');

        // 2. Update footer settings
        $updateRes = $this->actingAs($superAdmin)->put(route('superadmin.settings.update'), [
            'platform_name' => 'Custom Retail POS',
            'support_email' => 'admin@customretail.com',
            'default_trial_days' => 30,
            'footer_settings_submitted' => '1',
            'footer_enabled' => '1',
            'footer_copyright' => '© {year} Apex Retail Systems Worldwide.',
            'footer_tagline' => 'Industrial hardware POS & multi-channel inventory intelligence.',
            'footer_contact_info' => '24/7 Priority Desk: +91 98765 43210',
            'footer_badge_text' => 'v3.5 Enterprise',
            'footer_link_1_text' => 'Legal Terms',
            'footer_link_1_url' => 'https://example.com/terms',
            'footer_link_2_text' => 'Privacy Standard',
            'footer_link_2_url' => 'https://example.com/privacy',
            'footer_link_3_text' => 'POS Help Desk',
            'footer_link_3_url' => 'https://example.com/help',
        ]);

        $updateRes->assertRedirect();
        $updateRes->assertSessionHas('success');

        // 3. Verify updated footer content renders on user dashboard
        $user = User::where('role', 'tenant_admin')->first();
        $dashboardRes = $this->actingAs($user)->get(route('dashboard'));
        $dashboardRes->assertStatus(200);
        $dashboardRes->assertSee('Apex Retail Systems Worldwide.');
        $dashboardRes->assertSee('Industrial hardware POS & multi-channel inventory intelligence.');
        $dashboardRes->assertSee('24/7 Priority Desk: +91 98765 43210');
        $dashboardRes->assertSee('v3.5 Enterprise');
        $dashboardRes->assertSee('Legal Terms');
        $dashboardRes->assertSee('Privacy Standard');
        $dashboardRes->assertSee('POS Help Desk');
    }

    public function test_super_admin_can_disable_footer_globally(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        // Update settings with footer disabled (footer_enabled omitted)
        $updateRes = $this->actingAs($superAdmin)->put(route('superadmin.settings.update'), [
            'platform_name' => 'Custom Retail POS',
            'support_email' => 'admin@customretail.com',
            'default_trial_days' => 30,
            'footer_settings_submitted' => '1',
            // omitting 'footer_enabled' disables it
        ]);

        $updateRes->assertRedirect();

        // Verify footer is absent on dashboard
        $user = User::where('role', 'tenant_admin')->first();
        $dashboardRes = $this->actingAs($user)->get(route('dashboard'));
        $dashboardRes->assertStatus(200);
        $dashboardRes->assertDontSee('globalPageFooter');
    }

    public function test_non_super_admin_cannot_access_or_update_footer_settings(): void
    {
        $regularUser = User::where('role', 'tenant_admin')->first();

        $response = $this->actingAs($regularUser)->get(route('superadmin.settings.index'));
        $response->assertStatus(403);

        $putResponse = $this->actingAs($regularUser)->put(route('superadmin.settings.update'), [
            'footer_copyright' => 'Hacked footer',
        ]);
        $putResponse->assertStatus(403);
    }
}
