<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Partner;
use App\Models\PartnerCategory;
use App\Models\PartnerOffer;
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
                         '*' => ['id', 'name', 'slug'],
                     ],
                 ])
                 ->assertJsonCount(5, 'data');
    }

    /**
     * Test can filter partners by category
     */
    public function test_can_filter_partners_by_category(): void
    {
        $category1 = PartnerCategory::factory()->create(['slug' => 'restaurant']);
        $category2 = PartnerCategory::factory()->create(['slug' => 'sport']);
        
        Partner::factory()->count(3)->create(['category_id' => $category1->id]);
        Partner::factory()->count(2)->create(['category_id' => $category2->id]);

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

        PartnerOffer::factory()->count(3)->create(['partner_id' => $partner->id]);

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
        PartnerOffer::factory()->count(5)->create([
            'partner_id' => $partner->id,
            'status' => 'active',
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
        PartnerOffer::factory()->count(3)->create([
            'partner_id' => $partner->id,
            'status' => 'active',
        ]);
        PartnerOffer::factory()->count(2)->create([
            'partner_id' => $partner->id,
            'status' => 'draft',
        ]);

        $response = $this->getJson("/api/v1/partners/{$partner->slug}/offers");

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }
}
