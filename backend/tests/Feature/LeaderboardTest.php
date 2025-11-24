<?php

namespace Tests\Feature;

use App\Models\Leaderboard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaderboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->users = User::factory()->count(10)->create();

        // Create leaderboard entries
        foreach ($this->users as $index => $user) {
            Leaderboard::create([
                'user_id' => $user->id,
                'leaderboard_type' => 'points',
                'score' => ($index + 1) * 100,
                'rank' => $index + 1,
                'previous_rank' => $index + 2,
                'period' => 'all_time',
                'last_updated_at' => now(),
            ]);
        }
    }

    public function test_can_list_leaderboards(): void
    {
        $response = $this->getJson('/api/v1/leaderboards?type=points&period=all_time');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'user', 'score', 'rank', 'leaderboard_type']
                ]
            ]);
    }

    public function test_can_filter_leaderboards_by_type(): void
    {
        // Create donations leaderboard
        Leaderboard::create([
            'user_id' => $this->users->first()->id,
            'leaderboard_type' => 'donations',
            'score' => 500,
            'rank' => 1,
            'period' => 'all_time',
            'last_updated_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/leaderboards?type=donations&period=all_time');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_authenticated_user_can_view_own_rank(): void
    {
        $user = $this->users->first();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/leaderboards/my-rank?type=points&period=all_time');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'ranking' => ['id', 'score', 'rank'],
                'total_users',
            ]);
    }

    public function test_can_view_leaderboard_stats(): void
    {
        $response = $this->getJson('/api/v1/leaderboards/stats?type=points&period=all_time');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'type',
                'period',
                'total_users',
                'average_score',
                'highest_score',
                'lowest_score',
                'top_3',
            ]);
    }

    public function test_can_compare_users(): void
    {
        $user1 = $this->users->first();
        $user2 = $this->users->last();

        $response = $this->actingAs($user1, 'sanctum')
            ->getJson("/api/v1/leaderboards/compare/{$user2->id}?type=points&period=all_time");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user',
                'other_user',
                'comparison' => [
                    'score_difference',
                    'rank_difference',
                    'user_ahead',
                ],
            ]);
    }

    public function test_rank_change_is_calculated_correctly(): void
    {
        $leaderboard = Leaderboard::first();

        $this->assertNotNull($leaderboard->rank_change);
        $this->assertEquals($leaderboard->previous_rank - $leaderboard->rank, $leaderboard->rank_change);
    }

    public function test_rank_trend_is_determined_correctly(): void
    {
        // Create leaderboard with upward trend (rank improved)
        $upwardLeaderboard = Leaderboard::create([
            'user_id' => User::factory()->create()->id,
            'leaderboard_type' => 'points',
            'score' => 5000,
            'rank' => 5,
            'previous_rank' => 10,
            'period' => 'weekly',
            'period_start' => now()->startOfWeek(),
            'period_end' => now()->endOfWeek(),
            'last_updated_at' => now(),
        ]);

        $this->assertEquals('up', $upwardLeaderboard->rank_trend);
    }
}
