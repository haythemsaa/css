<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reduction_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reduction_code_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('partner_id')->constrained()->onDelete('cascade');

            // Transaction details
            $table->decimal('original_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('final_amount', 10, 2);
            $table->decimal('commission_earned', 10, 2); // For CSS club

            // Location
            $table->decimal('location_lat', 10, 8)->nullable();
            $table->decimal('location_lng', 11, 8)->nullable();
            $table->boolean('location_verified')->default(false);
            $table->decimal('distance_from_partner', 8, 2)->nullable(); // meters

            // Device info
            $table->string('device_type')->nullable(); // mobile, web
            $table->string('user_agent')->nullable();
            $table->string('ip_address')->nullable();

            // Validation
            $table->string('validated_by')->nullable(); // partner staff name
            $table->timestamp('validated_at');

            // Feedback
            $table->integer('user_satisfaction_rating')->nullable(); // 1-5
            $table->text('user_feedback')->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->index('partner_id');
            $table->index('validated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reduction_usages');
    }
};
