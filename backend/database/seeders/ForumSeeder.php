<?php

namespace Database\Seeders;

use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\ForumReply;
use App\Models\User;
use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    public function run(): void
    {
        // Create categories
        $categories = [
            [
                'name' => 'Discussions Générales',
                'slug' => 'discussions-generales',
                'description' => 'Parlez de tout ce qui concerne le CSS',
                'icon' => '💬',
                'color' => '#3B82F6',
                'order' => 1,
                'min_user_type' => 'free',
            ],
            [
                'name' => 'Matchs & Compétitions',
                'slug' => 'matchs-competitions',
                'description' => 'Analyses et discussions sur les matchs',
                'icon' => '⚽',
                'color' => '#10B981',
                'order' => 2,
                'min_user_type' => 'free',
            ],
            [
                'name' => 'Joueurs & Équipe',
                'slug' => 'joueurs-equipe',
                'description' => 'Tout sur nos joueurs et le staff',
                'icon' => '👥',
                'color' => '#F59E0B',
                'order' => 3,
                'min_user_type' => 'free',
            ],
            [
                'name' => 'Espace Socios',
                'slug' => 'espace-socios',
                'description' => 'Forum réservé aux membres Socios',
                'icon' => '⭐',
                'color' => '#8B5CF6',
                'order' => 4,
                'min_user_type' => 'socios',
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = ForumCategory::create($categoryData);

            // Create 2-3 topics per category
            $users = User::limit(5)->get();
            if ($users->isEmpty()) {
                continue;
            }

            for ($i = 1; $i <= 3; $i++) {
                $topic = ForumTopic::create([
                    'category_id' => $category->id,
                    'user_id' => $users->random()->id,
                    'title' => $this->getTopicTitle($category->slug, $i),
                    'content' => $this->getTopicContent($category->slug),
                    'is_pinned' => $i === 1,
                    'views_count' => rand(50, 500),
                    'likes_count' => rand(5, 50),
                ]);

                // Create 1-3 replies per topic
                for ($j = 1; $j <= rand(1, 3); $j++) {
                    ForumReply::create([
                        'topic_id' => $topic->id,
                        'user_id' => $users->random()->id,
                        'content' => $this->getReplyContent(),
                        'likes_count' => rand(0, 20),
                    ]);
                }
            }
        }

        $this->command->info('Forum créé avec succès');
    }

    private function getTopicTitle($categorySlug, $index): string
    {
        $titles = [
            'discussions-generales' => [
                'Bienvenue sur le forum officiel du CSS !',
                'Quel est votre souvenir CSS préféré ?',
                'Suggestions pour améliorer l\'application',
            ],
            'matchs-competitions' => [
                'Match CSS vs EST - Analyse et prédictions',
                'Notre parcours en Champions League cette saison',
                'Calendrier des prochains matchs',
            ],
            'joueurs-equipe' => [
                'Performance de nos attaquants cette saison',
                'Transferts : Qui devrait-on recruter ?',
                'Nos jeunes talents à suivre',
            ],
            'espace-socios' => [
                'Événement exclusif Socios - Rencontre avec les joueurs',
                'Avantages Socios : Vos retours',
                'Prochaine assemblée générale',
            ],
        ];

        return $titles[$categorySlug][$index - 1] ?? 'Topic de discussion';
    }

    private function getTopicContent($categorySlug): string
    {
        $contents = [
            'discussions-generales' => 'Bienvenue à tous les supporters du CSS ! N\'hésitez pas à partager vos pensées et à échanger avec la communauté.',
            'matchs-competitions' => 'Qu\'avez-vous pensé du dernier match ? Partagez votre analyse et vos impressions ici.',
            'joueurs-equipe' => 'Discussion sur les performances de nos joueurs et l\'évolution de l\'équipe.',
            'espace-socios' => 'Espace réservé aux membres Socios pour discuter des événements exclusifs et des avantages.',
        ];

        return $contents[$categorySlug] ?? 'Contenu du topic...';
    }

    private function getReplyContent(): string
    {
        $replies = [
            'Très bonne analyse, je suis totalement d\'accord !',
            'Intéressant, mais je pense qu\'il y a d\'autres aspects à considérer.',
            'Merci pour ce partage ! Vivement le prochain match.',
            'CSS forever ! 💚',
            'J\'espère qu\'on va continuer sur cette lancée.',
        ];

        return $replies[array_rand($replies)];
    }
}
