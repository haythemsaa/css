<?php

namespace Tests\Feature;

use App\Models\Partner;
use App\Models\PartnerCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = PartnerCategory::create([
            'name' => 'Restaurants',
            'slug' => 'restaurants',
        ]);
    }

    public function test_can_list_partners(): void
    {
        Partner::create([
            'name' => 'Test Restaurant',
            'description' => 'Test description',
            'city' => 'Sfax',
            'category_id' => $this->category->id,
            'discount_premium' => 15,
            'discount_socios' => 25,
            'latitude' => 34.7406,
            'longitude' => 10.7603,
        ]);

        $response = $this->getJson('/api/v1/partners');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'city', 'discount_premium', 'discount_socios']
                ]
            ]);
    }

    public function test_can_view_single_partner(): void
    {
        $partner = Partner::create([
            'name' => 'Test Restaurant',
            'description' => 'Test description',
            'city' => 'Sfax',
            'category_id' => $this->category->id,
            'discount_premium' => 15,
            'discount_socios' => 25,
            'latitude' => 34.7406,
            'longitude' => 10.7603,
        ]);

        $response = $this->getJson("/api/v1/partners/{$partner->id}");

        $response->assertStatus(200)
            ->assertJson(['name' => 'Test Restaurant']);
    }

    public function test_can_filter_nearby_partners(): void
    {
        Partner::create([
            'name' => 'Nearby Restaurant',
            'description' => 'Test description',
            'city' => 'Sfax',
            'category_id' => $this->category->id,
            'discount_premium' => 15,
            'discount_socios' => 25,
            'latitude' => 34.7406,
            'longitude' => 10.7603,
        ]);

        $response = $this->getJson('/api/v1/partners/nearby?latitude=34.7400&longitude=10.7600&radius=5');

        $response->assertStatus(200);
    }

    public function test_premium_user_can_favorite_partner(): void
    {
        $user = User::factory()->create(['user_type' => 'premium']);
        $partner = Partner::create([
            'name' => 'Test Restaurant',
            'description' => 'Test description',
            'city' => 'Sfax',
            'category_id' => $this->category->id,
            'discount_premium' => 15,
            'discount_socios' => 25,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/partners/{$partner->id}/favorite");

        $response->assertStatus(200);
    }
}
