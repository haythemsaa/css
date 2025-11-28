<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminPlayerTest extends TestCase
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

    public function test_admin_can_list_all_players(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        Player::factory()->count(10)->create();

        $response = $this->getJson('/api/v1/admin/players');

        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
    }

    public function test_admin_can_create_player(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        $playerData = [
            'first_name' => 'Mohamed',
            'last_name' => 'Ben Ali',
            'jersey_number' => 10,
            'position' => 'midfielder',
            'date_of_birth' => '1995-05-15',
            'nationality' => 'Tunisia',
            'height' => 178,
            'weight' => 75,
            'is_active' => true,
            'goals' => 0,
            'assists' => 0,
            'matches_played' => 0,
            'yellow_cards' => 0,
            'red_cards' => 0,
        ];

        $response = $this->postJson('/api/v1/admin/players', $playerData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'player' => [
                'id',
                'first_name',
                'last_name',
                'jersey_number',
            ]
        ]);

        $this->assertDatabaseHas('players', [
            'first_name' => 'Mohamed',
            'last_name' => 'Ben Ali',
            'jersey_number' => 10,
        ]);
    }

    public function test_admin_can_update_player_stats(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        $player = Player::factory()->create([
            'goals' => 5,
            'assists' => 3,
            'matches_played' => 10,
        ]);

        $updateData = [
            'goals' => 6,
            'assists' => 4,
            'matches_played' => 11,
        ];

        $response = $this->putJson("/api/v1/admin/players/{$player->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('players', [
            'id' => $player->id,
            'goals' => 6,
            'assists' => 4,
            'matches_played' => 11,
        ]);
    }

    public function test_admin_can_filter_players_by_position(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        Player::factory()->count(3)->create(['position' => 'forward']);
        Player::factory()->count(4)->create(['position' => 'midfielder']);

        $response = $this->getJson('/api/v1/admin/players?position=forward');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_admin_can_deactivate_player(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        $player = Player::factory()->create(['is_active' => true]);

        $response = $this->putJson("/api/v1/admin/players/{$player->id}", [
            'is_active' => false,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('players', [
            'id' => $player->id,
            'is_active' => false,
        ]);
    }
}
