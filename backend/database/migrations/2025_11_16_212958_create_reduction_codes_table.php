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
        Schema::create('reduction_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('partner_id')->constrained('partners')->onDelete('cascade');
            $table->foreignId('offer_id')->nullable()->constrained('partner_offers')->onDelete('set null');

            // Code
            $table->string('code')->unique();
            $table->enum('code_type', ['qr', 'promo', 'nfc', 'wallet'])->default('qr');
            $table->string('qr_code_image_url')->nullable();

            // Réduction
            $table->decimal('reduction_value', 8, 2);
            $table->enum('reduction_type', ['percentage', 'fixed'])->default('percentage');

            // Validité
            $table->timestamp('generated_at');
            $table->timestamp('expires_at');

            // Statut
            $table->enum('status', ['active', 'used', 'expired', 'cancelled'])->default('active');

            // Traçabilité
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('code');
            $table->index('user_id');
            $table->index('partner_id');
            $table->index('offer_id');
            $table->index('status');
            $table->index('expires_at');
            $table->index('code_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reduction_codes');
    }
};
