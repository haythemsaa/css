<?php

namespace Database\Seeders;

use App\Models\SociosBenefit;
use Illuminate\Database\Seeder;

class SociosBenefitSeeder extends Seeder
{
    public function run(): void
    {
        $benefits = [
            [
                'name' => 'Accès VIP au stade',
                'description' => 'Accès aux loges VIP pour tous les matchs à domicile',
                'benefit_type' => 'access',
                'value' => 'vip_lounge',
                'points_cost' => 0, // Gratuit pour tous les Socios
                'stock' => null, // Illimité
                'stock_used' => 0,
                'starts_at' => now(),
                'expires_at' => now()->addYear(),
                'is_active' => true,
                'terms' => 'Présentation de la carte Socios obligatoire',
            ],
            [
                'name' => 'Maillot CSS officiel personnalisé',
                'description' => 'Maillot officiel avec votre nom et numéro préféré',
                'benefit_type' => 'merchandise',
                'value' => 'custom_jersey',
                'points_cost' => 500,
                'stock' => 100,
                'stock_used' => 0,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(6),
                'is_active' => true,
                'terms' => 'Disponible en tailles S, M, L, XL, XXL',
            ],
            [
                'name' => 'Rencontre avec les joueurs',
                'description' => 'Séance photos et autographes avec l\'équipe',
                'benefit_type' => 'event',
                'value' => 'meet_and_greet',
                'points_cost' => 1000,
                'stock' => 50,
                'stock_used' => 0,
                'starts_at' => now()->addMonth(),
                'expires_at' => now()->addMonths(2),
                'is_active' => true,
                'terms' => 'Événement organisé au siège du club',
            ],
            [
                'name' => 'Pack cadeau Socios',
                'description' => 'Pack contenant écharpe, casquette et porte-clés CSS',
                'benefit_type' => 'merchandise',
                'value' => 'socios_pack',
                'points_cost' => 200,
                'stock' => 200,
                'stock_used' => 0,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(3),
                'is_active' => true,
                'terms' => 'Un pack par Socios',
            ],
            [
                'name' => 'Invitation match de gala',
                'description' => 'Invitation pour 2 personnes au match de gala annuel',
                'benefit_type' => 'event',
                'value' => 'gala_match',
                'points_cost' => 750,
                'stock' => 150,
                'stock_used' => 0,
                'starts_at' => now()->addMonths(2),
                'expires_at' => now()->addMonths(4),
                'is_active' => true,
                'terms' => 'Incluant dîner de gala',
            ],
            [
                'name' => 'Visite guidée du stade',
                'description' => 'Visite exclusive des coulisses du stade Taïeb Mhiri',
                'benefit_type' => 'experience',
                'value' => 'stadium_tour',
                'points_cost' => 100,
                'stock' => null, // Illimité
                'stock_used' => 0,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(12),
                'is_active' => true,
                'terms' => 'Sur réservation, disponible les mercredis et samedis',
            ],
        ];

        foreach ($benefits as $benefit) {
            SociosBenefit::create($benefit);
        }

        $this->command->info('Avantages Socios créés avec succès');
    }
}
