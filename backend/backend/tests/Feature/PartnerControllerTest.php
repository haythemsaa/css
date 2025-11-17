<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Partner;
use App\Models\Offer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PartnerControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test can get list of partners
     */
    public function test_can_get_partners_list(): void
    {
        Partner::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/partners');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => ['id', 'name', 'slug', 'category', 'city', 'offers_count'],
                     ],
                 ])
                 ->assertJsonCount(5, 'data');
    }

    /**
     * Test can filter partners by category
     */
    public function test_can_filter_partners_by_category(): void
    {
        Partner::factory()->count(3)->create(['category' => 'restaurant']);
        Partner::factory()->count(2)->create(['category' => 'sport']);

        $response = $this->getJson('/api/v1/partners?category=restaurant');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    /**
     * Test can filter partners by city
     */
    public function test_can_filter_partners_by_city(): void
    {
        Partner::factory()->count(4)->create(['city' => 'Sfax']);
        Partner::factory()->count(2)->create(['city' => 'Tunis']);

        $response = $this->getJson('/api/v1/partners?city=Sfax');

        $response->assertStatus(200)
                 ->assertJsonCount(4, 'data');
    }

    /**
     * Test can get partner details by slug
     */
    public function test_can_get_partner_details(): void
    {
        $partner = Partner::factory()->create([
            'name' => 'Test Restaurant',
            'slug' => 'test-restaurant',
        ]);

        Offer::factory()->count(3)->create(['partner_id' => $partner->id]);

        $response = $this->getJson("/api/v1/partners/{$partner->slug}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'slug' => 'test-restaurant',
                         'name' => 'Test Restaurant',
                     ],
                 ])
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'name',
                         'slug',
                         'category',
                         'offers',
                     ],
                 ]);
    }

    /**
     * Test returns 404 for non-existent partner
     */
    public function test_returns_404_for_non_existent_partner(): void
    {
        $response = $this->getJson('/api/v1/partners/non-existent-partner');

        $response->assertStatus(404);
    }

    /**
     * Test can get partner offers
     */
    public function test_can_get_partner_offers(): void
    {
        $partner = Partner::factory()->create();
        Offer::factory()->count(5)->create([
            'partner_id' => $partner->id,
            'is_active' => true,
        ]);

        $response = $this->getJson("/api/v1/partners/{$partner->slug}/offers");

        $response->assertStatus(200)
                 ->assertJsonCount(5, 'data');
    }

    /**
     * Test only returns active offers
     */
    public function test_only_returns_active_offers(): void
    {
        $partner = Partner::factory()->create();
        Offer::factory()->count(3)->create([
            'partner_id' => $partner->id,
            'is_active' => true,
        ]);
        Offer::factory()->count(2)->create([
            'partner_id' => $partner->id,
            'is_active' => false,
        ]);

        $response = $this->getJson("/api/v1/partners/{$partner->slug}/offers");

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }
}
