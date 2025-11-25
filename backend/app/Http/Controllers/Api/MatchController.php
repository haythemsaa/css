<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Match;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MatchController extends Controller
{
    /**
     * Get all matches
     */
    public function index(Request $request): JsonResponse
    {
        $query = Match::query()
            ->with(['homeTeam', 'awayTeam']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by competition
        if ($request->has('competition')) {
            $query->where('competition', $request->competition);
        }

        // Filter by season
        if ($request->has('season')) {
            $query->where('season', $request->season);
        }

        // Sort by date
        $query->orderBy('match_date', $request->get('sort', 'desc'));

        $matches = $query->paginate($request->get('per_page', 20));

        return response()->json($matches);
    }

    /**
     * Get single match
     */
    public function show(int $id): JsonResponse
    {
        $match = Match::query()
            ->with(['homeTeam', 'awayTeam'])
            ->findOrFail($id);

        return response()->json($match);
    }

    /**
     * Get live match data
     */
    public function live(int $id): JsonResponse
    {
        $match = Match::findOrFail($id);

        if (!in_array($match->status, ['live', 'halftime'])) {
            return response()->json([
                'message' => 'Match is not currently live',
            ], 400);
        }

        return response()->json([
            'match' => $match,
            'home_score' => $match->home_score,
            'away_score' => $match->away_score,
            'status' => $match->status,
            'statistics' => [
                'possession' => [
                    'home' => $match->home_possession,
                    'away' => $match->away_possession,
                ],
                'shots' => [
                    'home' => $match->home_shots,
                    'away' => $match->away_shots,
                ],
                'shots_on_target' => [
                    'home' => $match->home_shots_on_target,
                    'away' => $match->away_shots_on_target,
                ],
                'corners' => [
                    'home' => $match->home_corners,
                    'away' => $match->away_corners,
                ],
                'cards' => [
                    'home' => [
                        'yellow' => $match->home_yellow_cards,
                        'red' => $match->home_red_cards,
                    ],
                    'away' => [
                        'yellow' => $match->away_yellow_cards,
                        'red' => $match->away_red_cards,
                    ],
                ],
            ],
            'events' => $match->match_events,
        ]);
    }

    /**
     * Get match predictions (community game)
     */
    public function predict(int $id): JsonResponse
    {
        // TODO: Implement prediction logic
        return response()->json([
            'message' => 'Match predictions - To be implemented',
        ]);
    }

    /**
     * Store user's match prediction
     */
    public function storePrediction(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
        ]);

        $match = Match::findOrFail($id);

        if ($match->status !== 'scheduled') {
            return response()->json([
                'message' => 'Cannot predict after match has started',
            ], 400);
        }

        // TODO: Store prediction in database
        // $prediction = $match->predictions()->create([...]);

        return response()->json([
            'message' => 'Prediction saved successfully',
        ]);
    }

    /**
     * Get competition standings
     */
    public function standings(Request $request): JsonResponse
    {
        $competition = $request->get('competition', 'Ligue 1');
        $season = $request->get('season', '2024/2025');

        // TODO: Calculate standings from matches
        return response()->json([
            'message' => 'Standings - To be implemented',
            'competition' => $competition,
            'season' => $season,
        ]);
    }

    /**
     * Admin: Create a new match
     */
    public function adminStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'home_team' => 'required|string|max:255',
            'away_team' => 'required|string|max:255',
            'competition' => 'required|string|max:255',
            'season' => 'required|string|max:50',
            'match_date' => 'required|date',
            'stadium' => 'nullable|string|max:255',
            'status' => 'required|in:scheduled,live,halftime,finished,postponed,cancelled',
            'home_score' => 'nullable|integer|min:0',
            'away_score' => 'nullable|integer|min:0',
            'home_possession' => 'nullable|integer|min:0|max:100',
            'away_possession' => 'nullable|integer|min:0|max:100',
            'home_shots' => 'nullable|integer|min:0',
            'away_shots' => 'nullable|integer|min:0',
            'home_shots_on_target' => 'nullable|integer|min:0',
            'away_shots_on_target' => 'nullable|integer|min:0',
            'home_corners' => 'nullable|integer|min:0',
            'away_corners' => 'nullable|integer|min:0',
            'home_yellow_cards' => 'nullable|integer|min:0',
            'away_yellow_cards' => 'nullable|integer|min:0',
            'home_red_cards' => 'nullable|integer|min:0',
            'away_red_cards' => 'nullable|integer|min:0',
            'match_events' => 'nullable|json',
        ]);

        $match = Match::create($validated);

        return response()->json([
            'message' => 'Match créé avec succès',
            'match' => $match,
        ], 201);
    }

    /**
     * Admin: Update a match
     */
    public function adminUpdate(Request $request, int $id): JsonResponse
    {
        $match = Match::findOrFail($id);

        $validated = $request->validate([
            'home_team' => 'sometimes|string|max:255',
            'away_team' => 'sometimes|string|max:255',
            'competition' => 'sometimes|string|max:255',
            'season' => 'sometimes|string|max:50',
            'match_date' => 'sometimes|date',
            'stadium' => 'nullable|string|max:255',
            'status' => 'sometimes|in:scheduled,live,halftime,finished,postponed,cancelled',
            'home_score' => 'nullable|integer|min:0',
            'away_score' => 'nullable|integer|min:0',
            'home_possession' => 'nullable|integer|min:0|max:100',
            'away_possession' => 'nullable|integer|min:0|max:100',
            'home_shots' => 'nullable|integer|min:0',
            'away_shots' => 'nullable|integer|min:0',
            'home_shots_on_target' => 'nullable|integer|min:0',
            'away_shots_on_target' => 'nullable|integer|min:0',
            'home_corners' => 'nullable|integer|min:0',
            'away_corners' => 'nullable|integer|min:0',
            'home_yellow_cards' => 'nullable|integer|min:0',
            'away_yellow_cards' => 'nullable|integer|min:0',
            'home_red_cards' => 'nullable|integer|min:0',
            'away_red_cards' => 'nullable|integer|min:0',
            'match_events' => 'nullable|json',
        ]);

        $match->update($validated);

        return response()->json([
            'message' => 'Match mis à jour avec succès',
            'match' => $match,
        ]);
    }

    /**
     * Admin: Delete a match
     */
    public function adminDestroy(int $id): JsonResponse
    {
        $match = Match::findOrFail($id);
        $match->delete();

        return response()->json([
            'message' => 'Match supprimé avec succès',
        ]);
    }
}
