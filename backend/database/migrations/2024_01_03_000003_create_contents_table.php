<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->enum('type', ['article', 'video', 'gallery', 'podcast', 'story', 'live'])->default('article');
            $table->foreignId('category_id')->constrained('content_categories')->onDelete('cascade');
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');

            // Media
            $table->string('featured_image')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_id')->nullable(); // Cloudflare Stream ID
            $table->integer('video_duration')->nullable(); // seconds
            $table->json('gallery_images')->nullable();
            $table->string('podcast_url')->nullable();
            $table->integer('podcast_duration')->nullable();

            // Access control
            $table->enum('access_level', ['free', 'premium', 'socios'])->default('free');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_trending')->default(false);

            // Status
            $table->enum('status', ['draft', 'scheduled', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('meta_keywords')->nullable();

            // Stats
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('shares_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('slug');
            $table->index('type');
            $table->index('access_level');
            $table->index('status');
            $table->index('is_featured');
            $table->index('is_trending');
            $table->index('published_at');
            $table->index('views_count');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
