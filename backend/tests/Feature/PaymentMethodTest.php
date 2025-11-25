<?php

namespace Tests\Feature;

use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_active_payment_methods(): void
    {
        PaymentMethod::factory()->create(['is_active' => true, 'name' => 'D17']);
        PaymentMethod::factory()->create(['is_active' => false, 'name' => 'Inactive Method']);

        $response = $this->getJson('/api/v1/payment-methods');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'payment_methods' => [
                    '*' => ['id', 'name', 'code', 'type', 'is_active'],
                ],
            ])
            ->assertJsonCount(1, 'payment_methods');
    }

    public function test_can_filter_payment_methods_by_context(): void
    {
        PaymentMethod::factory()->create([
            'is_active' => true,
            'code' => 'd17',
            'available_for' => ['donations', 'products'],
        ]);

        PaymentMethod::factory()->create([
            'is_active' => true,
            'code' => 'cash',
            'available_for' => ['products'],
        ]);

        $response = $this->getJson('/api/v1/payment-methods?context=donations');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'payment_methods');
    }

    public function test_can_filter_payment_methods_by_type(): void
    {
        PaymentMethod::factory()->create(['is_active' => true, 'type' => 'mobile_wallet']);
        PaymentMethod::factory()->create(['is_active' => true, 'type' => 'bank_card']);

        $response = $this->getJson('/api/v1/payment-methods?type=mobile_wallet');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'payment_methods');
    }

    public function test_can_view_payment_method_details(): void
    {
        $method = PaymentMethod::factory()->create([
            'name' => 'D17',
            'code' => 'd17',
            'type' => 'mobile_wallet',
            'is_active' => true,
        ]);

        $response = $this->getJson("/api/v1/payment-methods/{$method->id}");

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'D17',
                'code' => 'd17',
                'type' => 'mobile_wallet',
            ]);
    }

    public function test_can_calculate_transaction_fees(): void
    {
        $method = PaymentMethod::factory()->create([
            'is_active' => true,
            'transaction_fee' => 2.00,
            'transaction_fee_percentage' => 1.5,
        ]);

        $response = $this->postJson("/api/v1/payment-methods/{$method->id}/calculate-fees", [
            'amount' => 100,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'amount',
                'fixed_fee',
                'percentage_fee',
                'total_fee',
                'net_amount',
            ]);

        // Fixed: 2 TND, Percentage: 100 * 1.5% = 1.5 TND, Total: 3.5 TND
        $response->assertJson([
            'amount' => 100,
            'fixed_fee' => 2.00,
            'percentage_fee' => 1.50,
            'total_fee' => 3.50,
            'net_amount' => 96.50,
        ]);
    }

    public function test_payment_method_can_check_amount_limits(): void
    {
        $method = PaymentMethod::factory()->create([
            'min_amount' => 10,
            'max_amount' => 1000,
        ]);

        $this->assertTrue($method->canProcessAmount(50));
        $this->assertFalse($method->canProcessAmount(5)); // Below min
        $this->assertFalse($method->canProcessAmount(1500)); // Above max
    }

    public function test_payment_method_checks_availability_for_context(): void
    {
        $method = PaymentMethod::factory()->create([
            'available_for' => ['donations', 'products'],
        ]);

        $this->assertTrue($method->isAvailableFor('donations'));
        $this->assertTrue($method->isAvailableFor('products'));
        $this->assertFalse($method->isAvailableFor('tickets'));
    }

    public function test_payment_methods_ordered_by_display_order(): void
    {
        PaymentMethod::factory()->create(['is_active' => true, 'display_order' => 3, 'name' => 'Third']);
        PaymentMethod::factory()->create(['is_active' => true, 'display_order' => 1, 'name' => 'First']);
        PaymentMethod::factory()->create(['is_active' => true, 'display_order' => 2, 'name' => 'Second']);

        $response = $this->getJson('/api/v1/payment-methods');

        $methods = $response->json('payment_methods');
        $this->assertEquals('First', $methods[0]['name']);
        $this->assertEquals('Second', $methods[1]['name']);
        $this->assertEquals('Third', $methods[2]['name']);
    }

    public function test_default_payment_method_is_marked(): void
    {
        PaymentMethod::factory()->create(['is_active' => true, 'is_default' => true, 'name' => 'Default Method']);
        PaymentMethod::factory()->create(['is_active' => true, 'is_default' => false, 'name' => 'Other Method']);

        $response = $this->getJson('/api/v1/payment-methods');

        $methods = $response->json('payment_methods');
        $defaultMethod = collect($methods)->firstWhere('is_default', true);

        $this->assertNotNull($defaultMethod);
        $this->assertEquals('Default Method', $defaultMethod['name']);
    }

    public function test_supports_multiple_currencies(): void
    {
        $method = PaymentMethod::factory()->create([
            'supported_currencies' => ['TND', 'EUR', 'USD'],
        ]);

        $response = $this->getJson("/api/v1/payment-methods/{$method->id}");

        $response->assertStatus(200)
            ->assertJson([
                'supported_currencies' => ['TND', 'EUR', 'USD'],
            ]);
    }
}
