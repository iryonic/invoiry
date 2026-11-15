<?php

namespace Database\Factories;

use App\Models\EmailTemplate;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmailTemplate>
 */
class EmailTemplateFactory extends Factory
{
    protected $model = EmailTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'name' => 'Invoice Generated Notification',
            'code' => 'invoice_generated',
            'category' => 'invoices',
            'subject' => 'Invoice #{{invoice_number}} from {{business_name}}',
            'body_html' => '<p>Dear {{customer_name}},</p><p>Thank you for choosing {{business_name}}. Your invoice #{{invoice_number}} for <strong>{{invoice_total}}</strong> has been generated.</p>',
            'body_text' => 'Dear {{customer_name}}, Thank you for choosing {{business_name}}. Your invoice #{{invoice_number}} for {{invoice_total}} has been generated.',
            'available_placeholders' => ['customer_name', 'invoice_number', 'invoice_total', 'business_name'],
            'is_active' => true,
            'is_default' => true,
        ];
    }
}
