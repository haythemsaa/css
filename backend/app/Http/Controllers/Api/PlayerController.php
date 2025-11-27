<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PlayerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Player::query()->active();

        if ($request->has('position')) {
            $query->where('position', $request->position);
        }

        $players = $query->orderBy('jersey_number')->get();

        return response()->json($players);
    }

    public function show(int $id): JsonResponse
    {
        $player = Player::findOrFail($id);
        return response()->json($player);
    }

    public function statistics(int $id): JsonResponse
    {
        $player = Player::findOrFail($id);

        return response()->json([
            'player' => $player,
            'stats' => [
                'goals' => $player->goals,
                'assists' => $player->assists,
                'matches_played' => $player->matches_played,
                'yellow_cards' => $player->yellow_cards,
                'red_cards' => $player->red_cards,
                'goals_per_match' => $player->matches_played > 0
                    ? round($player->goals / $player->matches_played, 2)
                    : 0,
            ],
        ]);
    }

    public function videos(int $id): JsonResponse
    {
        // TODO: Return player highlight videos
        return response()->json([
            'message' => 'Player videos - To be implemented',
        ]);
    }

    /**
     * Admin: Get all players (including inactive)
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $query = Player::query();

        // Filter by position
        if ($request->has('position')) {
            $query->where('position', $request->position);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('jersey_number', $request->search);
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'jersey_number');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $players = $query->paginate($request->get('per_page', 50));

        return response()->json($players);
    }

    /**
     * Admin: Create a new player
     */
    public function adminStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'jersey_number' => 'required|integer|min:1|max:99',
            'position' => 'required|in:goalkeeper,defender,midfielder,forward',
            'date_of_birth' => 'nullable|date',
            'nationality' => 'nullable|string|max:100',
            'height' => 'nullable|integer|min:150|max:220',
            'weight' => 'nullable|integer|min:50|max:150',
            'photo_url' => 'nullable|url',
            'bio' => 'nullable|string',
            'is_active' => 'boolean',
            'goals' => 'integer|min:0',
            'assists' => 'integer|min:0',
            'matches_played' => 'integer|min:0',
            'yellow_cards' => 'integer|min:0',
            'red_cards' => 'integer|min:0',
        ]);

        $player = Player::create($validated);

        return response()->json([
            'message' => 'Joueur créé avec succès',
            'player' => $player,
        ], 201);
    }

    /**
     * Admin: Update a player
     */
    public function adminUpdate(Request $request, int $id): JsonResponse
    {
        $player = Player::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'jersey_number' => 'sometimes|integer|min:1|max:99',
            'position' => 'sometimes|in:goalkeeper,defender,midfielder,forward',
            'date_of_birth' => 'nullable|date',
            'nationality' => 'nullable|string|max:100',
            'height' => 'nullable|integer|min:150|max:220',
            'weight' => 'nullable|integer|min:50|max:150',
            'photo_url' => 'nullable|url',
            'bio' => 'nullable|string',
            'is_active' => 'boolean',
            'goals' => 'integer|min:0',
            'assists' => 'integer|min:0',
            'matches_played' => 'integer|min:0',
            'yellow_cards' => 'integer|min:0',
            'red_cards' => 'integer|min:0',
        ]);

        $player->update($validated);

        return response()->json([
            'message' => 'Joueur mis à jour avec succès',
            'player' => $player,
        ]);
    }

    /**
     * Admin: Delete a player
     */
    public function adminDestroy(int $id): JsonResponse
    {
        $player = Player::findOrFail($id);
        $player->delete();

        return response()->json([
            'message' => 'Joueur supprimé avec succès',
        ]);
    }
}
