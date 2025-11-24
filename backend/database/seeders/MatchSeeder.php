<?php

namespace Database\Seeders;

use App\Models\Match;
use App\Models\Team;
use Illuminate\Database\Seeder;

class MatchSeeder extends Seeder
{
    public function run(): void
    {
        $css = Team::where('name', 'Club Sportif Sfaxien')->first();

        if (!$css) {
            return;
        }

        // Create opponent teams
        $opponents = [
            ['name' => 'Espérance Sportive de Tunis', 'short_name' => 'EST', 'city' => 'Tunis'],
            ['name' => 'Étoile Sportive du Sahel', 'short_name' => 'ESS', 'city' => 'Sousse'],
            ['name' => 'Club Africain', 'short_name' => 'CA', 'city' => 'Tunis'],
            ['name' => 'US Monastir', 'short_name' => 'USM', 'city' => 'Monastir'],
            ['name' => 'CS Hammam-Lif', 'short_name' => 'CSHL', 'city' => 'Hammam-Lif'],
        ];

        $teams = collect();
        foreach ($opponents as $opponent) {
            $teams->push(Team::firstOrCreate(
                ['name' => $opponent['name']],
                $opponent + ['founded_year' => 1920]
            ));
        }

        $competitions = ['Ligue 1', 'Coupe de Tunisie', 'CAF Champions League'];
        $statuses = ['scheduled', 'live', 'finished'];

        // Past matches
        for ($i = 5; $i >= 1; $i--) {
            Match::create([
                'home_team_id' => $css->id,
                'away_team_id' => $teams->random()->id,
                'competition' => $competitions[array_rand($competitions)],
                'venue' => 'Stade Taïeb Mhiri',
                'match_date' => now()->subDays($i * 7),
                'kickoff_time' => '19:00',
                'home_score' => rand(0, 3),
                'away_score' => rand(0, 2),
                'status' => 'finished',
            ]);
        }

        // Upcoming matches
        for ($i = 1; $i <= 5; $i++) {
            Match::create([
                'home_team_id' => $i % 2 === 0 ? $css->id : $teams->random()->id,
                'away_team_id' => $i % 2 === 0 ? $teams->random()->id : $css->id,
                'competition' => $competitions[array_rand($competitions)],
                'venue' => $i % 2 === 0 ? 'Stade Taïeb Mhiri' : 'Stade Olympique',
                'match_date' => now()->addDays($i * 7),
                'kickoff_time' => ['15:00', '19:00', '20:00'][array_rand(['15:00', '19:00', '20:00'])],
                'status' => 'scheduled',
            ]);
        }

        $this->command->info('Matches créés avec succès');
    }
}
