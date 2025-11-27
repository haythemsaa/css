<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reduction_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('partner_id')->constrained()->onDelete('cascade');
            $table->foreignId('offer_id')->nullable()->constrained('partner_offers')->onDelete('set null');

            // Code details
            $table->string('code', 20)->unique();
            $table->string('qr_code')->nullable(); // Path to QR code image
            $table->enum('type', ['qr', 'promo', 'nfc'])->default('qr');

            // Discount info
            $table->decimal('discount_percentage', 5, 2);
            $table->decimal('max_discount_amount', 10, 2)->nullable();

            // Validity
            $table->timestamp('generated_at');
            $table->timestamp('expires_at');
            $table->boolean('is_active')->default(true);

            // Usage
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->decimal('original_amount', 10, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('final_amount', 10, 2)->nullable();

            // Location verification
            $table->decimal('usage_latitude', 10, 8)->nullable();
            $table->decimal('usage_longitude', 11, 8)->nullable();
            $table->boolean('location_verified')->default(false);

            $table->timestamps();

            $table->index('code');
            $table->index('user_id');
            $table->index('partner_id');
            $table->index('is_used');
            $table->index('expires_at');
            $table->index(['generated_at', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reduction_codes');
    }
};
