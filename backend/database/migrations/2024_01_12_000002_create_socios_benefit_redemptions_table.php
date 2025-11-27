<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('socios_benefit_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('benefit_id')->constrained('socios_benefits')->onDelete('cascade');

            $table->integer('points_spent')->default(0);
            $table->enum('status', ['pending', 'confirmed', 'used', 'expired', 'cancelled'])->default('pending');

            $table->string('redemption_code')->unique()->nullable();
            $table->timestamp('redeemed_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->index('benefit_id');
            $table->index('status');
            $table->index('redemption_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('socios_benefit_redemptions');
    }
};
