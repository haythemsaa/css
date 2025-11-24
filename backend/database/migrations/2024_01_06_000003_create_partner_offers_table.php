<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            // Offer type
            $table->enum('type', [
                'standard',
                'flash',
                'seasonal',
                'time_restricted',
                'exclusive'
            ])->default('standard');

            // Discount
            $table->decimal('discount_value_premium', 5, 2)->nullable();
            $table->decimal('discount_value_socios', 5, 2)->nullable();
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('min_purchase_amount', 10, 2)->nullable();

            // Time restrictions
            $table->timestamp('valid_from');
            $table->timestamp('valid_until');
            $table->json('available_days')->nullable(); // [1,2,3,4,5] = Mon-Fri
            $table->time('available_time_start')->nullable();
            $table->time('available_time_end')->nullable();

            // Limits
            $table->integer('max_uses')->nullable();
            $table->integer('current_uses')->default(0);
            $table->integer('max_uses_per_user')->nullable();

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);

            // Access level
            $table->enum('access_level', ['premium', 'socios', 'both'])->default('both');

            $table->timestamps();
            $table->softDeletes();

            $table->index('partner_id');
            $table->index('type');
            $table->index('is_active');
            $table->index('is_featured');
            $table->index(['valid_from', 'valid_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_offers');
    }
};
