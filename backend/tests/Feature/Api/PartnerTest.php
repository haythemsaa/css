<?php

namespace Tests\Feature\Api;

use App\Models\Partner;
use App\Models\PartnerCategory;
use App\Models\PartnerOffer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_get_all_partner_categories()
    {
        PartnerCategory::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/partners/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name_fr',
                        'name_ar',
                        'slug',
                        'icon',
                        'color',
                    ],
                ],
            ])
            ->assertJson(['success' => true]);

        $this->assertCount(5, $response->json('data'));
    }

    /** @test */
    public function can_get_all_partners()
    {
        $category = PartnerCategory::factory()->create();
        Partner::factory()->count(10)->create([
            'category_id' => $category->id,
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/v1/partners');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'category',
                        'city',
                        'has_discount',
                        'reduction_value',
                        'status',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function can_filter_partners_by_city()
    {
        $category = PartnerCategory::factory()->create();

        Partner::factory()->count(5)->create([
            'category_id' => $category->id,
            'city' => 'Sfax',
            'status' => 'active',
        ]);

        Partner::factory()->count(3)->create([
            'category_id' => $category->id,
            'city' => 'Tunis',
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/v1/partners?city=Sfax');

        $response->assertStatus(200);
        $this->assertEquals(5, $response->json('meta.total'));
    }

    /** @test */
    public function can_filter_partners_by_category()
    {
        $restaurant = PartnerCategory::factory()->create(['slug' => 'restauration']);
        $shopping = PartnerCategory::factory()->create(['slug' => 'shopping']);

        Partner::factory()->count(7)->create([
            'category_id' => $restaurant->id,
            'status' => 'active',
        ]);

        Partner::factory()->count(3)->create([
            'category_id' => $shopping->id,
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/v1/partners?category=restauration');

        $response->assertStatus(200);
        $this->assertEquals(7, $response->json('meta.total'));
    }

    /** @test */
    public function can_get_featured_partners()
    {
        $category = PartnerCategory::factory()->create();

        Partner::factory()->count(5)->create([
            'category_id' => $category->id,
            'featured_order' => 0,
            'status' => 'active',
        ]);

        Partner::factory()->count(3)->create([
            'category_id' => $category->id,
            'featured_order' => 1,
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/v1/partners/featured');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function can_get_partner_details()
    {
        $category = PartnerCategory::factory()->create();
        $partner = Partner::factory()->create([
            'category_id' => $category->id,
            'slug' => 'test-partner',
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/v1/partners/test-partner');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $partner->id,
                    'name' => $partner->name,
                    'slug' => 'test-partner',
                ],
            ]);
    }

    /** @test */
    public function returns_404_for_non_existent_partner()
    {
        $response = $this->getJson('/api/v1/partners/non-existent');

        $response->assertStatus(404);
    }

    /** @test */
    public function free_user_sees_no_discount()
    {
        $category = PartnerCategory::factory()->create();
        $partner = Partner::factory()->create([
            'category_id' => $category->id,
            'reduction_value_premium' => 15,
            'reduction_value_socios' => 25,
            'status' => 'active',
        ]);

        // Not authenticated (free user)
        $response = $this->getJson('/api/v1/partners');

        $response->assertStatus(200);
        $partnerData = collect($response->json('data'))->firstWhere('id', $partner->id);
        $this->assertFalse($partnerData['has_discount']);
        $this->assertEquals(0, $partnerData['reduction_value']);
    }

    /** @test */
    public function premium_user_sees_premium_discount()
    {
        $category = PartnerCategory::factory()->create();
        $partner = Partner::factory()->create([
            'category_id' => $category->id,
            'reduction_value_premium' => 15,
            'reduction_value_socios' => 25,
            'status' => 'active',
        ]);

        $user = User::factory()->create(['user_type' => 'premium']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/partners');

        $response->assertStatus(200);
        $partnerData = collect($response->json('data'))->firstWhere('id', $partner->id);
        $this->assertTrue($partnerData['has_discount']);
        $this->assertEquals(15, $partnerData['reduction_value']);
    }

    /** @test */
    public function socios_user_sees_socios_discount()
    {
        $category = PartnerCategory::factory()->create();
        $partner = Partner::factory()->create([
            'category_id' => $category->id,
            'reduction_value_premium' => 15,
            'reduction_value_socios' => 25,
            'status' => 'active',
        ]);

        $user = User::factory()->create(['user_type' => 'socios']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/partners');

        $response->assertStatus(200);
        $partnerData = collect($response->json('data'))->firstWhere('id', $partner->id);
        $this->assertTrue($partnerData['has_discount']);
        $this->assertEquals(25, $partnerData['reduction_value']);
    }

    /** @test */
    public function can_get_all_offers()
    {
        $category = PartnerCategory::factory()->create();
        $partner = Partner::factory()->create([
            'category_id' => $category->id,
            'status' => 'active',
        ]);

        PartnerOffer::factory()->count(5)->create([
            'partner_id' => $partner->id,
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/v1/offers');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'slug',
                        'description',
                        'offer_type',
                        'reduction_value',
                        'discount_label',
                        'valid_from',
                        'valid_until',
                        'is_valid',
                        'stock_remaining',
                        'partner',
                    ],
                ],
                'meta',
            ]);
    }

    /** @test */
    public function can_filter_offers_by_type()
    {
        $category = PartnerCategory::factory()->create();
        $partner = Partner::factory()->create([
            'category_id' => $category->id,
            'status' => 'active',
        ]);

        PartnerOffer::factory()->count(3)->create([
            'partner_id' => $partner->id,
            'offer_type' => 'flash',
            'status' => 'active',
        ]);

        PartnerOffer::factory()->count(4)->create([
            'partner_id' => $partner->id,
            'offer_type' => 'standard',
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/v1/offers?type=flash');

        $response->assertStatus(200);
        $this->assertEquals(3, $response->json('meta.total'));
    }

    /** @test */
    public function can_get_offer_details()
    {
        $category = PartnerCategory::factory()->create();
        $partner = Partner::factory()->create([
            'category_id' => $category->id,
            'status' => 'active',
        ]);

        $offer = PartnerOffer::factory()->create([
            'partner_id' => $partner->id,
            'slug' => 'test-offer',
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/v1/offers/test-offer');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $offer->id,
                    'slug' => 'test-offer',
                ],
            ]);
    }
}
