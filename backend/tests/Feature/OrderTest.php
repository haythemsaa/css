<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->product1 = Product::create([
            'name' => 'Maillot CSS',
            'slug' => 'maillot-css',
            'description' => 'Maillot officiel',
            'category' => 'jerseys',
            'sku' => 'CSS-001',
            'price' => 89.90,
            'stock_quantity' => 50,
            'is_available' => true,
        ]);

        $this->product2 = Product::create([
            'name' => 'Écharpe CSS',
            'slug' => 'echarpe-css',
            'description' => 'Écharpe officielle',
            'category' => 'merchandise',
            'sku' => 'CSS-002',
            'price' => 19.90,
            'stock_quantity' => 100,
            'is_available' => true,
        ]);
    }

    public function test_authenticated_user_can_create_order(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/orders', [
                'items' => [
                    ['product_id' => $this->product1->id, 'quantity' => 2],
                    ['product_id' => $this->product2->id, 'quantity' => 1],
                ],
                'shipping_address' => [
                    'full_name' => 'John Doe',
                    'phone' => '+216 12 345 678',
                    'address_line' => '123 Rue Habib Bourguiba',
                    'city' => 'Sfax',
                    'postal_code' => '3000',
                ],
                'payment_method' => 'd17',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'order' => ['id', 'order_number', 'total', 'items'],
            ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        // Check stock decremented
        $this->product1->refresh();
        $this->assertEquals(48, $this->product1->stock_quantity);

        $this->product2->refresh();
        $this->assertEquals(99, $this->product2->stock_quantity);
    }

    public function test_order_calculates_totals_correctly(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/orders', [
                'items' => [
                    ['product_id' => $this->product1->id, 'quantity' => 1], // 89.90
                ],
                'shipping_address' => [
                    'full_name' => 'John Doe',
                    'phone' => '+216 12 345 678',
                    'address_line' => '123 Rue',
                    'city' => 'Sfax',
                    'postal_code' => '3000',
                ],
                'payment_method' => 'd17',
            ]);

        $response->assertStatus(201);

        $order = Order::where('user_id', $this->user->id)->first();

        // Subtotal = 89.90
        $this->assertEquals(89.90, $order->subtotal);

        // Tax = 89.90 * 0.19 = 17.08
        $this->assertEquals(17.08, round($order->tax, 2));

        // Shipping = 7.00
        $this->assertEquals(7.00, $order->shipping_cost);

        // Total = 89.90 + 17.08 + 7.00 = 113.98
        $this->assertEquals(113.98, round($order->total, 2));
    }

    public function test_cannot_order_unavailable_product(): void
    {
        $this->product1->update(['is_available' => false]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/orders', [
                'items' => [
                    ['product_id' => $this->product1->id, 'quantity' => 1],
                ],
                'shipping_address' => [
                    'full_name' => 'John Doe',
                    'phone' => '+216 12 345 678',
                    'address_line' => '123 Rue',
                    'city' => 'Sfax',
                    'postal_code' => '3000',
                ],
                'payment_method' => 'd17',
            ]);

        $response->assertStatus(400);
    }

    public function test_cannot_order_more_than_available_stock(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/orders', [
                'items' => [
                    ['product_id' => $this->product1->id, 'quantity' => 100],
                ],
                'shipping_address' => [
                    'full_name' => 'John Doe',
                    'phone' => '+216 12 345 678',
                    'address_line' => '123 Rue',
                    'city' => 'Sfax',
                    'postal_code' => '3000',
                ],
                'payment_method' => 'd17',
            ]);

        $response->assertStatus(400);
    }

    public function test_can_view_order_history(): void
    {
        // Create some orders
        $order1 = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $this->user->id,
            'status' => 'pending',
            'subtotal' => 100.00,
            'tax' => 19.00,
            'shipping_cost' => 7.00,
            'discount' => 0,
            'total' => 126.00,
            'payment_method' => 'd17',
            'payment_status' => 'pending',
            'shipping_address' => ['city' => 'Sfax'],
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'order_number', 'status', 'total'],
                ],
            ]);
    }

    public function test_can_cancel_pending_order(): void
    {
        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $this->user->id,
            'status' => 'pending',
            'subtotal' => 89.90,
            'tax' => 17.08,
            'shipping_cost' => 7.00,
            'discount' => 0,
            'total' => 113.98,
            'payment_method' => 'd17',
            'payment_status' => 'pending',
            'shipping_address' => ['city' => 'Sfax'],
        ]);

        $order->items()->create([
            'product_id' => $this->product1->id,
            'quantity' => 2,
            'unit_price' => 89.90,
            'total_price' => 179.80,
        ]);

        $this->product1->update(['stock_quantity' => 48]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/orders/{$order->id}/cancel");

        $response->assertStatus(200);

        $order->refresh();
        $this->assertEquals('cancelled', $order->status);

        // Check stock restored
        $this->product1->refresh();
        $this->assertEquals(50, $this->product1->stock_quantity);
    }

    public function test_order_number_is_unique(): void
    {
        $orderNumber1 = Order::generateOrderNumber();
        $orderNumber2 = Order::generateOrderNumber();

        $this->assertNotEquals($orderNumber1, $orderNumber2);
        $this->assertStringStartsWith('CSS-', $orderNumber1);
    }
}
