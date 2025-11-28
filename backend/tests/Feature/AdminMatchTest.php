<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Match;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminMatchTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@css.tn',
            'user_type' => 'admin',
            'is_admin' => true,
        ]);
    }

    public function test_admin_can_list_all_matches(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        Match::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/admin/matches');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'links',
            'meta'
        ]);
        $response->assertJsonCount(5, 'data');
    }

    public function test_admin_can_create_match(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        $matchData = [
            'home_team' => 'CSS',
            'away_team' => 'EST',
            'competition' => 'Ligue 1',
            'season' => '2024/2025',
            'match_date' => '2025-12-01 19:00:00',
            'stadium' => 'Stade Taïeb Mhiri',
            'status' => 'scheduled',
        ];

        $response = $this->postJson('/api/v1/admin/matches', $matchData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'match' => [
                'id',
                'home_team',
                'away_team',
                'match_date',
            ]
        ]);

        $this->assertDatabaseHas('matches', [
            'home_team' => 'CSS',
            'away_team' => 'EST',
        ]);
    }

    public function test_admin_can_update_match_scores(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        $match = Match::factory()->create([
            'status' => 'scheduled',
            'home_score' => null,
            'away_score' => null,
        ]);

        $updateData = [
            'home_score' => 2,
            'away_score' => 1,
            'status' => 'finished',
        ];

        $response = $this->putJson("/api/v1/admin/matches/{$match->id}", $updateData);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Match mis à jour avec succès',
        ]);

        $this->assertDatabaseHas('matches', [
            'id' => $match->id,
            'home_score' => 2,
            'away_score' => 1,
            'status' => 'finished',
        ]);
    }

    public function test_admin_can_filter_matches_by_status(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        Match::factory()->count(3)->create(['status' => 'scheduled']);
        Match::factory()->count(2)->create(['status' => 'finished']);

        $response = $this->getJson('/api/v1/admin/matches?status=scheduled');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_admin_can_delete_match(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        $match = Match::factory()->create();

        $response = $this->deleteJson("/api/v1/admin/matches/{$match->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('matches', [
            'id' => $match->id,
        ]);
    }
}
