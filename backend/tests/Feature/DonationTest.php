<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->campaign = Campaign::create([
            'title' => 'Test Campaign',
            'slug' => 'test-campaign',
            'description' => 'Test description',
            'goal_amount' => 10000,
            'current_amount' => 0,
            'starts_at' => now(),
            'ends_at' => now()->addMonths(1),
            'status' => 'active',
        ]);
    }

    public function test_can_list_active_campaigns(): void
    {
        $response = $this->getJson('/api/v1/campaigns');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'goal_amount', 'current_amount']
                ]
            ]);
    }

    public function test_can_view_single_campaign(): void
    {
        $response = $this->getJson("/api/v1/campaigns/{$this->campaign->slug}");

        $response->assertStatus(200)
            ->assertJson(['title' => 'Test Campaign']);
    }

    public function test_authenticated_user_can_donate(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/donations', [
                'campaign_id' => $this->campaign->id,
                'amount' => 50,
                'payment_method' => 'd17',
                'is_anonymous' => false,
            ]);

        $response->assertStatus(201);
    }

    public function test_donation_requires_minimum_amount(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/donations', [
                'campaign_id' => $this->campaign->id,
                'amount' => 2, // Less than minimum (5)
                'payment_method' => 'd17',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);
    }

    public function test_can_view_donation_history(): void
    {
        $user = User::factory()->create();

        Donation::create([
            'user_id' => $user->id,
            'campaign_id' => $this->campaign->id,
            'amount' => 100,
            'payment_method' => 'd17',
            'status' => 'completed',
            'donated_at' => now(),
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/donations/history');

        $response->assertStatus(200);
    }
}
