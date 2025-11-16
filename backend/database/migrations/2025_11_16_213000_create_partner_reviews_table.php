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
        Schema::create('partner_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('partner_id')->constrained('partners')->onDelete('cascade');

            // Avis
            $table->integer('rating'); // 1-5
            $table->text('comment')->nullable();

            // Lien avec utilisation
            $table->foreignId('reduction_usage_id')->nullable()->constrained('reduction_usages')->onDelete('set null');
            $table->boolean('is_verified_purchase')->default(false);

            // Interactions
            $table->unsignedInteger('helpful_count')->default(0);
            $table->unsignedInteger('reported_count')->default(0);

            // Statut
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_id');
            $table->index('partner_id');
            $table->index('rating');
            $table->index('status');
            $table->index('is_verified_purchase');
            $table->index('reduction_usage_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_reviews');
    }
};
