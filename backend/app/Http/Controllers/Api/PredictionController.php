<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MatchPrediction;
use App\Models\PredictionLeaderboard;
use App\Models\Match;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PredictionController extends Controller
{
    /**
     * Get matches available for prediction
     */
    public function getAvailableMatches(Request $request): JsonResponse
    {
        $user = Auth::user();

        $matches = Match::where('status', 'scheduled')
            ->where('match_date', '>', now())
            ->with(['homeTeam', 'awayTeam', 'competition'])
            ->orderBy('match_date')
            ->get();

        $matchesWithPredictions = $matches->map(function ($match) use ($user) {
            $matchData = $match->toArray();

            $prediction = MatchPrediction::where('match_id', $match->id)
                ->where('user_id', $user->id)
                ->first();

            $matchData['user_prediction'] = $prediction;
            $matchData['can_predict'] = !$prediction || $prediction->canEdit();
            $matchData['time_until_match'] = now()->diffInHours($match->match_date);

            return $matchData;
        });

        return response()->json([
            'success' => true,
            'matches' => $matchesWithPredictions,
        ]);
    }

    /**
     * Submit or update a prediction
     */
    public function submitPrediction(Request $request, int $matchId): JsonResponse
    {
        $user = Auth::user();

        $request->validate([
            'predicted_home_score' => 'required|integer|min:0',
            'predicted_away_score' => 'required|integer|min:0',
            'predicted_first_scorer_id' => 'nullable|exists:players,id',
        ]);

        $match = Match::find($matchId);

        if (!$match) {
            return response()->json([
                'success' => false,
                'message' => 'Match non trouvé',
            ], 404);
        }

        // Check if match hasn't started
        if ($match->match_date && now()->greaterThanOrEqualTo($match->match_date)) {
            return response()->json([
                'success' => false,
                'message' => 'Le match a déjà commencé',
            ], 400);
        }

        // Determine result
        $homeScore = $request->input('predicted_home_score');
        $awayScore = $request->input('predicted_away_score');

        $predictedResult = 'draw';
        if ($homeScore > $awayScore) {
            $predictedResult = 'home_win';
        } elseif ($homeScore < $awayScore) {
            $predictedResult = 'away_win';
        }

        // Create or update prediction
        $prediction = MatchPrediction::updateOrCreate(
            [
                'user_id' => $user->id,
                'match_id' => $matchId,
            ],
            [
                'predicted_home_score' => $homeScore,
                'predicted_away_score' => $awayScore,
                'predicted_result' => $predictedResult,
                'predicted_first_scorer_id' => $request->input('predicted_first_scorer_id'),
                'predicted_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Pronostic enregistré avec succès',
            'prediction' => $prediction,
        ]);
    }

    /**
     * Get user's predictions
     */
    public function getMyPredictions(Request $request): JsonResponse
    {
        $user = Auth::user();

        $predictions = MatchPrediction::where('user_id', $user->id)
            ->with(['match.homeTeam', 'match.awayTeam', 'match.competition'])
            ->orderByDesc('predicted_at')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'predictions' => $predictions,
        ]);
    }

    /**
     * Get prediction leaderboard
     */
    public function getLeaderboard(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 50);
        $user = Auth::user();

        $leaderboard = PredictionLeaderboard::with('user')
            ->orderByDesc('total_points')
            ->orderByDesc('accuracy_percentage')
            ->limit($limit)
            ->get()
            ->map(function ($entry, $index) {
                $entry->rank = $index + 1;
                $entry->save();
                return $entry;
            });

        // Get current user's rank
        $userRank = null;
        if ($user) {
            $userLeaderboard = $user->predictionLeaderboard;
            if ($userLeaderboard) {
                $userRank = PredictionLeaderboard::where('total_points', '>', $userLeaderboard->total_points)
                    ->orWhere(function ($q) use ($userLeaderboard) {
                        $q->where('total_points', '=', $userLeaderboard->total_points)
                          ->where('accuracy_percentage', '>', $userLeaderboard->accuracy_percentage);
                    })
                    ->count() + 1;
            }
        }

        return response()->json([
            'success' => true,
            'leaderboard' => $leaderboard,
            'user_rank' => $userRank,
            'user_stats' => $user->predictionLeaderboard,
        ]);
    }

    /**
     * Get user's prediction stats
     */
    public function getMyStats(Request $request): JsonResponse
    {
        $user = Auth::user();

        $stats = $user->predictionLeaderboard ?? $user->predictionLeaderboard()->create([]);

        // Get recent performance
        $recentPredictions = MatchPrediction::where('user_id', $user->id)
            ->where('is_processed', true)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'recent_predictions' => $recentPredictions,
        ]);
    }

    /**
     * Process predictions for a completed match
     */
    public function processMatch(Request $request, int $matchId): JsonResponse
    {
        $match = Match::find($matchId);

        if (!$match || $match->status !== 'finished') {
            return response()->json([
                'success' => false,
                'message' => 'Match non terminé',
            ], 400);
        }

        $predictions = MatchPrediction::where('match_id', $matchId)
            ->where('is_processed', false)
            ->get();

        foreach ($predictions as $prediction) {
            $prediction->process();
        }

        return response()->json([
            'success' => true,
            'message' => "Pronostics traités: {$predictions->count()}",
            'processed_count' => $predictions->count(),
        ]);
    }
}
