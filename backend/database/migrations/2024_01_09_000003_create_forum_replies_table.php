<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('forum_topics')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('forum_replies')->onDelete('cascade');

            $table->longText('body');

            // Status
            $table->boolean('is_approved')->default(true);
            $table->boolean('is_solution')->default(false);

            // Stats
            $table->integer('likes_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('topic_id');
            $table->index('user_id');
            $table->index('parent_id');
            $table->index('is_approved');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_replies');
    }
};
