<?php

namespace Database\Seeders;

use App\Models\AchievementBadge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            [
                'name' => 'Nouveau Supporter',
                'description' => 'Créer votre compte et rejoindre la communauté CSS',
                'icon' => '🆕',
                'category' => 'general',
                'rarity' => 'common',
                'criteria' => ['type' => 'days_active', 'target' => 1],
                'points_reward' => 10,
                'order' => 1,
            ],
            [
                'name' => 'Fan Fidèle',
                'description' => 'Être actif pendant 30 jours',
                'icon' => '⭐',
                'category' => 'general',
                'rarity' => 'uncommon',
                'criteria' => ['type' => 'days_active', 'target' => 30],
                'points_reward' => 50,
                'order' => 2,
            ],
            [
                'name' => 'Contributeur Généreux',
                'description' => 'Faire votre premier don',
                'icon' => '💝',
                'category' => 'donations',
                'rarity' => 'common',
                'criteria' => ['type' => 'donations_count', 'target' => 1],
                'points_reward' => 20,
                'order' => 3,
            ],
            [
                'name' => 'Donateur Platine',
                'description' => 'Donner plus de 500 TND au total',
                'icon' => '💎',
                'category' => 'donations',
                'rarity' => 'legendary',
                'criteria' => ['type' => 'donations_amount', 'target' => 500],
                'points_reward' => 200,
                'order' => 4,
            ],
            [
                'name' => 'Économe',
                'description' => 'Utiliser 10 codes de réduction Freeoui',
                'icon' => '💰',
                'category' => 'freeoui',
                'rarity' => 'uncommon',
                'criteria' => ['type' => 'reductions_used', 'target' => 10],
                'points_reward' => 30,
                'order' => 5,
            ],
            [
                'name' => 'Expert Freeoui',
                'description' => 'Utiliser 50 codes de réduction',
                'icon' => '🏆',
                'category' => 'freeoui',
                'rarity' => 'epic',
                'criteria' => ['type' => 'reductions_used', 'target' => 50],
                'points_reward' => 150,
                'order' => 6,
            ],
            [
                'name' => 'Membre Actif',
                'description' => 'Poster 10 messages sur le forum',
                'icon' => '💬',
                'category' => 'community',
                'rarity' => 'common',
                'criteria' => ['type' => 'forum_posts', 'target' => 10],
                'points_reward' => 25,
                'order' => 7,
            ],
            [
                'name' => 'Collectionneur',
                'description' => 'Obtenir 10 cartes à collectionner',
                'icon' => '🎴',
                'category' => 'cards',
                'rarity' => 'uncommon',
                'criteria' => ['type' => 'cards_collected', 'target' => 10],
                'points_reward' => 40,
                'order' => 8,
            ],
            [
                'name' => 'Maître Collectionneur',
                'description' => 'Obtenir 50 cartes à collectionner',
                'icon' => '👑',
                'category' => 'cards',
                'rarity' => 'legendary',
                'criteria' => ['type' => 'cards_collected', 'target' => 50],
                'points_reward' => 300,
                'order' => 9,
            ],
            [
                'name' => 'Parrain',
                'description' => 'Parrainer 5 nouveaux membres',
                'icon' => '🤝',
                'category' => 'referral',
                'rarity' => 'rare',
                'criteria' => ['type' => 'referrals', 'target' => 5],
                'points_reward' => 100,
                'order' => 10,
            ],
        ];

        foreach ($badges as $badge) {
            AchievementBadge::create($badge);
        }

        $this->command->info('Badges créés avec succès');
    }
}
