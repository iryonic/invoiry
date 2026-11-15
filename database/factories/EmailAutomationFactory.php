<?php

namespace Database\Factories;

use App\Models\EmailAutomation;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmailAutomation>
 */
class EmailAutomationFactory extends Factory
{
    protected $model = EmailAutomation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'event_name' => EmailAutomation::EVENT_INVOICE_FINALIZED,
            'template_id' => null,
            'is_enabled' => true,
            'timing_days' => 0,
            'description' => 'Automatically email the generated PDF invoice to customer.',
        ];
    }
}
