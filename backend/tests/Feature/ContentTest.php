<?php

namespace Tests\Feature;

use App\Models\Content;
use App\Models\ContentCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->category = ContentCategory::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);
    }

    public function test_can_list_public_contents(): void
    {
        Content::create([
            'title' => 'Free Content',
            'slug' => 'free-content',
            'content' => 'Test content',
            'type' => 'article',
            'status' => 'published',
            'access_level' => 'free',
            'category_id' => $this->category->id,
        ]);

        $response = $this->getJson('/api/v1/contents');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'slug', 'type', 'access_level']
                ]
            ]);
    }

    public function test_can_view_single_content(): void
    {
        $content = Content::create([
            'title' => 'Test Content',
            'slug' => 'test-content',
            'content' => 'Test content body',
            'type' => 'article',
            'status' => 'published',
            'access_level' => 'free',
            'category_id' => $this->category->id,
        ]);

        $response = $this->getJson("/api/v1/contents/{$content->slug}");

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Test Content',
                'slug' => 'test-content',
            ]);
    }

    public function test_premium_content_requires_subscription(): void
    {
        $content = Content::create([
            'title' => 'Premium Content',
            'slug' => 'premium-content',
            'content' => 'Premium content body',
            'type' => 'article',
            'status' => 'published',
            'access_level' => 'premium',
            'category_id' => $this->category->id,
        ]);

        // Free user cannot access
        $freeUser = User::factory()->create(['user_type' => 'free']);

        $response = $this->actingAs($freeUser, 'sanctum')
            ->getJson("/api/v1/contents/{$content->slug}");

        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_like_content(): void
    {
        $user = User::factory()->create();
        $content = Content::create([
            'title' => 'Test Content',
            'slug' => 'test-content',
            'content' => 'Test content body',
            'type' => 'article',
            'status' => 'published',
            'access_level' => 'free',
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/contents/{$content->id}/like");

        $response->assertStatus(200);
    }
}
