<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_404_page_renders_with_branded_layout_for_missing_routes(): void
    {
        $response = $this->get('/non-existent-terminal-route-404');
        $response->assertStatus(404);
        $response->assertSee('404 NOT FOUND');
        $response->assertSee('Record or Page Not Located');
        $response->assertSee('Return to Dashboard');
    }

    public function test_404_page_renders_when_model_is_not_found(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();

        $response = $this->actingAs($user)->get('/customers/999999');
        $response->assertStatus(404);
        $response->assertSee('404 NOT FOUND');
    }

    public function test_403_page_renders_with_branded_layout_when_access_is_forbidden(): void
    {
        $cashier = User::where('role', 'cashier')->first();

        // Cashier attempting to access super admin restricted route
        $response = $this->actingAs($cashier)->get('/superadmin');
        $response->assertStatus(403);
        $response->assertSee('403 ACCESS RESTRICTED');
        $response->assertSee('Administrative Permission Required');
    }

    public function test_sold_item_cannot_be_deleted_returns_friendly_error(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $soldItem = Item::where('tenant_id', $user->tenant_id)->where('status', 'sold')->first();

        if (! $soldItem) {
            $soldItem = Item::where('tenant_id', $user->tenant_id)->first();
            $soldItem->update(['status' => 'sold']);
        }

        $response = $this->actingAs($user)->delete(route('items.destroy', $soldItem));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_duplicate_in_stock_imei_returns_clean_error_message(): void
    {
        $user = User::where('email', 'freetrial@gmail.com')->first();
        $inStockItem = Item::where('tenant_id', $user->tenant_id)->inStock()->first();

        $response = $this->actingAs($user)->post(route('items.store'), [
            'item_name' => 'Duplicate IMEI Device',
            'purchase_price' => 25000,
            'imei_1' => $inStockItem->imei_1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertStringContainsString('already present in active store inventory', session('error'));
    }
}
