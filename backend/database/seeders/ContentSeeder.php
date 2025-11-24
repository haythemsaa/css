<?php

namespace Database\Seeders;

use App\Models\Content;
use App\Models\ContentCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@css-sfax.tn')->first();
        if (!$admin) {
            $admin = User::first();
        }

        $categories = ContentCategory::all();
        if ($categories->isEmpty()) {
            return;
        }

        $contents = [
            [
                'title' => 'CSS remporte le derby contre l\'EST !',
                'slug' => 'css-remporte-derby-est',
                'excerpt' => 'Victoire historique du CSS face à l\'Espérance avec un score de 2-1',
                'content' => 'Le Club Sportif Sfaxien a remporté une victoire éclatante contre l\'Espérance Sportive de Tunis lors du derby de ce weekend. Deux buts magnifiques de nos attaquants ont permis de remporter ce match crucial.',
                'type' => 'article',
                'status' => 'published',
                'access_level' => 'free',
                'category_id' => $categories->random()->id,
                'author_id' => $admin->id,
                'published_at' => now()->subDays(1),
                'views_count' => rand(500, 2000),
                'likes_count' => rand(50, 200),
            ],
            [
                'title' => 'Interview exclusive : Le capitaine parle de la saison',
                'slug' => 'interview-capitaine-saison',
                'excerpt' => 'Notre capitaine revient sur les moments forts de cette saison',
                'content' => 'Dans une interview exclusive accordée à CSS Media, notre capitaine revient sur les hauts et les bas de cette saison, et partage sa vision pour l\'avenir du club.',
                'type' => 'article',
                'status' => 'published',
                'access_level' => 'premium',
                'category_id' => $categories->random()->id,
                'author_id' => $admin->id,
                'published_at' => now()->subDays(3),
                'views_count' => rand(300, 1000),
                'likes_count' => rand(30, 150),
            ],
            [
                'title' => 'Résumé vidéo : Les meilleurs moments du match',
                'slug' => 'resume-video-meilleurs-moments',
                'excerpt' => 'Revivez les temps forts du dernier match en vidéo',
                'content' => 'Revivez tous les moments forts de notre dernière victoire à travers cette vidéo exclusive.',
                'type' => 'video',
                'status' => 'published',
                'access_level' => 'premium',
                'category_id' => $categories->random()->id,
                'author_id' => $admin->id,
                'video_url' => 'https://www.youtube.com/watch?v=example',
                'duration' => 480,
                'published_at' => now()->subDays(2),
                'views_count' => rand(1000, 3000),
                'likes_count' => rand(100, 300),
            ],
            [
                'title' => 'Podcast : L\'histoire du CSS avec d\'anciens joueurs',
                'slug' => 'podcast-histoire-css',
                'excerpt' => 'Découvrez l\'histoire du club à travers les témoignages de légendes',
                'content' => 'Dans ce podcast spécial, d\'anciens joueurs du CSS partagent leurs souvenirs et anecdotes.',
                'type' => 'podcast',
                'status' => 'published',
                'access_level' => 'free',
                'category_id' => $categories->random()->id,
                'author_id' => $admin->id,
                'audio_url' => 'https://example.com/podcast.mp3',
                'duration' => 1800,
                'published_at' => now()->subDays(5),
                'views_count' => rand(200, 800),
                'likes_count' => rand(20, 100),
            ],
            [
                'title' => 'Préparation match : L\'équipe s\'entraîne intensément',
                'slug' => 'preparation-match-entrainement',
                'excerpt' => 'L\'équipe se prépare pour le prochain match décisif',
                'content' => 'Nos joueurs s\'entraînent avec intensité en préparation du prochain match crucial. Le coach a mis en place un programme d\'entraînement spécial.',
                'type' => 'article',
                'status' => 'published',
                'access_level' => 'free',
                'category_id' => $categories->random()->id,
                'author_id' => $admin->id,
                'published_at' => now(),
                'views_count' => rand(100, 500),
                'likes_count' => rand(10, 50),
            ],
        ];

        foreach ($contents as $content) {
            Content::create($content);
        }

        $this->command->info('Contenus créés avec succès');
    }
}
