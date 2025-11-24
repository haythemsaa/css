<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AchievementBadge;
use App\Models\UserBadge;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    /**
     * Get all available badges
     */
    public function all(Request $request)
    {
        $query = AchievementBadge::active();

        // Filter by category
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        // Filter by rarity
        if ($request->has('rarity')) {
            $query->byRarity($request->rarity);
        }

        $badges = $query->get();

        // If user is authenticated, add progress for each badge
        $user = $request->user();
        if ($user) {
            $badges = $badges->map(function ($badge) use ($user) {
                $badge->user_progress = $badge->calculateProgress($user);
                $badge->is_earned = $badge->hasBeenEarnedBy($user);
                return $badge;
            });
        }

        return response()->json($badges);
    }

    /**
     * Get user's earned badges
     */
    public function myBadges(Request $request)
    {
        $user = $request->user();

        $userBadges = UserBadge::with('badge')
            ->forUser($user->id)
            ->latest('earned_at')
            ->get();

        $stats = [
            'total_badges' => $userBadges->count(),
            'total_points' => $userBadges->sum(function ($userBadge) {
                return $userBadge->badge->points_reward;
            }),
            'displayed_badges' => $userBadges->where('is_displayed', true)->count(),
        ];

        return response()->json([
            'badges' => $userBadges,
            'stats' => $stats,
        ]);
    }

    /**
     * Get progress for a specific badge
     */
    public function progress(Request $request, $id)
    {
        $user = $request->user();
        $badge = AchievementBadge::findOrFail($id);

        $progress = $badge->calculateProgress($user);
        $isEarned = $badge->hasBeenEarnedBy($user);

        return response()->json([
            'badge' => $badge,
            'progress' => $progress,
            'is_earned' => $isEarned,
            'criteria' => $badge->criteria,
        ]);
    }
}
