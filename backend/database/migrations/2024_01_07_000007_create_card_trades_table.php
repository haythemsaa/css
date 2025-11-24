<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card_trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('from_card_id')->constrained('user_cards')->onDelete('cascade');
            $table->foreignId('to_card_id')->constrained('user_cards')->onDelete('cascade');

            $table->text('message')->nullable();

            $table->enum('status', [
                'pending',
                'accepted',
                'rejected',
                'cancelled',
                'completed'
            ])->default('pending');

            $table->timestamp('offered_at');
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index('from_user_id');
            $table->index('to_user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('card_trades');
    }
};
