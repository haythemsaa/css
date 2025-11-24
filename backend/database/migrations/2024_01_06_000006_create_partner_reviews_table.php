<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('partner_id')->constrained()->onDelete('cascade');
            $table->foreignId('reduction_usage_id')->nullable()->constrained()->onDelete('set null');

            $table->integer('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->json('images')->nullable();

            // Review details
            $table->boolean('verified_purchase')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_featured')->default(false);

            // Helpful votes
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);

            // Response
            $table->text('partner_response')->nullable();
            $table->timestamp('partner_responded_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('partner_id');
            $table->index('user_id');
            $table->index('rating');
            $table->index('is_approved');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_reviews');
    }
};
