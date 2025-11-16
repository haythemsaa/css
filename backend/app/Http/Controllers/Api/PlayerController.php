<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlayerResource;
use App\Models\Player;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    /**
     * Get all players
     */
    public function index(Request $request): JsonResponse
    {
        $query = Player::query()->active();

        // Filter by position
        if ($request->has('position')) {
            $query->where('position', $request->position);
        }

        // Filter by nationality
        if ($request->has('nationality')) {
            $query->where('nationality', $request->nationality);
        }

        // Search
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('bio', 'like', '%' . $request->search . '%');
            });
        }

        // Order by jersey number or name
        $orderBy = $request->input('order_by', 'jersey_number');
        $query->orderBy($orderBy);

        $players = $query->paginate($request->input('per_page', 25));

        return response()->json([
            'success' => true,
            'data' => PlayerResource::collection($players),
            'meta' => [
                'current_page' => $players->currentPage(),
                'last_page' => $players->lastPage(),
                'per_page' => $players->perPage(),
                'total' => $players->total(),
            ],
        ]);
    }

    /**
     * Get single player
     */
    public function show(string $slug): JsonResponse
    {
        $player = Player::where('slug', $slug)
            ->active()
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => new PlayerResource($player),
        ]);
    }

    /**
     * Get players by position
     */
    public function byPosition(string $position): JsonResponse
    {
        $players = Player::where('position', $position)
            ->active()
            ->orderBy('jersey_number')
            ->get();

        return response()->json([
            'success' => true,
            'data' => PlayerResource::collection($players),
        ]);
    }
}
