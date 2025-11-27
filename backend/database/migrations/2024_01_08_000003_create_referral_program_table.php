<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_program', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('referred_id')->constrained('users')->onDelete('cascade');

            // Reward
            $table->integer('referrer_points_earned')->default(0);
            $table->integer('referred_points_earned')->default(0);
            $table->boolean('referrer_rewarded')->default(false);
            $table->boolean('referred_rewarded')->default(false);

            // Status
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('completed_at')->nullable(); // When referred user subscribes

            $table->timestamps();

            $table->index('referrer_id');
            $table->index('referred_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_program');
    }
};
