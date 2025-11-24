<?php

namespace Database\Seeders;

use App\Models\GiftCampaign;
use Illuminate\Database\Seeder;

class GiftCampaignSeeder extends Seeder
{
    public function run(): void
    {
        $campaigns = [
            [
                'name' => 'Cadeau Quotidien Free',
                'description' => 'Un cadeau surprise chaque jour pour tous les membres',
                'start_date' => now(),
                'end_date' => now()->addMonths(3),
                'total_gifts' => 1000,
                'gifts_distributed' => 0,
                'gift_type' => 'points',
                'gift_value' => '10',
                'frequency' => 'daily',
                'eligibility_criteria' => ['user_types' => ['free', 'premium', 'socios']],
                'is_active' => true,
            ],
            [
                'name' => 'Bonus Premium',
                'description' => 'Bonus exclusif pour les membres Premium',
                'start_date' => now(),
                'end_date' => now()->addMonths(2),
                'total_gifts' => 500,
                'gifts_distributed' => 0,
                'gift_type' => 'discount_code',
                'gift_value' => '20',
                'frequency' => 'weekly',
                'eligibility_criteria' => ['user_types' => ['premium', 'socios'], 'min_loyalty_points' => 100],
                'is_active' => true,
            ],
            [
                'name' => 'Récompense Socios',
                'description' => 'Cadeau spécial pour les Socios fidèles',
                'start_date' => now(),
                'end_date' => now()->addMonths(1),
                'total_gifts' => 100,
                'gifts_distributed' => 0,
                'gift_type' => 'merchandise',
                'gift_value' => 'CSS Keychain',
                'frequency' => 'monthly',
                'eligibility_criteria' => ['user_types' => ['socios'], 'min_loyalty_points' => 500],
                'is_active' => true,
            ],
        ];

        foreach ($campaigns as $campaign) {
            GiftCampaign::create($campaign);
        }

        $this->command->info('Campagnes de cadeaux créées avec succès');
    }
}
