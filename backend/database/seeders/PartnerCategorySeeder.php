<?php

namespace Database\Seeders;

use App\Models\PartnerCategory;
use Illuminate\Database\Seeder;

class PartnerCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Restaurants & Alimentation',
                'slug' => 'restaurants-alimentation',
                'description' => 'Restaurants, cafés, fast-foods, supermarchés',
                'icon' => '🍽️',
                'color' => '#FF6B6B',
                'order' => 1,
            ],
            [
                'name' => 'Hôtels & Tourisme',
                'slug' => 'hotels-tourisme',
                'description' => 'Hôtels, agences de voyage, locations de voiture',
                'icon' => '🏨',
                'color' => '#4ECDC4',
                'order' => 2,
            ],
            [
                'name' => 'Sports & Bien-être',
                'slug' => 'sports-bien-etre',
                'description' => 'Salles de sport, spas, équipements sportifs',
                'icon' => '💪',
                'color' => '#45B7D1',
                'order' => 3,
            ],
            [
                'name' => 'Shopping',
                'slug' => 'shopping',
                'description' => 'Vêtements, électronique, accessoires',
                'icon' => '🛍️',
                'color' => '#F7B731',
                'order' => 4,
            ],
            [
                'name' => 'Services',
                'slug' => 'services',
                'description' => 'Banques, assurances, télécoms, services divers',
                'icon' => '💼',
                'color' => '#5F27CD',
                'order' => 5,
            ],
            [
                'name' => 'Divertissement',
                'slug' => 'divertissement',
                'description' => 'Cinémas, parcs d\'attractions, loisirs',
                'icon' => '🎬',
                'color' => '#EE5A6F',
                'order' => 6,
            ],
            [
                'name' => 'Éducation & Formation',
                'slug' => 'education-formation',
                'description' => 'Écoles, centres de formation, cours particuliers',
                'icon' => '📚',
                'color' => '#00D2D3',
                'order' => 7,
            ],
            [
                'name' => 'Santé & Médical',
                'slug' => 'sante-medical',
                'description' => 'Pharmacies, cliniques, laboratoires',
                'icon' => '🏥',
                'color' => '#FF9FF3',
                'order' => 8,
            ],
        ];

        foreach ($categories as $category) {
            PartnerCategory::create($category);
        }
    }
}
