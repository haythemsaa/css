<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Jerseys
            [
                'name' => 'Maillot Domicile CSS 2024/25',
                'slug' => 'maillot-domicile-css-2024-25',
                'description' => 'Maillot officiel domicile du Club Sportif Sfaxien saison 2024/2025. Tissu respirant haute performance.',
                'category' => 'jerseys',
                'sku' => 'CSS-HOME-2425',
                'price' => 89.90,
                'sale_price' => null,
                'stock_quantity' => 150,
                'is_available' => true,
                'is_featured' => true,
                'images' => ['/images/products/jersey-home.jpg'],
                'specifications' => ['sizes' => ['S', 'M', 'L', 'XL', 'XXL'], 'material' => '100% Polyester', 'care' => 'Lavage à 30°C'],
            ],
            [
                'name' => 'Maillot Extérieur CSS 2024/25',
                'slug' => 'maillot-exterieur-css-2024-25',
                'description' => 'Maillot officiel extérieur du Club Sportif Sfaxien saison 2024/2025.',
                'category' => 'jerseys',
                'sku' => 'CSS-AWAY-2425',
                'price' => 89.90,
                'sale_price' => 74.90,
                'stock_quantity' => 100,
                'is_available' => true,
                'is_featured' => false,
                'images' => ['/images/products/jersey-away.jpg'],
                'specifications' => ['sizes' => ['S', 'M', 'L', 'XL', 'XXL'], 'material' => '100% Polyester'],
            ],
            [
                'name' => 'Maillot Gardien CSS 2024/25',
                'slug' => 'maillot-gardien-css-2024-25',
                'description' => 'Maillot officiel gardien du CSS avec renforts aux coudes.',
                'category' => 'jerseys',
                'sku' => 'CSS-GK-2425',
                'price' => 79.90,
                'sale_price' => null,
                'stock_quantity' => 50,
                'is_available' => true,
                'is_featured' => false,
                'images' => ['/images/products/jersey-gk.jpg'],
                'specifications' => ['sizes' => ['M', 'L', 'XL', 'XXL'], 'material' => '100% Polyester'],
            ],

            // Merchandise
            [
                'name' => 'Écharpe Officielle CSS',
                'slug' => 'echarpe-officielle-css',
                'description' => 'Écharpe jacquard aux couleurs du CSS. Parfait pour les jours de match.',
                'category' => 'merchandise',
                'sku' => 'CSS-SCARF-01',
                'price' => 19.90,
                'sale_price' => null,
                'stock_quantity' => 200,
                'is_available' => true,
                'is_featured' => true,
                'images' => ['/images/products/scarf.jpg'],
                'specifications' => ['length' => '140cm', 'material' => ''Acrylique'],
            ],
            [
                'name' => 'Casquette CSS Logo Brodé',
                'slug' => 'casquette-css-logo-brode',
                'description' => 'Casquette ajustable avec logo CSS brodé. Protection soleil garantie.',
                'category' => 'merchandise',
                'sku' => 'CSS-CAP-01',
                'price' => 24.90,
                'sale_price' => null,
                'stock_quantity' => 120,
                'is_available' => true,
                'is_featured' => false,
                'images' => ['/images/products/cap.jpg'],
                'specifications' => ['size' => 'Réglable', 'material' => 'Coton'],
            ],
            [
                'name' => 'Drapeau CSS Grand Format',
                'slug' => 'drapeau-css-grand-format',
                'description' => 'Drapeau CSS 150x90cm. Idéal pour votre salon ou le stade.',
                'category' => 'merchandise',
                'sku' => 'CSS-FLAG-01',
                'price' => 29.90,
                'sale_price' => 24.90,
                'stock_quantity' => 80,
                'is_available' => true,
                'is_featured' => false,
                'images' => ['/images/products/flag.jpg'],
                'specifications' => ['size' => '150x90cm', 'material' => 'Polyester'],
            ],

            // Accessories
            [
                'name' => 'Sac de Sport CSS',
                'slug' => 'sac-de-sport-css',
                'description' => 'Sac de sport grande capacité avec logo CSS. Compartiment chaussures séparé.',
                'category' => 'accessories',
                'sku' => 'CSS-BAG-01',
                'price' => 49.90,
                'sale_price' => null,
                'stock_quantity' => 60,
                'is_available' => true,
                'is_featured' => true,
                'images' => ['/images/products/sports-bag.jpg'],
                'specifications' => ['capacity' => '45L', 'material' => 'Polyester imperméable'],
            ],
            [
                'name' => 'Porte-clés CSS Métal',
                'slug' => 'porte-cles-css-metal',
                'description' => 'Porte-clés métallique avec logo CSS gravé.',
                'category' => 'accessories',
                'sku' => 'CSS-KEY-01',
                'price' => 9.90,
                'sale_price' => null,
                'stock_quantity' => 300,
                'is_available' => true,
                'is_featured' => false,
                'images' => ['/images/products/keychain.jpg'],
                'specifications' => ['material' => 'Métal chromé'],
            ],
            [
                'name' => 'Mug CSS Céramique',
                'slug' => 'mug-css-ceramique',
                'description' => 'Mug en céramique avec logo CSS. Capacité 350ml.',
                'category' => 'accessories',
                'sku' => 'CSS-MUG-01',
                'price' => 14.90,
                'sale_price' => null,
                'stock_quantity' => 100,
                'is_available' => true,
                'is_featured' => false,
                'images' => ['/images/products/mug.jpg'],
                'specifications' => ['capacity' => '350ml', 'material' => 'Céramique', 'dishwasher_safe' => true],
            ],

            // Collectibles
            [
                'name' => 'Mini Maillot CSS Collection',
                'slug' => 'mini-maillot-css-collection',
                'description' => 'Mini maillot de collection CSS. Parfait pour votre bureau ou votre voiture.',
                'category' => 'collectibles',
                'sku' => 'CSS-MINI-01',
                'price' => 12.90,
                'sale_price' => null,
                'stock_quantity' => 150,
                'is_available' => true,
                'is_featured' => false,
                'images' => ['/images/products/mini-jersey.jpg'],
                'specifications' => ['size' => '20cm', 'with_hanger' => true],
            ],
            [
                'name' => 'Ballon CSS Signature',
                'slug' => 'ballon-css-signature',
                'description' => 'Ballon officiel CSS taille 5. Parfait pour l\'entraînement.',
                'category' => 'collectibles',
                'sku' => 'CSS-BALL-01',
                'price' => 34.90,
                'sale_price' => null,
                'stock_quantity' => 75,
                'is_available' => true,
                'is_featured' => true,
                'images' => ['/images/products/ball.jpg'],
                'specifications' => ['size' => '5', 'material' => 'Synthétique', 'for_training' => true],
            ],
            [
                'name' => 'Poster CSS Équipe 2024',
                'slug' => 'poster-css-equipe-2024',
                'description' => 'Poster officiel de l\'équipe CSS saison 2024. Format A2.',
                'category' => 'collectibles',
                'sku' => 'CSS-POSTER-01',
                'price' => 7.90,
                'sale_price' => 5.90,
                'stock_quantity' => 200,
                'is_available' => true,
                'is_featured' => false,
                'images' => ['/images/products/poster.jpg'],
                'specifications' => ['size' => 'A2 (42x59cm)', 'paper' => 'Papier glacé 250g'],
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('Produits créés avec succès');
    }
}
