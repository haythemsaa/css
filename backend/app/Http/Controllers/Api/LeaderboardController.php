<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeaderboardResource;
use App\Models\Leaderboard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LeaderboardController extends Controller
{
    /**
     * Display leaderboard rankings
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'type' => 'nullable|string|in:points,donations,engagement,social',
            'period' => 'nullable|string|in:daily,weekly,monthly,all_time',
            'limit' => 'nullable|integer|min:1|max:500',
        ]);

        $type = $request->input('type', 'points');
        $period = $request->input('period', 'all_time');
        $limit = $request->input('limit', 100);

        $leaderboards = Leaderboard::query()
            ->with('user')
            ->ofType($type)
            ->ofPeriod($period)
            ->topRanked($limit)
            ->get();

        return LeaderboardResource::collection($leaderboards);
    }

    /**
     * Display user's ranking
     */
    public function userRank(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Authentification requise',
            ], 401);
        }

        $request->validate([
            'type' => 'nullable|string|in:points,donations,engagement,social',
            'period' => 'nullable|string|in:daily,weekly,monthly,all_time',
        ]);

        $type = $request->input('type', 'points');
        $period = $request->input('period', 'all_time');

        $ranking = Leaderboard::query()
            ->where('user_id', $user->id)
            ->ofType($type)
            ->ofPeriod($period)
            ->first();

        if (!$ranking) {
            return response()->json([
                'message' => 'Aucun classement trouvé',
                'user_id' => $user->id,
                'type' => $type,
                'period' => $period,
            ], 404);
        }

        return response()->json([
            'ranking' => new LeaderboardResource($ranking),
            'total_users' => Leaderboard::ofType($type)->ofPeriod($period)->count(),
        ]);
    }

    /**
     * Get leaderboard statistics
     */
    public function stats(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'nullable|string|in:points,donations,engagement,social',
            'period' => 'nullable|string|in:daily,weekly,monthly,all_time',
        ]);

        $type = $request->input('type', 'points');
        $period = $request->input('period', 'all_time');

        $leaderboards = Leaderboard::ofType($type)->ofPeriod($period)->get();

        if ($leaderboards->isEmpty()) {
            return response()->json([
                'message' => 'Aucune donnée disponible',
            ], 404);
        }

        return response()->json([
            'type' => $type,
            'period' => $period,
            'total_users' => $leaderboards->count(),
            'average_score' => round($leaderboards->avg('score'), 2),
            'highest_score' => $leaderboards->max('score'),
            'lowest_score' => $leaderboards->min('score'),
            'top_3' => LeaderboardResource::collection(
                $leaderboards->sortBy('rank')->take(3)
            ),
        ]);
    }

    /**
     * Compare user with another user
     */
    public function compare(Request $request, int $otherUserId): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Authentification requise',
            ], 401);
        }

        $request->validate([
            'type' => 'nullable|string|in:points,donations,engagement,social',
            'period' => 'nullable|string|in:daily,weekly,monthly,all_time',
        ]);

        $type = $request->input('type', 'points');
        $period = $request->input('period', 'all_time');

        $userRanking = Leaderboard::query()
            ->where('user_id', $user->id)
            ->ofType($type)
            ->ofPeriod($period)
            ->first();

        $otherRanking = Leaderboard::query()
            ->where('user_id', $otherUserId)
            ->ofType($type)
            ->ofPeriod($period)
            ->first();

        if (!$userRanking || !$otherRanking) {
            return response()->json([
                'message' => 'Impossible de comparer - classements non trouvés',
            ], 404);
        }

        return response()->json([
            'user' => new LeaderboardResource($userRanking),
            'other_user' => new LeaderboardResource($otherRanking),
            'comparison' => [
                'score_difference' => $userRanking->score - $otherRanking->score,
                'rank_difference' => $otherRanking->rank - $userRanking->rank,
                'user_ahead' => $userRanking->rank < $otherRanking->rank,
            ],
        ]);
    }
}
