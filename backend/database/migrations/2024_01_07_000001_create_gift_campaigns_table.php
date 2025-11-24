<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gift_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            // Campaign type
            $table->enum('type', [
                'monthly_tier',
                'periodic',
                'milestone',
                'lottery',
                'random'
            ]);

            // Eligibility
            $table->enum('eligible_users', ['all', 'premium', 'socios'])->default('all');
            $table->enum('loyalty_level', ['all', 'bronze', 'silver', 'gold', 'platinum'])->default('all');
            $table->integer('min_points')->default(0);

            // Gift details
            $table->json('gifts')->nullable(); // Array of gift items
            $table->integer('total_gifts')->default(0);
            $table->integer('distributed_gifts')->default(0);

            // Dates
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->enum('frequency', ['once', 'daily', 'weekly', 'monthly', 'quarterly', 'yearly'])->default('once');

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('auto_distribute')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('type');
            $table->index('is_active');
            $table->index(['starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_campaigns');
    }
};
