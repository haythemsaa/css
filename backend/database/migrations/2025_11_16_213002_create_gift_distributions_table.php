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
        Schema::create('gift_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('gift_campaigns')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Cadeau
            $table->enum('gift_type', ['physical', 'digital', 'points', 'voucher'])->default('physical');
            $table->text('gift_description')->nullable();
            $table->decimal('gift_value', 10, 2)->nullable();
            $table->string('physical_item')->nullable();

            // Livraison
            $table->enum('delivery_method', ['mail', 'email', 'pickup', 'digital'])->default('mail');
            $table->enum('delivery_status', ['pending', 'processing', 'shipped', 'delivered', 'failed'])->default('pending');
            $table->string('tracking_number')->nullable();
            $table->text('delivery_address')->nullable();

            // Dates
            $table->timestamp('distributed_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('campaign_id');
            $table->index('user_id');
            $table->index('gift_type');
            $table->index('delivery_status');
            $table->index('distributed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_distributions');
    }
};
