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
}
