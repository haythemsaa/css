<?php

namespace Tests\Feature;

use App\Models\Poll;
use App\Models\PollVote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PollTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->poll = Poll::create([
            'title' => 'Test Poll',
            'description' => 'Test description',
            'options' => ['Option 1', 'Option 2', 'Option 3'],
            'starts_at' => now(),
            'ends_at' => now()->addDays(7),
            'is_active' => true,
            'allow_multiple' => false,
            'show_results_before_vote' => false,
        ]);
    }

    public function test_can_list_active_polls(): void
    {
        $response = $this->getJson('/api/v1/polls?status=active');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'options', 'is_active']
                ]
            ]);
    }

    public function test_can_view_single_poll(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/polls/{$this->poll->id}");

        $response->assertStatus(200)
            ->assertJson(['title' => 'Test Poll']);
    }

    public function test_authenticated_user_can_vote(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/polls/{$this->poll->id}/vote", [
                'option_index' => 0,
            ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Vote recorded successfully']);
    }

    public function test_cannot_vote_twice_on_single_choice_poll(): void
    {
        $user = User::factory()->create();

        // First vote
        $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/polls/{$this->poll->id}/vote", [
                'option_index' => 0,
            ]);

        // Second vote attempt
        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/polls/{$this->poll->id}/vote", [
                'option_index' => 1,
            ]);

        $response->assertStatus(403);
    }

    public function test_cannot_vote_with_invalid_option(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/polls/{$this->poll->id}/vote", [
                'option_index' => 10, // Invalid option
            ]);

        $response->assertStatus(400);
    }

    public function test_can_view_poll_results_after_voting(): void
    {
        $user = User::factory()->create();

        // Vote first
        PollVote::create([
            'poll_id' => $this->poll->id,
            'user_id' => $user->id,
            'option_index' => 0,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/polls/{$this->poll->id}/results");

        $response->assertStatus(200)
            ->assertJsonStructure(['poll', 'results', 'total_votes']);
    }
}
