<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            // Options
            $table->json('options'); // [{id: 1, text: 'Option 1', votes: 0}, ...]

            // Access control
            $table->enum('access_level', ['all', 'premium', 'socios'])->default('all');

            // Settings
            $table->boolean('allow_multiple_choices')->default(false);
            $table->boolean('show_results_before_vote')->default(false);
            $table->boolean('is_anonymous')->default(false);

            // Dates
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);

            // Stats
            $table->integer('total_votes')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
            $table->index('is_featured');
            $table->index(['starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('polls');
    }
};
