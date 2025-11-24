<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Users & Auth
            UserSeeder::class,

            // Sports
            TeamSeeder::class,
            PlayerSeeder::class,
            MatchSeeder::class,

            // Content
            ContentCategorySeeder::class,

            // Partners
            PartnerCategorySeeder::class,
            PartnerSeeder::class,

            // Donations
            CampaignSeeder::class,

            // Gamification
            BadgeSeeder::class,
            LotteryDrawSeeder::class,
            GiftCampaignSeeder::class,

            // Community
            ForumSeeder::class,
            PollSeeder::class,
        ]);
    }
}
