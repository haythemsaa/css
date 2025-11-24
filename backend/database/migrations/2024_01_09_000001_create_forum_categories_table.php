<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);

            // Access control
            $table->enum('access_level', ['all', 'premium', 'socios'])->default('all');

            // Stats
            $table->integer('topics_count')->default(0);
            $table->integer('posts_count')->default(0);

            $table->timestamps();

            $table->index('slug');
            $table->index('is_active');
            $table->index('access_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_categories');
    }
};
