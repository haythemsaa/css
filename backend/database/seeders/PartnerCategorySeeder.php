<?php

namespace Database\Seeders;

use App\Models\PartnerCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PartnerCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name_fr' => 'Restauration',
                'name_ar' => 'مطاعم',
                'slug' => 'restauration',
                'description' => 'Restaurants, Fast-food, Cafés et établissements de restauration',
                'icon' => '🍽️',
                'color' => '#FF6B6B',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'name_fr' => 'Hôtellerie & Tourisme',
                'name_ar' => 'فنادق و سياحة',
                'slug' => 'hotellerie-tourisme',
                'description' => 'Hôtels, Maisons d\'hôtes, Agences de voyage',
                'icon' => '🏨',
                'color' => '#4ECDC4',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'name_fr' => 'Sport & Bien-être',
                'name_ar' => 'رياضة و صحة',
                'slug' => 'sport-bien-etre',
                'description' => 'Salles de sport, Spa, Équipements sportifs',
                'icon' => '💪',
                'color' => '#95E1D3',
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'name_fr' => 'Shopping',
                'name_ar' => 'تسوق',
                'slug' => 'shopping',
                'description' => 'Mode, Électronique, Supermarchés et commerce',
                'icon' => '🛍️',
                'color' => '#F38181',
                'display_order' => 4,
                'is_active' => true,
            ],
            [
                'name_fr' => 'Services',
                'name_ar' => 'خدمات',
                'slug' => 'services',
                'description' => 'Banques, Assurances, Télécom, Coiffeurs et services divers',
                'icon' => '🔧',
                'color' => '#AA96DA',
                'display_order' => 5,
                'is_active' => true,
            ],
            [
                'name_fr' => 'Loisirs',
                'name_ar' => 'ترفيه',
                'slug' => 'loisirs',
                'description' => 'Cinémas, Parcs, Événements et activités de loisirs',
                'icon' => '🎭',
                'color' => '#FCBAD3',
                'display_order' => 6,
                'is_active' => true,
            ],
            [
                'name_fr' => 'Éducation',
                'name_ar' => 'تعليم',
                'slug' => 'education',
                'description' => 'Cours, Formations, Langues et établissements éducatifs',
                'icon' => '📚',
                'color' => '#FFFFD2',
                'display_order' => 7,
                'is_active' => true,
            ],
            [
                'name_fr' => 'Santé',
                'name_ar' => 'صحة',
                'slug' => 'sante',
                'description' => 'Pharmacies, Cliniques, Laboratoires et services de santé',
                'icon' => '⚕️',
                'color' => '#A8D8EA',
                'display_order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            PartnerCategory::create($category);
        }
    }
}
