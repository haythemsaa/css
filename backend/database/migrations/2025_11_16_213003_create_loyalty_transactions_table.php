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
        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Points
            $table->integer('points');
            $table->enum('type', ['earn', 'spend'])->default('earn');
            $table->text('description')->nullable();

            // Source
            $table->enum('source', ['purchase', 'referral', 'event', 'birthday', 'bonus', 'redemption'])->default('purchase');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_type')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('type');
            $table->index('source');
            $table->index(['reference_id', 'reference_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_transactions');
    }
};
