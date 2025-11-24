<?php

namespace Tests\Feature;

use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\ForumReply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = ForumCategory::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test description',
            'is_active' => true,
        ]);
    }

    public function test_can_list_forum_categories(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/forum/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'name', 'slug', 'description']
            ]);
    }

    public function test_can_list_topics(): void
    {
        $user = User::factory()->create();

        ForumTopic::create([
            'category_id' => $this->category->id,
            'user_id' => $user->id,
            'title' => 'Test Topic',
            'slug' => 'test-topic',
            'content' => 'Test content',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/forum/topics');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_create_topic(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/forum/topics', [
                'category_id' => $this->category->id,
                'title' => 'New Test Topic',
                'content' => 'This is a test topic content',
            ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Topic created successfully']);
    }

    public function test_topic_requires_minimum_content_length(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/forum/topics', [
                'category_id' => $this->category->id,
                'title' => 'Test',
                'content' => 'Short', // Less than 20 characters
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content']);
    }

    public function test_can_reply_to_topic(): void
    {
        $user = User::factory()->create();

        $topic = ForumTopic::create([
            'category_id' => $this->category->id,
            'user_id' => $user->id,
            'title' => 'Test Topic',
            'slug' => 'test-topic',
            'content' => 'Test content',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/forum/topics/{$topic->id}/reply", [
                'content' => 'This is a reply',
            ]);

        $response->assertStatus(201);
    }

    public function test_cannot_reply_to_locked_topic(): void
    {
        $user = User::factory()->create();

        $topic = ForumTopic::create([
            'category_id' => $this->category->id,
            'user_id' => $user->id,
            'title' => 'Locked Topic',
            'slug' => 'locked-topic',
            'content' => 'Test content',
            'is_locked' => true,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/forum/topics/{$topic->id}/reply", [
                'content' => 'This is a reply',
            ]);

        $response->assertStatus(403);
    }
}
