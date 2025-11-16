<?php

namespace Database\Seeders;

use App\Models\Content;
use App\Models\Video;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // Créer un utilisateur admin comme auteur si aucun n'existe
        $author = User::first() ?? User::factory()->create([
            'name' => 'Rédaction CSS',
            'email' => 'redaction@css.tn',
            'user_type' => 'socios',
        ]);

        $articleTitles = [
            'Victoire éclatante du CSS face à l\'EST au Stade Taieb Mhiri',
            'Pedro Sá nommé meilleur buteur du mois',
            'Analyse tactique : Le 4-3-3 du CSS sous la loupe',
            'Interview exclusive : Aymen Dahmen parle de ses ambitions',
            'Le CSS prépare le derby face à l\'ESS',
            'Retour sur la victoire historique en Ligue des Champions',
            'Formation : Les jeunes talents du CSS à surveiller',
            'Transferts : Le point sur le mercato hivernal',
            'Firas Chaouat prolonge son contrat jusqu\'en 2026',
            'Le Stade Taieb Mhiri bientôt rénové',
            'CSS Socios : Les nouveaux avantages dévoilés',
            'Préparation physique : Dans les coulisses de l\'entraînement',
            'Match nul frustrant face au Club Africain',
            'Les supporters du CSS mobilisés pour le prochain match',
            'Histoire du CSS : Les légendes du club',
            'Conférence de presse : Le coach fait le point',
            'CSS Academy : Former les champions de demain',
            'Statistiques : Les chiffres clés de la saison',
            'Kingsley Eduwo : Le roc de la défense sfaxienne',
            'Programme de fidélité : Comment gagner des points',
        ];

        $videoTitles = [
            'Résumé CSS vs EST - Tous les buts',
            'Entraînement d\'avant-match au Stade Taieb Mhiri',
            'Interview : Firas Chaouat après la victoire',
            'Les plus beaux buts de la saison',
            'Dans les vestiaires après la victoire',
            'Conférence de presse intégrale',
            'Présentation des nouveaux maillots',
            'Best of : Les arrêts d\'Aymen Dahmen',
            'Ambiance tribune : Les supporters en feu',
            'CSS Inside : Une journée avec l\'équipe',
        ];

        $galleryTitles = [
            'Photos : CSS 3-1 EST - Galerie complète',
            'Entraînement du mercredi : Les images',
            'Célébration de la victoire avec les supporters',
            'Coulisses : Préparation du match',
            'Les supporters au Stade Taieb Mhiri',
        ];

        $podcastTitles = [
            'CSS Inside #12 : Décryptage tactique avec un expert',
            'CSS Podcast : Interview de Mohamed Ali Ben Romdhane',
            'Histoire du CSS : Episode 5 - Les années glorieuses',
            'Débat : Le CSS peut-il remporter le championnat ?',
            'CSS Radio : Les réactions après le derby',
        ];

        // Créer les articles (20)
        foreach ($articleTitles as $index => $title) {
            $isPremium = $index % 2 === 0;
            $isFeatured = $index < 4;

            Content::create([
                'author_id' => $author->id,
                'title' => $title,
                'slug' => Str::slug($title) . '-' . rand(1000, 9999),
                'excerpt' => substr($title, 0, 100) . '...',
                'type' => 'article',
                'is_premium' => $isPremium,
                'is_featured' => $isFeatured,
                'published_at' => now()->subDays(rand(1, 90)),
                'views_count' => rand(500, 15000),
                'likes_count' => rand(50, 1200),
            ]);
        }

        // Créer les vidéos (10)
        foreach ($videoTitles as $index => $title) {
            $isPremium = $index % 3 === 0;
            $isFeatured = $index < 3;

            $content = Content::create([
                'author_id' => $author->id,
                'title' => $title,
                'slug' => Str::slug($title) . '-' . rand(1000, 9999),
                'excerpt' => substr($title, 0, 100) . '...',
                'type' => 'video',
                'is_premium' => $isPremium,
                'is_featured' => $isFeatured,
                'published_at' => now()->subDays(rand(1, 60)),
                'views_count' => rand(1000, 25000),
                'likes_count' => rand(100, 2500),
            ]);

            // Créer l'entrée Video associée
            Video::create([
                'content_id' => $content->id,
                'title' => $content->title,
                'thumbnail_url' => 'https://img.youtube.com/vi/' . \Illuminate\Support\Str::random(11) . '/maxresdefault.jpg',
                'video_url' => 'https://youtube.com/watch?v=' . Str::random(11),
                'duration' => rand(120, 900), // 2-15 minutes
                'quality' => 'fullhd',
            ]);
        }

        // Créer les galeries (5)
        foreach ($galleryTitles as $index => $title) {
            $isFeatured = $index === 0;

            Content::create([
                'author_id' => $author->id,
                'title' => $title,
                'slug' => Str::slug($title) . '-' . rand(1000, 9999),
                'excerpt' => substr($title, 0, 100) . '...',
                'type' => 'gallery',
                'is_premium' => false,
                'is_featured' => $isFeatured,
                'published_at' => now()->subDays(rand(1, 45)),
                'views_count' => rand(800, 12000),
                'likes_count' => rand(80, 1000),
            ]);
        }

        // Créer les podcasts (5)
        foreach ($podcastTitles as $index => $title) {
            $isPremium = $index % 2 === 1;

            Content::create([
                'author_id' => $author->id,
                'title' => $title,
                'slug' => Str::slug($title) . '-' . rand(1000, 9999),
                'excerpt' => substr($title, 0, 100) . '...',
                'type' => 'podcast',
                'is_premium' => $isPremium,
                'is_featured' => false,
                'published_at' => now()->subDays(rand(1, 30)),
                'views_count' => rand(300, 5000),
                'likes_count' => rand(30, 500),
            ]);
        }
    }
}
