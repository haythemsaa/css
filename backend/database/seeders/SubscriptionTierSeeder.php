<?php

namespace Database\Seeders;

use App\Models\SubscriptionTier;
use Illuminate\Database\Seeder;

class SubscriptionTierSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Accès gratuit aux fonctionnalités de base',
                'monthly_price' => 0,
                'yearly_price' => 0,
                'features' => [
                    'Accès aux actualités gratuites',
                    'Notifications de matchs',
                    'Classements et statistiques de base',
                    'Accès à la boutique',
                ],
                'benefits' => [
                    'Application mobile gratuite',
                    'Newsletter hebdomadaire',
                ],
                'badge_color' => '#6B7280',
                'badge_icon' => 'star',
                'points_multiplier' => 1,
                'discount_percentage' => 0,
                'priority_support' => false,
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'name' => 'Bronze',
                'slug' => 'bronze',
                'description' => 'Pour les supporters engagés',
                'monthly_price' => 9.90,
                'yearly_price' => 99.00,
                'features' => [
                    'Tout du plan Free',
                    'Accès aux contenus Premium',
                    'Replay des matchs (48h)',
                    '5% de réduction boutique',
                    'Badge Bronze sur le profil',
                ],
                'benefits' => [
                    'Priorité modérée pour les billets',
                    'Concours et tirages au sort exclusifs',
                    'Wallpapers exclusifs mensuels',
                ],
                'badge_color' => '#CD7F32',
                'badge_icon' => 'medal',
                'points_multiplier' => 2,
                'discount_percentage' => 5,
                'priority_support' => false,
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'name' => 'Silver',
                'slug' => 'silver',
                'description' => 'Pour les vrais fans du CSS',
                'monthly_price' => 19.90,
                'yearly_price' => 199.00,
                'features' => [
                    'Tout du plan Bronze',
                    'Replay illimité des matchs',
                    'Contenus exclusifs coulisses',
                    '10% de réduction boutique',
                    'Badge Silver personnalisé',
                    'Vote pour le Joueur du Mois',
                ],
                'benefits' => [
                    'Priorité élevée pour les billets',
                    'Invitation à 2 événements/an',
                    'Meet & Greet annuel',
                    'Carte de membre physique',
                ],
                'badge_color' => '#C0C0C0',
                'badge_icon' => 'trophy',
                'points_multiplier' => 3,
                'discount_percentage' => 10,
                'priority_support' => true,
                'is_active' => true,
                'display_order' => 3,
            ],
            [
                'name' => 'Gold',
                'slug' => 'gold',
                'description' => 'L\'expérience VIP complète',
                'monthly_price' => 39.90,
                'yearly_price' => 399.00,
                'features' => [
                    'Tout du plan Silver',
                    'Accès VIP au stade (loges)',
                    'Streaming HD multi-caméras',
                    '20% de réduction boutique',
                    'Badge Gold animé',
                    'Participation Assemblée Générale',
                ],
                'benefits' => [
                    'Garantie billets saison complète',
                    'Invitation à tous les événements',
                    'Visite guidée du stade privée',
                    'Maillot dédicacé annuel',
                    'Support prioritaire 24/7',
                ],
                'badge_color' => '#FFD700',
                'badge_icon' => 'crown',
                'points_multiplier' => 5,
                'discount_percentage' => 20,
                'priority_support' => true,
                'is_active' => true,
                'display_order' => 4,
            ],
            [
                'name' => 'Platinum',
                'slug' => 'platinum',
                'description' => 'Le summum de l\'expérience CSS Socios',
                'monthly_price' => 99.90,
                'yearly_price' => 999.00,
                'features' => [
                    'Tout du plan Gold',
                    'Accès illimité à tout',
                    'Pass VIP famille (4 personnes)',
                    '30% de réduction boutique',
                    'Badge Platinum exclusif',
                    'Nom sur le mur des légendes',
                ],
                'benefits' => [
                    'Places VIP garanties à vie',
                    'Déjeuner annuel avec l\'équipe',
                    'Voyage avec l\'équipe (1/an)',
                    'Consultation décisions club',
                    'Concierge personnel 24/7',
                    'Kit complet saison offert',
                ],
                'badge_color' => '#E5E4E2',
                'badge_icon' => 'gem',
                'points_multiplier' => 10,
                'discount_percentage' => 30,
                'priority_support' => true,
                'is_active' => true,
                'display_order' => 5,
            ],
        ];

        foreach ($tiers as $tier) {
            SubscriptionTier::create($tier);
        }

        $this->command->info('5 tiers d\'abonnement créés avec succès');
    }
}
