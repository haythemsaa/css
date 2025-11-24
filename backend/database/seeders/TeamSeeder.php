<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        // CSS Team
        Team::create([
            'name' => 'Club Sportif Sfaxien',
            'slug' => 'css-sfax',
            'country' => 'Tunisia',
            'city' => 'Sfax',
            'stadium' => 'Stade Taïeb Mhiri',
            'founded_year' => 1928,
            'description' => 'Le Club Sportif Sfaxien, fondé en 1928, est l\'un des clubs les plus titrés de Tunisie.',
        ]);

        // Other Tunisian teams
        $teams = [
            ['name' => 'Espérance Sportive de Tunis', 'slug' => 'est', 'city' => 'Tunis'],
            ['name' => 'Club Africain', 'slug' => 'ca', 'city' => 'Tunis'],
            ['name' => 'Étoile Sportive du Sahel', 'slug' => 'ess', 'city' => 'Sousse'],
            ['name' => 'CS Hammam-Lif', 'slug' => 'cshl', 'city' => 'Hammam-Lif'],
            ['name' => 'US Monastir', 'slug' => 'usm', 'city' => 'Monastir'],
            ['name' => 'Stade Tunisien', 'slug' => 'st', 'city' => 'Tunis'],
        ];

        foreach ($teams as $team) {
            Team::create([
                'name' => $team['name'],
                'slug' => $team['slug'],
                'country' => 'Tunisia',
                'city' => $team['city'],
                'founded_year' => fake()->numberBetween(1920, 1970),
            ]);
        }
    }
}
