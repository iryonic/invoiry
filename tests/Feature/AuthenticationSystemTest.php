<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthenticationSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_page_renders_with_split_pane_and_evaluation_switcher(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee('Point-of-Sale', false);
        $response->assertSee('Retail Intelligence', false);
        $response->assertSee('Quick Evaluation Roles');
        $response->assertSee('Sign In to Terminal');
        $response->assertSee(route('register'));
        $response->assertSee(route('password.request'));
    }

    public function test_user_can_authenticate_via_email_and_redirects_to_dashboard(): void
    {
        $response = $this->post(route('login'), [
            'login' => 'freetrial@gmail.com',
            'password' => 'freetrial',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_user_can_authenticate_via_phone_number(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $user->update(['phone' => '9876543210']);

        $response = $this->post(route('login'), [
            'login' => '9876543210',
            'password' => 'freetrial',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->from(route('login'))->post(route('login'), [
            'login' => 'freetrial@gmail.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_deactivated_user_cannot_login(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $user->update(['is_active' => false]);

        $response = $this->post(route('login'), [
            'login' => 'freetrial@gmail.com',
            'password' => 'freetrial',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_evaluation_roles_quick_login_works(): void
    {
        $response = $this->get(route('demo.login', 'cashier'));
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals('cashier', auth()->user()->role);
    }

    public function test_registration_page_renders_with_gst_states(): void
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
        $response->assertSee('Create Business Store');
        $response->assertSee('01 - Jammu and Kashmir');
        $response->assertSee('07 - Delhi');
        $response->assertSee('Activate 14-Day Free Trial');
    }

    public function test_new_store_can_register_and_automatically_logs_in_with_14_day_trial(): void
    {
        $response = $this->post(route('register'), [
            'store_name' => 'Kashmir Tech Electronics',
            'state_code' => '01',
            'gstin' => '01ABCDE1234F1Z5',
            'name' => 'Tariq Lone',
            'phone' => '9419001122',
            'email' => 'tariq@kashmirtech.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $user = auth()->user();
        $this->assertEquals('tariq@kashmirtech.com', $user->email);
        $this->assertEquals('tenant_admin', $user->role);
        $this->assertNotNull($user->tenant_id);

        $tenant = $user->tenant;
        $this->assertEquals('Kashmir Tech Electronics', $tenant->name);
        $this->assertEquals('01', $tenant->state_code);
        $this->assertEquals('trial', $tenant->plan);
        $this->assertTrue($tenant->is_active);
        $this->assertNotNull($tenant->expires_at);
        $this->assertIsArray($tenant->permissions);
        $this->assertIsArray($tenant->form_fields);
    }

    public function test_forgot_password_generates_token_and_flashes_link(): void
    {
        $response = $this->get(route('password.request'));
        $response->assertStatus(200);
        $response->assertSee('Forgot Password?');

        $submitResponse = $this->post(route('password.email'), [
            'email' => 'freetrial@gmail.com',
        ]);

        $submitResponse->assertRedirect();
        $submitResponse->assertSessionHas('status');
        $submitResponse->assertSessionHas('demo_reset_link');

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'freetrial@gmail.com',
        ]);
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $rawToken = Str::random(64);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => 'freetrial@gmail.com'],
            [
                'token' => Hash::make($rawToken),
                'created_at' => now(),
            ]
        );

        $viewResponse = $this->get(route('password.reset', ['token' => $rawToken, 'email' => 'freetrial@gmail.com']));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Set New Password');

        $resetResponse = $this->post(route('password.update'), [
            'token' => $rawToken,
            'email' => 'freetrial@gmail.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $resetResponse->assertRedirect(route('login'));
        $resetResponse->assertSessionHas('success');

        // Verify user can log in with new password
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_terminal_can_be_locked_and_unlocked_with_password(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();

        // Lock terminal
        $lockResponse = $this->actingAs($user)->get(route('lock'));
        $lockResponse->assertStatus(200);
        $lockResponse->assertSee('Terminal Locked');
        $this->assertTrue(session('terminal_locked'));

        // Locked terminal accessing dashboard gets redirected to lock screen
        $dashResponse = $this->actingAs($user)->get(route('dashboard'));
        $dashResponse->assertRedirect(route('lock'));

        // Unlock with wrong password fails
        $failUnlock = $this->actingAs($user)->post(route('unlock'), [
            'password' => 'wrongpass',
        ]);
        $failUnlock->assertSessionHasErrors('password');
        $this->assertTrue(session('terminal_locked'));

        // Unlock with correct password succeeds
        $passUnlock = $this->actingAs($user)->post(route('unlock'), [
            'password' => 'freetrial',
        ]);
        $passUnlock->assertRedirect(route('dashboard'));
        $this->assertFalse(session()->has('terminal_locked'));
    }
}
