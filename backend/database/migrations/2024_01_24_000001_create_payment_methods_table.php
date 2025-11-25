<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Payment Methods - Toutes les méthodes de paiement disponibles en Tunisie
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // D17, Konnect, Flouci, etc.
            $table->string('code')->unique(); // d17, konnect, flouci, clictopay
            $table->string('type'); // mobile_wallet, bank_card, bank_transfer, cash, international
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('provider')->nullable(); // Nom du fournisseur
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->json('supported_currencies')->default('["TND"]'); // TND, EUR, USD
            $table->decimal('min_amount', 10, 2)->nullable(); // Montant minimum
            $table->decimal('max_amount', 10, 2)->nullable(); // Montant maximum
            $table->decimal('transaction_fee', 10, 2)->default(0); // Frais fixe
            $table->decimal('transaction_fee_percentage', 5, 2)->default(0); // Frais en %
            $table->json('config')->nullable(); // Configuration API (clés, endpoints, etc.)
            $table->string('processing_time')->nullable(); // Instantané, 24h, 3 jours, etc.
            $table->boolean('supports_refund')->default(true);
            $table->text('instructions')->nullable(); // Instructions pour l'utilisateur
            $table->integer('display_order')->default(0);
            $table->json('available_for')->default('["donations", "products", "tickets", "auctions", "subscriptions"]');
            $table->timestamps();
            $table->softDeletes();

            $table->index('code');
            $table->index('type');
            $table->index(['is_active', 'display_order']);
        });

        // Payment Transactions - Historique détaillé de toutes les transactions
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_method_id')->constrained()->cascadeOnDelete();
            $table->morphs('payable'); // donations, orders, tickets, auctions, subscriptions
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('TND');
            $table->decimal('transaction_fee', 10, 2)->default(0);
            $table->decimal('net_amount', 12, 2); // Montant après frais
            $table->string('status')->default('pending'); // pending, processing, completed, failed, refunded, cancelled
            $table->string('payment_gateway')->nullable(); // d17, konnect, stripe, etc.
            $table->string('gateway_transaction_id')->nullable();
            $table->string('gateway_reference')->nullable();
            $table->json('gateway_response')->nullable();
            $table->string('failure_reason')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->string('refund_reference')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('status');
            $table->index(['payable_type', 'payable_id']);
            $table->index('created_at');
        });

        // Ajouter payment_transaction_id aux tables existantes
        $tables = ['donations', 'orders', 'ticket_purchases', 'subscriptions'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('payment_transaction_id')->nullable()->after('id')->constrained('payment_transactions')->nullOnDelete();
                    $table->index('payment_transaction_id');
                });
            }
        }
    }

    public function down(): void
    {
        $tables = ['donations', 'orders', 'ticket_purchases', 'subscriptions'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['payment_transaction_id']);
                    $table->dropColumn('payment_transaction_id');
                });
            }
        }

        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('payment_methods');
    }
};
