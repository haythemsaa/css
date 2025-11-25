<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Auction Products - Produits exclusifs mis aux enchères
        Schema::create('auction_products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->json('images');
            $table->string('category')->default('collectibles'); // collectibles, memorabilia, experiences, signed_items
            $table->decimal('starting_price', 10, 2);
            $table->decimal('reserve_price', 10, 2)->nullable(); // Prix minimum pour valider la vente
            $table->decimal('current_bid', 10, 2)->default(0);
            $table->decimal('buy_now_price', 10, 2)->nullable(); // Option achat immédiat
            $table->integer('bid_increment')->default(5); // Montant minimum d'augmentation (TND)
            $table->integer('total_bids')->default(0);
            $table->foreignId('current_winner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->boolean('is_featured')->default(false);
            $table->boolean('auto_extend')->default(true); // Prolonge de 5 min si enchère dans les dernières minutes
            $table->integer('auto_extend_minutes')->default(5);
            $table->string('status')->default('scheduled'); // scheduled, active, ended, cancelled, sold
            $table->text('terms_conditions')->nullable();
            $table->json('metadata')->nullable(); // Certificat d'authenticité, provenance, etc.
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('end_time');
            $table->index(['category', 'status']);
        });

        // Auction Bids - Enchères des utilisateurs
        Schema::create('auction_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('bid_amount', 10, 2);
            $table->boolean('is_auto_bid')->default(false); // Enchère automatique
            $table->decimal('max_auto_bid', 10, 2)->nullable(); // Montant max pour enchères auto
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->boolean('is_winning')->default(false); // Enchère actuellement gagnante
            $table->boolean('was_outbid')->default(false); // Utilisateur surenchéri
            $table->timestamp('outbid_at')->nullable();
            $table->timestamps();

            $table->index(['auction_product_id', 'created_at']);
            $table->index(['user_id', 'is_winning']);
        });

        // Auction Winners - Gagnants des enchères
        Schema::create('auction_winners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('winning_bid_id')->constrained('auction_bids')->cascadeOnDelete();
            $table->decimal('final_price', 10, 2);
            $table->string('payment_status')->default('pending'); // pending, paid, failed, refunded
            $table->foreignId('payment_method_id')->nullable()->constrained()->nullOnDelete();
            $table->string('transaction_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('delivery_status')->default('pending'); // pending, shipped, delivered, picked_up
            $table->string('tracking_number')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'payment_status']);
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auction_winners');
        Schema::dropIfExists('auction_bids');
        Schema::dropIfExists('auction_products');
    }
};
