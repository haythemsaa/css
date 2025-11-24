<?php

namespace Database\Seeders;

use App\Models\ContentCategory;
use Illuminate\Database\Seeder;

class ContentCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Actualités', 'slug' => 'actualites', 'icon' => '📰', 'color' => '#FF6B6B'],
            ['name' => 'Matchs', 'slug' => 'matchs', 'icon' => '⚽', 'color' => '#4ECDC4'],
            ['name' => 'Vidéos', 'slug' => 'videos', 'icon' => '🎬', 'color' => '#45B7D1'],
            ['name' => 'Interviews', 'slug' => 'interviews', 'icon' => '🎤', 'color' => '#F7B731'],
            ['name' => 'Coulisses', 'slug' => 'coulisses', 'icon' => '🎭', 'color' => '#5F27CD'],
            ['name' => 'Analyses', 'slug' => 'analyses', 'icon' => '📊', 'color' => '#00D2D3'],
            ['name' => 'Histoire', 'slug' => 'histoire', 'icon' => '📜', 'color' => '#8B4513'],
            ['name' => 'Académie', 'slug' => 'academie', 'icon' => '🎓', 'color' => '#2ECC71'],
            ['name' => 'Benchmarking', 'slug' => 'benchmarking', 'icon' => '📈', 'color' => '#3498DB'],
        ];

        foreach ($categories as $index => $category) {
            ContentCategory::create([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'icon' => $category['icon'],
                'color' => $category['color'],
                'order' => $index + 1,
                'is_active' => true,
            ]);
        }
    }
}
