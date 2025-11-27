<?php

namespace Tests\Feature;

use App\Models\AuctionProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuctionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_active_auctions(): void
    {
        AuctionProduct::factory()->create(['status' => 'active', 'start_time' => now()->subDay(), 'end_time' => now()->addDay()]);
        AuctionProduct::factory()->create(['status' => 'ended']);

        $response = $this->getJson('/api/v1/auctions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'current_bid', 'status', 'end_time'],
                ],
                'pagination',
            ]);
    }

    public function test_can_view_auction_details(): void
    {
        $auction = AuctionProduct::factory()->create([
            'title' => 'Test Auction',
            'status' => 'active',
            'starting_price' => 100,
            'start_time' => now()->subDay(),
            'end_time' => now()->addDay(),
        ]);

        $response = $this->getJson("/api/v1/auctions/{$auction->id}");

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Test Auction',
                'starting_price' => 100,
                'status' => 'active',
            ]);
    }

    public function test_authenticated_user_can_place_bid(): void
    {
        $user = User::factory()->create();
        $auction = AuctionProduct::factory()->create([
            'status' => 'active',
            'starting_price' => 100,
            'bid_increment' => 10,
            'current_bid' => 0,
            'start_time' => now()->subDay(),
            'end_time' => now()->addDay(),
        ]);

        $response = $this->actingAs($user)
            ->postJson("/api/v1/auctions/{$auction->id}/bid", [
                'bid_amount' => 100,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Enchère placée avec succès',
            ]);

        $this->assertDatabaseHas('auction_bids', [
            'auction_product_id' => $auction->id,
            'user_id' => $user->id,
            'bid_amount' => 100,
        ]);
    }

    public function test_cannot_place_bid_below_minimum(): void
    {
        $user = User::factory()->create();
        $auction = AuctionProduct::factory()->create([
            'status' => 'active',
            'starting_price' => 100,
            'bid_increment' => 10,
            'current_bid' => 100,
            'start_time' => now()->subDay(),
            'end_time' => now()->addDay(),
        ]);

        $response = $this->actingAs($user)
            ->postJson("/api/v1/auctions/{$auction->id}/bid", [
                'bid_amount' => 105, // Below minimum (100 + 10 = 110)
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_can_buy_now_if_available(): void
    {
        $user = User::factory()->create();
        $auction = AuctionProduct::factory()->create([
            'status' => 'active',
            'starting_price' => 100,
            'buy_now_price' => 500,
            'start_time' => now()->subDay(),
            'end_time' => now()->addDay(),
        ]);

        $response = $this->actingAs($user)
            ->postJson("/api/v1/auctions/{$auction->id}/buy-now");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Achat immédiat effectué avec succès',
            ]);

        $auction->refresh();
        $this->assertEquals('sold', $auction->status);
    }

    public function test_cannot_place_bid_on_inactive_auction(): void
    {
        $user = User::factory()->create();
        $auction = AuctionProduct::factory()->create([
            'status' => 'ended',
            'starting_price' => 100,
        ]);

        $response = $this->actingAs($user)
            ->postJson("/api/v1/auctions/{$auction->id}/bid", [
                'bid_amount' => 100,
            ]);

        $response->assertStatus(422);
    }

    public function test_can_view_my_bids(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $auction = AuctionProduct::factory()->create([
            'status' => 'active',
            'starting_price' => 100,
            'start_time' => now()->subDay(),
            'end_time' => now()->addDay(),
        ]);

        $auction->placeBid($user, 100);

        $response = $this->getJson('/api/v1/auctions/my-bids');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'bid_amount', 'is_winning'],
                ],
            ]);
    }

    public function test_can_view_my_wins(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/v1/auctions/my-wins');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'pagination',
            ]);
    }

    public function test_auction_auto_extends_when_bid_placed_near_end(): void
    {
        $user = User::factory()->create();
        $auction = AuctionProduct::factory()->create([
            'status' => 'active',
            'starting_price' => 100,
            'bid_increment' => 10,
            'current_bid' => 0,
            'auto_extend' => true,
            'auto_extend_minutes' => 5,
            'start_time' => now()->subDay(),
            'end_time' => now()->addMinutes(3), // 3 minutes left
        ]);

        $originalEndTime = $auction->end_time;

        $this->actingAs($user)
            ->postJson("/api/v1/auctions/{$auction->id}/bid", [
                'bid_amount' => 100,
            ]);

        $auction->refresh();
        $this->assertTrue($auction->end_time->greaterThan($originalEndTime));
    }
}
