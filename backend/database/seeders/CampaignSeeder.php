<?php

namespace Database\Seeders;

use App\Models\Campaign;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        Campaign::create([
            'title' => 'Soutien Formation Jeunes',
            'slug' => 'soutien-formation-jeunes',
            'description' => 'Aidez-nous à former la prochaine génération de champions CSS',
            'type' => 'youth_training',
            'goal_amount' => 50000,
            'current_amount' => 12500,
            'min_donation' => 5,
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->addDays(20),
            'status' => 'active',
            'is_featured' => true,
            'donors_count' => 45,
        ]);

        Campaign::create([
            'title' => 'Rénovation Stade',
            'slug' => 'renovation-stade',
            'description' => 'Participez à la modernisation de notre stade historique',
            'type' => 'stadium',
            'goal_amount' => 100000,
            'current_amount' => 35000,
            'min_donation' => 10,
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->addMonths(2),
            'status' => 'active',
            'is_featured' => true,
            'donors_count' => 128,
        ]);
    }
}
