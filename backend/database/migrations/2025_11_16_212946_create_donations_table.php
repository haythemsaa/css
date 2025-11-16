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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Don
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['libre', 'ciblé'])->default('libre');

            // Campagne
            $table->foreignId('campaign_id')->nullable()->constrained('gift_campaigns')->onDelete('set null');
            $table->text('message')->nullable();
            $table->boolean('is_anonymous')->default(false);

            // Paiement
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_id');
            $table->index('campaign_id');
            $table->index('type');
            $table->index('status');
            $table->index('is_anonymous');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
