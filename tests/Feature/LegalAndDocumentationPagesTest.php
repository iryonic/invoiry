<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalAndDocumentationPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_privacy_policy_page_loads_for_guests_and_authenticated_users(): void
    {
        // 1. Guest access
        $guestRes = $this->get(route('privacy'));
        $guestRes->assertStatus(200);
        $guestRes->assertSee('Privacy Policy');
        $guestRes->assertSee('Multi-Tenant Isolation');
        $guestRes->assertSee('Hardware Identifiers');

        // 2. Authenticated user access
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $authRes = $this->actingAs($user)->get(route('privacy'));
        $authRes->assertStatus(200);
        $authRes->assertSee('Privacy Policy');
    }

    public function test_terms_of_service_page_loads_for_guests_and_authenticated_users(): void
    {
        // 1. Guest access
        $guestRes = $this->get(route('terms'));
        $guestRes->assertStatus(200);
        $guestRes->assertSee('Terms and Conditions');
        $guestRes->assertSee('IMEI, Serial Tracking', false);
        $guestRes->assertSee('Goods and Services Tax (GST) Disclaimer', false);

        // 2. Authenticated user access
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $authRes = $this->actingAs($user)->get(route('terms'));
        $authRes->assertStatus(200);
        $authRes->assertSee('Terms and Conditions');
    }

    public function test_help_and_documentation_page_loads_with_all_modules(): void
    {
        // 1. Guest access
        $guestRes = $this->get(route('help'));
        $guestRes->assertStatus(200);
        $guestRes->assertSee('System Documentation & Operational Manual', false);
        $guestRes->assertSee('Terminal Onboarding', false);
        $guestRes->assertSee('Stock Inwarding', false);
        $guestRes->assertSee('Point of Sale (POS) Billing Engine', false);
        $guestRes->assertSee('Customer Credit Ledger (Khata)', false);

        // 2. /documentation alias route
        $aliasRes = $this->get(route('docs'));
        $aliasRes->assertStatus(200);
        $aliasRes->assertSee('System Documentation & Operational Manual', false);

        // 3. Authenticated user access
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $authRes = $this->actingAs($user)->get(route('help'));
        $authRes->assertStatus(200);
        $authRes->assertSee('Automated GST Math', false);
    }

    public function test_footer_renders_links_to_privacy_terms_and_help(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('privacy'));
        $response->assertSee(route('terms'));
        $response->assertSee(route('help'));
    }
}
