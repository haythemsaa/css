<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Badge
            $table->string('badge_type');
            $table->string('badge_name');
            $table->text('badge_description')->nullable();
            $table->string('badge_icon')->nullable();

            // Date
            $table->timestamp('earned_at');

            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('badge_type');
            $table->index('earned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_badges');
    }
};
