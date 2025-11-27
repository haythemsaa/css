<?php

namespace Tests\Feature;

use App\Models\Challenge;
use App\Models\User;
use App\Models\UserChallenge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChallengeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['points' => 100]);

        $this->challenge = Challenge::create([
            'name' => 'Test Challenge',
            'slug' => 'test-challenge',
            'description' => 'Test challenge description',
            'challenge_type' => 'daily',
            'category' => 'engagement',
            'difficulty' => 'easy',
            'requirements' => ['action' => 'test_action', 'count' => 5],
            'target_value' => 5,
            'points_reward' => 50,
            'additional_rewards' => ['badge' => 'test_badge'],
            'icon' => '🎯',
            'color' => '#FF6B6B',
            'max_completions' => 10,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addWeek(),
            'is_active' => true,
            'is_featured' => false,
        ]);
    }

    public function test_can_list_active_challenges(): void
    {
        $response = $this->getJson('/api/v1/challenges');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'challenge_type', 'difficulty', 'points_reward']
                ]
            ]);
    }

    public function test_can_filter_challenges_by_type(): void
    {
        Challenge::create([
            'name' => 'Weekly Challenge',
            'slug' => 'weekly-challenge',
            'description' => 'Weekly challenge',
            'challenge_type' => 'weekly',
            'category' => 'social',
            'difficulty' => 'medium',
            'requirements' => ['action' => 'share', 'count' => 3],
            'target_value' => 3,
            'points_reward' => 100,
            'starts_at' => now(),
            'ends_at' => now()->addWeek(),
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/challenges?type=weekly');

        $response->assertStatus(200);
        $data = $response->json('data');

        foreach ($data as $challenge) {
            $this->assertEquals('weekly', $challenge['challenge_type']);
        }
    }

    public function test_can_view_single_challenge(): void
    {
        $response = $this->getJson("/api/v1/challenges/{$this->challenge->id}");

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Test Challenge',
                'slug' => 'test-challenge',
            ]);
    }

    public function test_authenticated_user_can_enroll_in_challenge(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/v1/challenges/{$this->challenge->id}/enroll");

        $response->assertStatus(201)
            ->assertJson(['message' => 'Inscription au défi réussie']);

        $this->assertDatabaseHas('user_challenges', [
            'user_id' => $this->user->id,
            'challenge_id' => $this->challenge->id,
        ]);
    }

    public function test_cannot_enroll_in_same_challenge_twice(): void
    {
        // First enrollment
        $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/v1/challenges/{$this->challenge->id}/enroll");

        // Second enrollment attempt
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/v1/challenges/{$this->challenge->id}/enroll");

        $response->assertStatus(400)
            ->assertJson(['message' => 'Vous êtes déjà inscrit à ce défi']);
    }

    public function test_can_update_challenge_progress(): void
    {
        $userChallenge = UserChallenge::create([
            'user_id' => $this->user->id,
            'challenge_id' => $this->challenge->id,
            'target_value' => 5,
            'started_at' => now(),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->patchJson("/api/v1/challenges/user/{$userChallenge->id}/progress", [
                'progress' => 3,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Progression mise à jour',
            ]);

        $this->assertDatabaseHas('user_challenges', [
            'id' => $userChallenge->id,
            'current_progress' => 3,
        ]);
    }

    public function test_challenge_completes_when_target_reached(): void
    {
        $userChallenge = UserChallenge::create([
            'user_id' => $this->user->id,
            'challenge_id' => $this->challenge->id,
            'target_value' => 5,
            'started_at' => now(),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->patchJson("/api/v1/challenges/user/{$userChallenge->id}/progress", [
                'progress' => 5,
            ]);

        $response->assertStatus(200);

        $userChallenge->refresh();
        $this->assertTrue($userChallenge->is_completed);
        $this->assertNotNull($userChallenge->completed_at);
        $this->assertEquals(100, $userChallenge->progress_percentage);
    }

    public function test_can_claim_reward_after_completion(): void
    {
        $userChallenge = UserChallenge::create([
            'user_id' => $this->user->id,
            'challenge_id' => $this->challenge->id,
            'target_value' => 5,
            'current_progress' => 5,
            'is_completed' => true,
            'completed_at' => now(),
            'started_at' => now()->subHour(),
        ]);

        $initialPoints = $this->user->points;

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/v1/challenges/user/{$userChallenge->id}/claim");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Récompense réclamée avec succès']);

        $this->user->refresh();
        $this->assertEquals($initialPoints + $this->challenge->points_reward, $this->user->points);

        $userChallenge->refresh();
        $this->assertTrue($userChallenge->reward_claimed);
    }

    public function test_cannot_claim_reward_twice(): void
    {
        $userChallenge = UserChallenge::create([
            'user_id' => $this->user->id,
            'challenge_id' => $this->challenge->id,
            'target_value' => 5,
            'current_progress' => 5,
            'is_completed' => true,
            'completed_at' => now(),
            'reward_claimed' => true,
            'started_at' => now()->subHour(),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/v1/challenges/user/{$userChallenge->id}/claim");

        $response->assertStatus(400)
            ->assertJson(['message' => 'Récompense déjà réclamée']);
    }

    public function test_can_view_user_challenges(): void
    {
        UserChallenge::create([
            'user_id' => $this->user->id,
            'challenge_id' => $this->challenge->id,
            'target_value' => 5,
            'current_progress' => 2,
            'started_at' => now(),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/challenges/user');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'challenges' => [
                    '*' => ['id', 'challenge', 'current_progress', 'is_completed']
                ]
            ]);
    }

    public function test_challenge_availability_is_based_on_dates(): void
    {
        // Create future challenge
        $futureChallenge = Challenge::create([
            'name' => 'Future Challenge',
            'slug' => 'future-challenge',
            'description' => 'Future challenge',
            'challenge_type' => 'special',
            'category' => 'engagement',
            'difficulty' => 'easy',
            'requirements' => ['action' => 'test'],
            'target_value' => 1,
            'points_reward' => 10,
            'starts_at' => now()->addWeek(),
            'ends_at' => now()->addMonths(2),
            'is_active' => true,
        ]);

        $this->assertFalse($futureChallenge->is_available);

        // Create expired challenge
        $expiredChallenge = Challenge::create([
            'name' => 'Expired Challenge',
            'slug' => 'expired-challenge',
            'description' => 'Expired challenge',
            'challenge_type' => 'special',
            'category' => 'engagement',
            'difficulty' => 'easy',
            'requirements' => ['action' => 'test'],
            'target_value' => 1,
            'points_reward' => 10,
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->subWeek(),
            'is_active' => true,
        ]);

        $this->assertFalse($expiredChallenge->is_available);

        // Current challenge should be available
        $this->assertTrue($this->challenge->is_available);
    }
}
