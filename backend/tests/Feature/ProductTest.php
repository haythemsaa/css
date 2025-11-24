<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->product = Product::create([
            'name' => 'Maillot Test CSS',
            'slug' => 'maillot-test-css',
            'description' => 'Maillot de test',
            'category' => 'jerseys',
            'sku' => 'TEST-001',
            'price' => 89.90,
            'stock_quantity' => 10,
            'is_available' => true,
        ]);
    }

    public function test_can_list_products(): void
    {
        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'price', 'category']
                ]
            ]);
    }

    public function test_can_filter_products_by_category(): void
    {
        Product::create([
            'name' => 'Écharpe Test',
            'slug' => 'echarpe-test',
            'description' => 'Écharpe de test',
            'category' => 'merchandise',
            'sku' => 'TEST-002',
            'price' => 19.90,
            'stock_quantity' => 20,
            'is_available' => true,
        ]);

        $response = $this->getJson('/api/v1/products?category=merchandise');

        $response->assertStatus(200);
        $data = $response->json('data');

        foreach ($data as $product) {
            $this->assertEquals('merchandise', $product['category']);
        }
    }

    public function test_can_view_single_product(): void
    {
        $response = $this->getJson("/api/v1/products/{$this->product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Maillot Test CSS',
                'price' => 89.90,
            ]);

        // Check views counter incremented
        $this->product->refresh();
        $this->assertEquals(1, $this->product->views_count);
    }

    public function test_sale_price_is_used_as_current_price(): void
    {
        $this->product->update(['sale_price' => 69.90]);

        $this->assertEquals(69.90, $this->product->current_price);
        $this->assertEquals(22.24, $this->product->discount_percentage);
    }

    public function test_can_add_product_review(): void
    {
        // Create an order to mark as verified purchase
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'payment_status' => 'paid',
        ]);

        $order->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => $this->product->price,
            'total_price' => $this->product->price,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/v1/products/{$this->product->id}/reviews", [
                'rating' => 5,
                'comment' => 'Excellent produit !',
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('product_reviews', [
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'rating' => 5,
            'is_verified_purchase' => true,
        ]);
    }

    public function test_cannot_review_product_twice(): void
    {
        ProductReview::create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'rating' => 4,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/v1/products/{$this->product->id}/reviews", [
                'rating' => 5,
                'comment' => 'Deuxième review',
            ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'Vous avez déjà évalué ce produit']);
    }

    public function test_product_rating_is_updated_after_review(): void
    {
        ProductReview::create([
            'product_id' => $this->product->id,
            'user_id' => User::factory()->create()->id,
            'rating' => 5,
        ]);

        ProductReview::create([
            'product_id' => $this->product->id,
            'user_id' => User::factory()->create()->id,
            'rating' => 3,
        ]);

        $this->product->refresh();
        $this->assertEquals(4.0, $this->product->average_rating);
        $this->assertEquals(2, $this->product->reviews_count);
    }

    public function test_stock_is_decremented_correctly(): void
    {
        $initialStock = $this->product->stock_quantity;

        $success = $this->product->decrementStock(3);

        $this->assertTrue($success);
        $this->product->refresh();
        $this->assertEquals($initialStock - 3, $this->product->stock_quantity);
        $this->assertEquals(1, $this->product->sales_count);
    }

    public function test_cannot_decrement_more_than_available_stock(): void
    {
        $success = $this->product->decrementStock(20);

        $this->assertFalse($success);
        $this->product->refresh();
        $this->assertEquals(10, $this->product->stock_quantity);
    }
}
