<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('image')->nullable();
            $table->enum('type', [
                'general',
                'equipment',
                'youth_training',
                'stadium',
                'medical',
                'other'
            ])->default('general');

            // Funding
            $table->decimal('goal_amount', 12, 2); // TND
            $table->decimal('current_amount', 12, 2)->default(0);
            $table->decimal('min_donation', 8, 2)->default(5.00);

            // Dates
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');

            // Status
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft');
            $table->boolean('is_featured')->default(false);

            // Stats
            $table->integer('donors_count')->default(0);

            // Transparency
            $table->text('usage_report')->nullable();
            $table->timestamp('report_published_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('status');
            $table->index('is_featured');
            $table->index(['starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
