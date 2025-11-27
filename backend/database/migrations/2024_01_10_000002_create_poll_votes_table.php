<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('selected_options'); // [1, 3] for multiple choice
            $table->timestamps();

            $table->index('poll_id');
            $table->index('user_id');
            $table->unique(['poll_id', 'user_id']); // One vote per user per poll
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poll_votes');
    }
};
