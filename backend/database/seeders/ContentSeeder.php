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

        // Add Histoire content
        $histoireCategory = $categories->where('slug', 'histoire')->first();
        if ($histoireCategory) {
            $histoireContents = [
                [
                    'title' => '1928 : La naissance du CSS',
                    'slug' => '1928-naissance-css',
                    'excerpt' => 'L\'histoire de la fondation du Club Sportif Sfaxien',
                    'content' => 'Le 9 septembre 1928, le Club Sportif Sfaxien voit le jour grâce à la vision de pionniers passionnés. Cette date marque le début d\'une épopée légendaire dans le football tunisien.',
                    'type' => 'article',
                    'status' => 'published',
                    'access_level' => 'free',
                    'category_id' => $histoireCategory->id,
                    'author_id' => $admin->id,
                    'published_at' => now()->subDays(10),
                    'views_count' => rand(1000, 3000),
                    'likes_count' => rand(100, 400),
                ],
                [
                    'title' => 'Les années glorieuses : 1960-1970',
                    'slug' => 'annees-glorieuses-1960-1970',
                    'excerpt' => 'Une décennie de domination du CSS sur le football tunisien',
                    'content' => 'Les années 60 et 70 représentent l\'âge d\'or du CSS avec de nombreux titres nationaux et la consolidation de sa place parmi les grands du football tunisien.',
                    'type' => 'article',
                    'status' => 'published',
                    'access_level' => 'free',
                    'category_id' => $histoireCategory->id,
                    'author_id' => $admin->id,
                    'published_at' => now()->subDays(8),
                    'views_count' => rand(800, 2000),
                    'likes_count' => rand(80, 300),
                ],
                [
                    'title' => 'Légendes du CSS : Les joueurs emblématiques',
                    'slug' => 'legendes-css-joueurs-emblematiques',
                    'excerpt' => 'Portrait des plus grands joueurs de l\'histoire du club',
                    'content' => 'De Tarek Dhiab à Youssef Msakni, en passant par Ali Zitouni, découvrez les légendes qui ont marqué l\'histoire du CSS de leur empreinte indélébile.',
                    'type' => 'article',
                    'status' => 'published',
                    'access_level' => 'premium',
                    'category_id' => $histoireCategory->id,
                    'author_id' => $admin->id,
                    'published_at' => now()->subDays(6),
                    'views_count' => rand(1500, 4000),
                    'likes_count' => rand(150, 500),
                ],
            ];

            foreach ($histoireContents as $content) {
                Content::create($content);
            }
        }

        // Add Académie content
        $academieCategory = $categories->where('slug', 'academie')->first();
        if ($academieCategory) {
            $academieContents = [
                [
                    'title' => 'L\'Académie CSS : Former les stars de demain',
                    'slug' => 'academie-css-former-stars-demain',
                    'excerpt' => 'Découvrez le centre de formation d\'excellence du CSS',
                    'content' => 'L\'Académie du CSS forme les jeunes talents de 6 à 18 ans avec un programme d\'entraînement de classe mondiale et un suivi éducatif personnalisé.',
                    'type' => 'article',
                    'status' => 'published',
                    'access_level' => 'free',
                    'category_id' => $academieCategory->id,
                    'author_id' => $admin->id,
                    'published_at' => now()->subDays(4),
                    'views_count' => rand(500, 1500),
                    'likes_count' => rand(50, 200),
                ],
                [
                    'title' => 'Catégories U13 : Les champions de demain',
                    'slug' => 'categories-u13-champions-demain',
                    'excerpt' => 'Portrait de la catégorie U13 de l\'Académie CSS',
                    'content' => 'Nos jeunes talents U13 s\'entraînent 4 fois par semaine et participent aux compétitions régionales et nationales. Découvrez leur parcours.',
                    'type' => 'article',
                    'status' => 'published',
                    'access_level' => 'free',
                    'category_id' => $academieCategory->id,
                    'author_id' => $admin->id,
                    'published_at' => now()->subDays(3),
                    'views_count' => rand(400, 1000),
                    'likes_count' => rand(40, 150),
                ],
                [
                    'title' => 'Success Stories : De l\'Académie à l\'équipe première',
                    'slug' => 'success-stories-academie-equipe-premiere',
                    'excerpt' => 'Ces joueurs formés à l\'académie qui brillent aujourd\'hui',
                    'content' => 'Plusieurs joueurs de l\'équipe première sont passés par l\'Académie du CSS. Découvrez leurs parcours inspirants et comment ils sont devenus des stars.',
                    'type' => 'article',
                    'status' => 'published',
                    'access_level' => 'premium',
                    'category_id' => $academieCategory->id,
                    'author_id' => $admin->id,
                    'published_at' => now()->subDays(2),
                    'views_count' => rand(800, 2500),
                    'likes_count' => rand(80, 300),
                ],
            ];

            foreach ($academieContents as $content) {
                Content::create($content);
            }
        }

        // Add Benchmarking content
        $benchmarkingCategory = $categories->where('slug', 'benchmarking')->first();
        if ($benchmarkingCategory) {
            $benchmarkingContents = [
                [
                    'title' => 'Analyse tactique : Le système 4-3-3 du CSS',
                    'slug' => 'analyse-tactique-systeme-433-css',
                    'excerpt' => 'Décryptage du système de jeu employé par le CSS',
                    'content' => 'Le CSS utilise principalement un système 4-3-3 qui privilégie la possession et les transitions rapides. Analyse détaillée de cette approche tactique.',
                    'type' => 'article',
                    'status' => 'published',
                    'access_level' => 'premium',
                    'category_id' => $benchmarkingCategory->id,
                    'author_id' => $admin->id,
                    'published_at' => now()->subDays(7),
                    'views_count' => rand(600, 1800),
                    'likes_count' => rand(60, 250),
                ],
                [
                    'title' => 'Comparaison CSS vs clubs européens',
                    'slug' => 'comparaison-css-clubs-europeens',
                    'excerpt' => 'Comment se positionne le CSS face aux clubs européens',
                    'content' => 'Analyse comparative des performances, infrastructures et stratégies du CSS par rapport aux clubs de Ligue 1 et Liga.',
                    'type' => 'article',
                    'status' => 'published',
                    'access_level' => 'premium',
                    'category_id' => $benchmarkingCategory->id,
                    'author_id' => $admin->id,
                    'published_at' => now()->subDays(5),
                    'views_count' => rand(900, 2500),
                    'likes_count' => rand(90, 350),
                ],
            ];

            foreach ($benchmarkingContents as $content) {
                Content::create($content);
            }
        }

        $contents = array_merge($contents, []);

        foreach ($contents as $content) {
            Content::create($content);
        }

        $this->command->info('Contenus créés avec succès');
    }
}
