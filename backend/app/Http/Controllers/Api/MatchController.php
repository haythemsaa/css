<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FootballMatchResource;
use App\Models\FootballMatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    /**
     * Get all matches
     */
    public function index(Request $request): JsonResponse
    {
        $query = FootballMatch::query();

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

        // Filter upcoming matches
        if ($request->boolean('upcoming')) {
            $query->where('match_date', '>', now())
                  ->orderBy('match_date', 'asc');
        }

        // Filter past matches
        if ($request->boolean('past')) {
            $query->where('match_date', '<', now())
                  ->orderBy('match_date', 'desc');
        }

        // Default ordering
        if (!$request->has('upcoming') && !$request->has('past')) {
            $query->orderByDesc('match_date');
        }

        $matches = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => FootballMatchResource::collection($matches),
            'meta' => [
                'current_page' => $matches->currentPage(),
                'last_page' => $matches->lastPage(),
                'per_page' => $matches->perPage(),
                'total' => $matches->total(),
            ],
        ]);
    }

    /**
     * Get upcoming matches
     */
    public function upcoming(): JsonResponse
    {
        $matches = FootballMatch::where('match_date', '>', now())
            ->orderBy('match_date', 'asc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => FootballMatchResource::collection($matches),
        ]);
    }

    /**
     * Get latest results
     */
    public function results(): JsonResponse
    {
        $matches = FootballMatch::whereIn('status', ['finished', 'completed'])
            ->orderByDesc('match_date')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => FootballMatchResource::collection($matches),
        ]);
    }

    /**
     * Get single match
     */
    public function show(int $id): JsonResponse
    {
        $match = FootballMatch::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new FootballMatchResource($match),
        ]);
    }
}
