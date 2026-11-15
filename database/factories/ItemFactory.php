<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'item_name' => fake()->randomElement(['iPhone 15 Pro', 'Samsung Galaxy S24 Ultra', 'Google Pixel 8 Pro']),
            'condition' => 'new',
            'purchase_date' => now()->toDateString(),
            'purchase_price' => fake()->numberBetween(10000, 80000),
            'selling_price' => fake()->numberBetween(15000, 95000),
            'quantity' => 1,
            'status' => 'in_stock',
        ];
    }
}
