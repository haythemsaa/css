<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // User Follow System
        Schema::create('user_follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follower_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('following_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['follower_id', 'following_id']);
            $table->index(['follower_id']);
            $table->index(['following_id']);
        });

        // Social Timeline/Feed
        Schema::create('timeline_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->string('post_type')->default('text'); // 'text', 'image', 'video', 'achievement'
            $table->json('media')->nullable(); // Images/videos
            $table->string('entity_type')->nullable(); // Related entity
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['entity_type', 'entity_id']);
        });

        // Post Likes
        Schema::create('timeline_post_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('timeline_posts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['post_id', 'user_id']);
        });

        // Post Comments
        Schema::create('timeline_post_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('timeline_posts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('comment');
            $table->foreignId('parent_id')->nullable()->constrained('timeline_post_comments')->onDelete('cascade');
            $table->integer('likes_count')->default(0);
            $table->timestamps();

            $table->index(['post_id', 'created_at']);
            $table->index(['parent_id']);
        });

        // Wishlist (Products)
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('priority')->default(0); // User can prioritize items
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'product_id']);
            $table->index(['user_id', 'priority']);
        });

        // Saved Content (Bookmarks)
        Schema::create('saved_content', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('content_id')->constrained()->onDelete('cascade');
            $table->string('collection')->default('default'); // Users can organize in collections
            $table->timestamps();

            $table->unique(['user_id', 'content_id']);
            $table->index(['user_id', 'collection']);
        });

        // User Achievements Showcase
        Schema::create('achievement_showcases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('achievement_type'); // 'badge', 'challenge', 'milestone'
            $table->unsignedBigInteger('achievement_id');
            $table->integer('display_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'display_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievement_showcases');
        Schema::dropIfExists('saved_content');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('timeline_post_comments');
        Schema::dropIfExists('timeline_post_likes');
        Schema::dropIfExists('timeline_posts');
        Schema::dropIfExists('user_follows');
    }
};
