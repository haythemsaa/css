<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CampaignController extends Controller
{
    /**
     * Get all campaigns
     */
    public function index(Request $request): JsonResponse
    {
        $query = Campaign::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'active');
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Only active campaigns within date range
        $query->where('starts_at', '<=', now())
              ->where(function ($q) {
                  $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
              });

        // Sort
        $query->orderBy('is_featured', 'desc')
              ->orderBy('created_at', 'desc');

        $campaigns = $query->paginate($request->get('per_page', 20));

        return response()->json($campaigns);
    }

    /**
     * Get single campaign
     */
    public function show(string $slug): JsonResponse
    {
        $campaign = Campaign::query()
            ->where('slug', $slug)
            ->firstOrFail();

        // Calculate progress percentage
        $progress = $campaign->goal_amount > 0
            ? ($campaign->current_amount / $campaign->goal_amount) * 100
            : 0;

        $campaign->progress_percentage = min(100, round($progress, 2));

        return response()->json($campaign);
    }
}
