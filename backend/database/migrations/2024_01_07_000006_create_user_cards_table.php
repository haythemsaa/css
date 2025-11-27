<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('card_id')->constrained('collectible_cards')->onDelete('cascade');

            $table->timestamp('acquired_at');
            $table->enum('acquisition_method', [
                'purchase',
                'lottery',
                'reward',
                'gift',
                'trade'
            ]);

            // Trade status
            $table->boolean('is_locked')->default(false); // Locked during trade
            $table->boolean('is_favorited')->default(false);

            $table->timestamps();

            $table->index('user_id');
            $table->index('card_id');
            $table->index(['user_id', 'card_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_cards');
    }
};
