<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
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

    public function test_authenticated_user_can_view_cart(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/cart');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'cart' => ['id', 'items', 'items_count', 'total'],
            ]);
    }

    public function test_can_add_item_to_cart(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/cart/items', [
                'product_id' => $this->product->id,
                'quantity' => 2,
            ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Produit ajouté au panier']);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);
    }

    public function test_adding_existing_item_increments_quantity(): void
    {
        $cart = Cart::create(['user_id' => $this->user->id]);
        $cart->addItem($this->product->id, 2);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/cart/items', [
                'product_id' => $this->product->id,
                'quantity' => 3,
            ]);

        $response->assertStatus(200);

        $cart->refresh();
        $item = $cart->items()->where('product_id', $this->product->id)->first();

        $this->assertEquals(5, $item->quantity);
    }

    public function test_cannot_add_unavailable_product_to_cart(): void
    {
        $this->product->update(['is_available' => false]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/cart/items', [
                'product_id' => $this->product->id,
                'quantity' => 1,
            ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'Ce produit n\'est pas disponible']);
    }

    public function test_cannot_add_more_than_stock_to_cart(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/cart/items', [
                'product_id' => $this->product->id,
                'quantity' => 20,
            ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'Stock insuffisant']);
    }

    public function test_can_update_cart_item_quantity(): void
    {
        $cart = Cart::create(['user_id' => $this->user->id]);
        $cart->addItem($this->product->id, 2);

        $response = $this->actingAs($this->user, 'sanctum')
            ->patchJson("/api/v1/cart/items/{$this->product->id}", [
                'quantity' => 5,
            ]);

        $response->assertStatus(200);

        $cart->refresh();
        $item = $cart->items()->where('product_id', $this->product->id)->first();

        $this->assertEquals(5, $item->quantity);
    }

    public function test_setting_quantity_to_zero_removes_item(): void
    {
        $cart = Cart::create(['user_id' => $this->user->id]);
        $cart->addItem($this->product->id, 2);

        $response = $this->actingAs($this->user, 'sanctum')
            ->patchJson("/api/v1/cart/items/{$this->product->id}", [
                'quantity' => 0,
            ]);

        $response->assertStatus(200);

        $cart->refresh();
        $this->assertEquals(0, $cart->items()->count());
    }

    public function test_can_remove_item_from_cart(): void
    {
        $cart = Cart::create(['user_id' => $this->user->id]);
        $cart->addItem($this->product->id, 2);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/cart/items/{$this->product->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Produit retiré du panier']);

        $cart->refresh();
        $this->assertEquals(0, $cart->items()->count());
    }

    public function test_can_clear_cart(): void
    {
        $cart = Cart::create(['user_id' => $this->user->id]);
        $cart->addItem($this->product->id, 2);

        $product2 = Product::create([
            'name' => 'Écharpe Test',
            'slug' => 'echarpe-test',
            'description' => 'Test',
            'category' => 'merchandise',
            'sku' => 'TEST-002',
            'price' => 19.90,
            'stock_quantity' => 50,
            'is_available' => true,
        ]);

        $cart->addItem($product2->id, 1);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson('/api/v1/cart');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Panier vidé']);

        $cart->refresh();
        $this->assertEquals(0, $cart->items()->count());
    }

    public function test_cart_total_is_calculated_correctly(): void
    {
        $cart = Cart::create(['user_id' => $this->user->id]);

        $product2 = Product::create([
            'name' => 'Écharpe Test',
            'slug' => 'echarpe-test',
            'description' => 'Test',
            'category' => 'merchandise',
            'sku' => 'TEST-002',
            'price' => 20.00,
            'stock_quantity' => 50,
            'is_available' => true,
        ]);

        $cart->addItem($this->product->id, 2); // 89.90 * 2 = 179.80
        $cart->addItem($product2->id, 3);      // 20.00 * 3 = 60.00

        $total = $cart->getTotal();

        $this->assertEquals(239.80, $total);
    }

    public function test_cart_items_count_is_correct(): void
    {
        $cart = Cart::create(['user_id' => $this->user->id]);
        $cart->addItem($this->product->id, 2);

        $product2 = Product::create([
            'name' => 'Écharpe Test',
            'slug' => 'echarpe-test',
            'description' => 'Test',
            'category' => 'merchandise',
            'sku' => 'TEST-002',
            'price' => 20.00,
            'stock_quantity' => 50,
            'is_available' => true,
        ]);

        $cart->addItem($product2->id, 3);

        $itemsCount = $cart->getItemsCount();

        $this->assertEquals(5, $itemsCount); // 2 + 3
    }
}
