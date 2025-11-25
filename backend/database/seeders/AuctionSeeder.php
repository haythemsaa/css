<?php

namespace Database\Seeders;

use App\Models\AuctionProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AuctionSeeder extends Seeder
{
    public function run(): void
    {
        $auctions = [
            [
                'title' => 'Maillot Match Historique CSS vs EST - Finale CAF 2007',
                'slug' => 'maillot-finale-caf-2007',
                'description' => 'Maillot porté par Youssef Msakni lors de la finale historique de la Coupe CAF 2007. Dédicacé et avec certificat d\'authenticité.',
                'images' => ['/images/auctions/maillot-caf-2007-1.jpg', '/images/auctions/maillot-caf-2007-2.jpg'],
                'category' => 'signed_items',
                'starting_price' => 500.00,
                'reserve_price' => 800.00,
                'current_bid' => 750.00,
                'buy_now_price' => 1500.00,
                'bid_increment' => 50,
                'total_bids' => 12,
                'start_time' => now()->subDays(2),
                'end_time' => now()->addDays(5),
                'is_featured' => true,
                'auto_extend' => true,
                'auto_extend_minutes' => 5,
                'status' => 'active',
                'terms_conditions' => 'Article vendu dans l\'état. Certificat d\'authenticité inclus. Livraison assurée.',
                'metadata' => [
                    'authenticity' => 'Certificat CSS officiel',
                    'condition' => 'Excellent état',
                    'provenance' => 'Collection privée CSS',
                ],
            ],
            [
                'title' => 'Ballon Officiel Dédicacé par l\'Équipe 2023/2024',
                'slug' => 'ballon-dedie-equipe-2023-2024',
                'description' => 'Ballon officiel de la saison 2023/2024 signé par toute l\'équipe première du CSS',
                'images' => ['/images/auctions/ballon-2024-1.jpg'],
                'category' => 'collectibles',
                'starting_price' => 200.00,
                'reserve_price' => 300.00,
                'current_bid' => 380.00,
                'buy_now_price' => 600.00,
                'bid_increment' => 20,
                'total_bids' => 8,
                'start_time' => now()->subDays(1),
                'end_time' => now()->addDays(3),
                'is_featured' => true,
                'auto_extend' => true,
                'auto_extend_minutes' => 5,
                'status' => 'active',
                'terms_conditions' => 'Ballon neuf, dédicacé par 23 joueurs. Présenté sous vitrine.',
                'metadata' => [
                    'authenticity' => 'Vidéo de signature disponible',
                    'condition' => 'Neuf',
                    'signatures' => '23 joueurs',
                ],
            ],
            [
                'title' => 'Expérience VIP - Jour avec l\'Équipe CSS',
                'slug' => 'experience-vip-jour-equipe',
                'description' => 'Passez une journée inoubliable avec l\'équipe du CSS : entraînement, déjeuner avec les joueurs, photos, et match VIP',
                'images' => ['/images/auctions/experience-vip-1.jpg', '/images/auctions/experience-vip-2.jpg'],
                'category' => 'experiences',
                'starting_price' => 1000.00,
                'reserve_price' => 1500.00,
                'current_bid' => 0,
                'buy_now_price' => 3000.00,
                'bid_increment' => 100,
                'total_bids' => 0,
                'start_time' => now()->addDays(1),
                'end_time' => now()->addDays(10),
                'is_featured' => true,
                'auto_extend' => true,
                'auto_extend_minutes' => 10,
                'status' => 'scheduled',
                'terms_conditions' => 'Valable pour 2 personnes. Date à convenir avec le club. Non remboursable.',
                'metadata' => [
                    'duration' => 'Journée complète (8h-18h)',
                    'includes' => 'Transport, repas, photos, cadeaux',
                    'validity' => '6 mois',
                ],
            ],
            [
                'title' => 'Trophée Coupe de Tunisie 1995 - Réplique Officielle',
                'slug' => 'trophee-coupe-tunisie-1995',
                'description' => 'Réplique officielle en bronze du trophée de la Coupe de Tunisie 1995. Pièce unique numérotée.',
                'images' => ['/images/auctions/trophee-1995-1.jpg'],
                'category' => 'memorabilia',
                'starting_price' => 800.00,
                'reserve_price' => 1200.00,
                'current_bid' => 950.00,
                'buy_now_price' => 2000.00,
                'bid_increment' => 50,
                'total_bids' => 5,
                'start_time' => now()->subHours(12),
                'end_time' => now()->addDays(7),
                'is_featured' => false,
                'auto_extend' => true,
                'auto_extend_minutes' => 5,
                'status' => 'active',
                'terms_conditions' => 'Réplique officielle CSS. Hauteur 45cm. Socle en marbre.',
                'metadata' => [
                    'authenticity' => 'Gravure officielle CSS',
                    'condition' => 'Neuf',
                    'material' => 'Bronze et marbre',
                    'dimensions' => '45cm x 20cm',
                    'number' => '12/100',
                ],
            ],
            [
                'title' => 'Chaussures de Match - Kingsley Eduwo',
                'slug' => 'chaussures-kingsley-eduwo',
                'description' => 'Chaussures portées par Kingsley Eduwo lors du derby CSS vs ESS. Dédicacées et authentifiées.',
                'images' => ['/images/auctions/chaussures-eduwo-1.jpg', '/images/auctions/chaussures-eduwo-2.jpg'],
                'category' => 'signed_items',
                'starting_price' => 300.00,
                'reserve_price' => 450.00,
                'current_bid' => 0,
                'buy_now_price' => 800.00,
                'bid_increment' => 30,
                'total_bids' => 0,
                'start_time' => now()->addHours(6),
                'end_time' => now()->addDays(4),
                'is_featured' => false,
                'auto_extend' => true,
                'auto_extend_minutes' => 5,
                'status' => 'scheduled',
                'terms_conditions' => 'Chaussures dans leur état d\'origine. Certificat du joueur inclus.',
                'metadata' => [
                    'authenticity' => 'Photo dédicace + certificat',
                    'condition' => 'Utilisé (match)',
                    'size' => '42',
                    'brand' => 'Nike Mercurial',
                ],
            ],
            [
                'title' => 'Écharpe Vintage CSS - Années 1980',
                'slug' => 'echarpe-vintage-1980',
                'description' => 'Écharpe originale des années 1980, rarissime. Pièce de collection pour nostalgiques.',
                'images' => ['/images/auctions/echarpe-1980-1.jpg'],
                'category' => 'memorabilia',
                'starting_price' => 150.00,
                'reserve_price' => null, // Pas de prix de réserve
                'current_bid' => 220.00,
                'buy_now_price' => null, // Pas d'achat immédiat
                'bid_increment' => 10,
                'total_bids' => 14,
                'start_time' => now()->subDays(3),
                'end_time' => now()->addHours(18),
                'is_featured' => false,
                'auto_extend' => true,
                'auto_extend_minutes' => 3,
                'status' => 'active',
                'terms_conditions' => 'Article vintage vendu dans l\'état. Pas de retour possible.',
                'metadata' => [
                    'authenticity' => 'Pièce originale',
                    'condition' => 'Bon état (quelques traces d\'usure)',
                    'era' => '1980-1985',
                    'rarity' => 'Très rare',
                ],
            ],
        ];

        foreach ($auctions as $auctionData) {
            AuctionProduct::create($auctionData);
        }

        $this->command->info('6 enchères créées (actives et programmées)');
    }
}
