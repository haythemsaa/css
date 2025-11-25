<?php

namespace Tests\Feature;

use App\Models\DonationGoal;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationGoalTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_active_donation_goals(): void
    {
        DonationGoal::factory()->create(['status' => 'active', 'start_date' => now()->subDay()]);
        DonationGoal::factory()->create(['status' => 'completed']);

        $response = $this->getJson('/api/v1/donation-goals');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'target_amount', 'current_amount', 'progress_percentage'],
                ],
            ]);
    }

    public function test_can_view_donation_goal_details(): void
    {
        $goal = DonationGoal::factory()->create([
            'title' => 'Test Goal',
            'slug' => 'test-goal',
            'target_amount' => 10000,
            'current_amount' => 2500,
            'status' => 'active',
            'start_date' => now()->subDay(),
        ]);

        $response = $this->getJson("/api/v1/donation-goals/{$goal->slug}");

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Test Goal',
                'target_amount' => 10000,
                'current_amount' => 2500,
                'progress_percentage' => 25,
            ]);
    }

    public function test_authenticated_user_can_donate(): void
    {
        $user = User::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create(['is_active' => true]);
        $goal = DonationGoal::factory()->create([
            'status' => 'active',
            'target_amount' => 10000,
            'min_donation' => 5,
            'start_date' => now()->subDay(),
        ]);

        $response = $this->actingAs($user)
            ->postJson("/api/v1/donation-goals/{$goal->id}/donate", [
                'amount' => 100,
                'payment_method_id' => $paymentMethod->id,
                'is_anonymous' => false,
                'donor_message' => 'Support from a fan!',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Don effectué avec succès',
            ]);

        $this->assertDatabaseHas('donations', [
            'user_id' => $user->id,
            'goal_id' => $goal->id,
            'amount' => 100,
        ]);
    }

    public function test_cannot_donate_below_minimum(): void
    {
        $user = User::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create();
        $goal = DonationGoal::factory()->create([
            'status' => 'active',
            'min_donation' => 10,
            'start_date' => now()->subDay(),
        ]);

        $response = $this->actingAs($user)
            ->postJson("/api/v1/donation-goals/{$goal->id}/donate", [
                'amount' => 5, // Below minimum
                'payment_method_id' => $paymentMethod->id,
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_can_donate_anonymously(): void
    {
        $user = User::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create();
        $goal = DonationGoal::factory()->create([
            'status' => 'active',
            'allow_anonymous' => true,
            'min_donation' => 5,
            'start_date' => now()->subDay(),
        ]);

        $response = $this->actingAs($user)
            ->postJson("/api/v1/donation-goals/{$goal->id}/donate", [
                'amount' => 50,
                'payment_method_id' => $paymentMethod->id,
                'is_anonymous' => true,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('donations', [
            'user_id' => $user->id,
            'goal_id' => $goal->id,
            'is_anonymous' => true,
        ]);
    }

    public function test_goal_progress_updates_after_donation(): void
    {
        $user = User::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create();
        $goal = DonationGoal::factory()->create([
            'status' => 'active',
            'target_amount' => 1000,
            'current_amount' => 0,
            'donors_count' => 0,
            'min_donation' => 5,
            'start_date' => now()->subDay(),
        ]);

        $this->actingAs($user)
            ->postJson("/api/v1/donation-goals/{$goal->id}/donate", [
                'amount' => 250,
                'payment_method_id' => $paymentMethod->id,
            ]);

        $goal->refresh();
        $this->assertEquals(250, $goal->current_amount);
        $this->assertEquals(1, $goal->donors_count);
        $this->assertEquals(25, $goal->progress_percentage);
    }

    public function test_can_view_my_donations(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/v1/donation-goals/my-donations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
            ]);
    }

    public function test_can_get_donation_categories(): void
    {
        $response = $this->getJson('/api/v1/donation-goals/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'categories' => [
                    '*' => ['key', 'name', 'icon'],
                ],
            ]);
    }

    public function test_goal_marked_completed_when_target_reached(): void
    {
        $user = User::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create();
        $goal = DonationGoal::factory()->create([
            'status' => 'active',
            'target_amount' => 100,
            'current_amount' => 80,
            'min_donation' => 5,
            'start_date' => now()->subDay(),
        ]);

        $this->actingAs($user)
            ->postJson("/api/v1/donation-goals/{$goal->id}/donate", [
                'amount' => 30,
                'payment_method_id' => $paymentMethod->id,
            ]);

        $goal->refresh();
        $this->assertTrue($goal->is_completed);
        $this->assertEquals('completed', $goal->status);
    }

    public function test_milestones_updated_after_donation(): void
    {
        $user = User::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create();
        $goal = DonationGoal::factory()->create([
            'status' => 'active',
            'target_amount' => 1000,
            'current_amount' => 0,
            'min_donation' => 5,
            'start_date' => now()->subDay(),
        ]);

        $milestone = $goal->milestones()->create([
            'title' => '25% Milestone',
            'percentage' => 25,
            'target_amount' => 250,
            'is_achieved' => false,
        ]);

        $this->actingAs($user)
            ->postJson("/api/v1/donation-goals/{$goal->id}/donate", [
                'amount' => 300,
                'payment_method_id' => $paymentMethod->id,
            ]);

        $milestone->refresh();
        $this->assertTrue($milestone->is_achieved);
        $this->assertNotNull($milestone->achieved_at);
    }
}
