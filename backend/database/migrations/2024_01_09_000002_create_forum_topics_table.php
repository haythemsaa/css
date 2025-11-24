<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('forum_categories')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('body');

            // Status
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_approved')->default(true);

            // Stats
            $table->integer('views_count')->default(0);
            $table->integer('replies_count')->default(0);
            $table->integer('likes_count')->default(0);

            // Last activity
            $table->foreignId('last_post_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_post_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('category_id');
            $table->index('user_id');
            $table->index('is_pinned');
            $table->index('is_locked');
            $table->index('last_post_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_topics');
    }
};
