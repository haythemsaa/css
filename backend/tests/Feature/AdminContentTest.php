<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Content;
use App\Models\ContentCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminContentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'email' => 'admin@css.tn',
            'user_type' => 'admin',
            'is_admin' => true,
        ]);
    }

    public function test_admin_can_list_all_content(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        $response = $this->getJson('/api/v1/admin/content');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'links',
            'meta'
        ]);
    }

    public function test_admin_can_create_content(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        $category = ContentCategory::factory()->create();

        $contentData = [
            'title' => 'Test Article',
            'slug' => 'test-article-' . time(),
            'type' => 'article',
            'category_id' => $category->id,
            'excerpt' => 'Test excerpt',
            'body' => 'Test body content',
            'access_level' => 'free',
            'is_featured' => false,
        ];

        $response = $this->postJson('/api/v1/admin/content', $contentData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'title',
                'slug',
                'type',
            ]
        ]);

        $this->assertDatabaseHas('contents', [
            'title' => 'Test Article',
            'type' => 'article',
        ]);
    }

    public function test_admin_can_update_content(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        $content = Content::factory()->create([
            'author_id' => $this->admin->id,
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'is_featured' => true,
        ];

        $response = $this->putJson("/api/v1/admin/content/{$content->id}", $updateData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('contents', [
            'id' => $content->id,
            'title' => 'Updated Title',
            'is_featured' => true,
        ]);
    }

    public function test_admin_can_delete_content(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        $content = Content::factory()->create([
            'author_id' => $this->admin->id,
        ]);

        $response = $this->deleteJson("/api/v1/admin/content/{$content->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseMissing('contents', [
            'id' => $content->id,
        ]);
    }

    public function test_admin_can_filter_content_by_type(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        Content::factory()->count(3)->create(['type' => 'article']);
        Content::factory()->count(2)->create(['type' => 'video']);

        $response = $this->getJson('/api/v1/admin/content?type=article');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_admin_can_search_content(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        Content::factory()->create(['title' => 'CSS Victory Match']);
        Content::factory()->create(['title' => 'Training Session']);

        $response = $this->getJson('/api/v1/admin/content?search=Victory');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    public function test_non_admin_cannot_access_admin_content_routes(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/v1/admin/content');
        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_admin_content_routes(): void
    {
        $response = $this->getJson('/api/v1/admin/content');
        $response->assertStatus(401);
    }
}
