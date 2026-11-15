<?php

namespace Database\Factories;

use App\Models\EmailSetting;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmailSetting>
 */
class EmailSettingFactory extends Factory
{
    protected $model = EmailSetting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'provider_name' => 'custom',
            'display_name' => 'Primary SMTP Server',
            'mail_mailer' => 'smtp',
            'mail_host' => 'smtp.mailtrap.io',
            'mail_port' => 587,
            'mail_username' => fake()->userName(),
            'mail_password' => 'secret123',
            'mail_encryption' => 'tls',
            'from_address' => fake()->safeEmail(),
            'from_name' => fake()->company(),
            'reply_to_address' => null,
            'is_active' => true,
            'is_default' => true,
            'last_tested_at' => now(),
            'last_test_status' => 'success',
            'last_test_error' => null,
        ];
    }
}
