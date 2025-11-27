<?php

namespace Database\Seeders;

use App\Models\DiscountCode;
use Illuminate\Database\Seeder;

class DiscountCodeSeeder extends Seeder
{
    public function run(): void
    {
        $codes = [
            [
                'code' => 'WELCOME2024',
                'name' => 'Bienvenue Nouveaux Socios',
                'description' => 'Code de bienvenue pour les nouveaux membres Socios',
                'discount_type' => 'percentage',
                'discount_value' => 15.00,
                'min_purchase_amount' => 50.00,
                'max_discount_amount' => 30.00,
                'usage_limit' => 1000,
                'usage_limit_per_user' => 1,
                'usage_count' => 0,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(3),
                'is_active' => true,
                'user_type' => 'all',
            ],
            [
                'code' => 'CSS100',
                'name' => 'Centenaire CSS',
                'description' => 'Célébration du centenaire du club',
                'discount_type' => 'fixed',
                'discount_value' => 10.00,
                'min_purchase_amount' => 30.00,
                'max_discount_amount' => null,
                'usage_limit' => 5000,
                'usage_limit_per_user' => 3,
                'usage_count' => 0,
                'valid_from' => now(),
                'valid_until' => now()->addYear(),
                'is_active' => true,
                'applicable_categories' => ['maillots', 'accessories', 'souvenirs'],
                'user_type' => 'all',
            ],
            [
                'code' => 'SOCIOS50',
                'name' => 'Réduction Socios Exclusif',
                'description' => 'Code exclusif pour les membres Socios vérifiés',
                'discount_type' => 'percentage',
                'discount_value' => 50.00,
                'min_purchase_amount' => 100.00,
                'max_discount_amount' => 150.00,
                'usage_limit' => null,
                'usage_limit_per_user' => 2,
                'usage_count' => 0,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(6),
                'is_active' => true,
                'user_type' => 'socios',
            ],
            [
                'code' => 'BLACKFRIDAY',
                'name' => 'Black Friday CSS',
                'description' => 'Offre spéciale Black Friday',
                'discount_type' => 'percentage',
                'discount_value' => 30.00,
                'min_purchase_amount' => 0,
                'max_discount_amount' => 200.00,
                'usage_limit' => 10000,
                'usage_limit_per_user' => 1,
                'usage_count' => 0,
                'valid_from' => now()->addMonths(2),
                'valid_until' => now()->addMonths(2)->addDays(3),
                'is_active' => false,
                'user_type' => 'all',
            ],
            [
                'code' => 'FREESHIP',
                'name' => 'Livraison Gratuite',
                'description' => 'Livraison gratuite pour toute commande',
                'discount_type' => 'free_shipping',
                'discount_value' => 0,
                'min_purchase_amount' => 75.00,
                'max_discount_amount' => null,
                'usage_limit' => null,
                'usage_limit_per_user' => 5,
                'usage_count' => 0,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(12),
                'is_active' => true,
                'user_type' => 'all',
            ],
            [
                'code' => 'PREMIUM20',
                'name' => 'Réduction Premium',
                'description' => 'Code exclusif pour les membres Premium',
                'discount_type' => 'percentage',
                'discount_value' => 20.00,
                'min_purchase_amount' => 50.00,
                'max_discount_amount' => 100.00,
                'usage_limit' => null,
                'usage_limit_per_user' => 10,
                'usage_count' => 0,
                'valid_from' => now(),
                'valid_until' => null,
                'is_active' => true,
                'user_type' => 'premium',
            ],
            [
                'code' => 'MATCHDAY10',
                'name' => 'Jour de Match',
                'description' => 'Réduction spéciale les jours de match à domicile',
                'discount_type' => 'percentage',
                'discount_value' => 10.00,
                'min_purchase_amount' => 25.00,
                'max_discount_amount' => 50.00,
                'usage_limit' => 2000,
                'usage_limit_per_user' => 5,
                'usage_count' => 0,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(6),
                'is_active' => true,
                'applicable_categories' => ['maillots', 'echarpes'],
                'user_type' => 'all',
            ],
        ];

        foreach ($codes as $code) {
            DiscountCode::create($code);
        }

        $this->command->info('7 codes de réduction créés avec succès');
    }
}
