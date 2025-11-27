<?php

namespace Database\Seeders;

use App\Models\Leaderboard;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeaderboardSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Run UserSeeder first.');
            return;
        }

        $types = ['points', 'donations', 'engagement', 'social'];
        $periods = ['all_time', 'monthly', 'weekly'];

        foreach ($types as $type) {
            foreach ($periods as $period) {
                $periodStart = match($period) {
                    'weekly' => now()->startOfWeek(),
                    'monthly' => now()->startOfMonth(),
                    default => null,
                };

                $periodEnd = match($period) {
                    'weekly' => now()->endOfWeek(),
                    'monthly' => now()->endOfMonth(),
                    default => null,
                };

                // Generate scores for all users
                $usersWithScores = $users->map(function ($user) use ($type) {
                    return [
                        'user' => $user,
                        'score' => $this->generateScore($type),
                    ];
                })->sortByDesc('score')->values();

                // Create leaderboard entries with ranks
                foreach ($usersWithScores as $index => $data) {
                    $rank = $index + 1;
                    $previousRank = $rank + rand(-5, 5);
                    if ($previousRank < 1) {
                        $previousRank = $rank;
                    }

                    Leaderboard::create([
                        'user_id' => $data['user']->id,
                        'leaderboard_type' => $type,
                        'score' => $data['score'],
                        'rank' => $rank,
                        'previous_rank' => $previousRank,
                        'period' => $period,
                        'period_start' => $periodStart,
                        'period_end' => $periodEnd,
                        'metadata' => $this->generateMetadata($type, $data['score']),
                        'last_updated_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info('Classements créés avec succès');
    }

    private function generateScore(string $type): int
    {
        return match($type) {
            'points' => rand(100, 10000),
            'donations' => rand(50, 5000),
            'engagement' => rand(200, 8000),
            'social' => rand(150, 6000),
            default => rand(100, 5000),
        };
    }

    private function generateMetadata(string $type, int $score): array
    {
        return match($type) {
            'points' => [
                'total_activities' => rand(10, 100),
                'badges_earned' => rand(1, 20),
                'streak_days' => rand(1, 30),
            ],
            'donations' => [
                'total_donations' => rand(1, 50),
                'campaigns_supported' => rand(1, 10),
                'average_donation' => round($score / max(rand(1, 50), 1), 2),
            ],
            'engagement' => [
                'posts_created' => rand(5, 100),
                'comments_made' => rand(10, 200),
                'likes_received' => rand(20, 500),
            ],
            'social' => [
                'friends_count' => rand(5, 200),
                'shares_count' => rand(10, 100),
                'referrals_count' => rand(0, 50),
            ],
            default => [],
        };
    }
}
