<?php

namespace Database\Factories;

use App\Models\EmailLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmailLog>
 */
class EmailLogFactory extends Factory
{
    protected $model = EmailLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'user_id' => User::factory(),
            'customer_id' => null,
            'invoice_id' => null,
            'template_id' => null,
            'category' => 'general',
            'recipient_email' => fake()->safeEmail(),
            'recipient_name' => fake()->name(),
            'cc' => null,
            'bcc' => null,
            'subject' => fake()->sentence(5),
            'body_html' => '<p>'.fake()->paragraph().'</p>',
            'body_text' => fake()->paragraph(),
            'attachments' => null,
            'mailer_used' => 'smtp',
            'status' => EmailLog::STATUS_SENT,
            'attempts' => 1,
            'error_message' => null,
            'scheduled_at' => null,
            'sent_at' => now(),
        ];
    }
}
