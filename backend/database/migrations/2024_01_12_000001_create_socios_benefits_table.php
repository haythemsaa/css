<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('socios_benefits', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('icon')->nullable();
            $table->string('image')->nullable();

            // Benefit type
            $table->enum('type', [
                'discount',
                'event_invite',
                'merchandise',
                'access',
                'gift',
                'other'
            ])->default('discount');

            // Value
            $table->string('value')->nullable(); // "20%", "VIP Access", etc.
            $table->integer('points_cost')->default(0);

            // Availability
            $table->integer('total_quantity')->nullable(); // null = unlimited
            $table->integer('redeemed_count')->default(0);
            $table->integer('max_per_user')->default(1);

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);

            // Dates
            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_until')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('type');
            $table->index('is_active');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('socios_benefits');
    }
};
