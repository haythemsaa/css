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

    // ========================================
    // ADMIN METHODS
    // ========================================

    /**
     * Admin: Get all campaigns
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $query = Campaign::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by featured
        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        // Search
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $campaigns = $query->paginate($request->get('per_page', 20));

        return response()->json($campaigns);
    }

    /**
     * Admin: Create a new campaign
     */
    public function adminStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:campaigns',
            'description' => 'required|string',
            'type' => 'required|in:donation,crowdfunding,sponsorship',
            'goal_amount' => 'required|numeric|min:0',
            'current_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,active,completed,cancelled',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_featured' => 'boolean',
            'image_url' => 'nullable|url',
        ]);

        $campaign = Campaign::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Campagne créée avec succès',
            'campaign' => $campaign,
        ], 201);
    }

    /**
     * Admin: Update a campaign
     */
    public function adminUpdate(Request $request, int $id): JsonResponse
    {
        $campaign = Campaign::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:campaigns,slug,' . $id,
            'description' => 'sometimes|string',
            'type' => 'sometimes|in:donation,crowdfunding,sponsorship',
            'goal_amount' => 'sometimes|numeric|min:0',
            'current_amount' => 'nullable|numeric|min:0',
            'status' => 'sometimes|in:draft,active,completed,cancelled',
            'starts_at' => 'sometimes|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_featured' => 'boolean',
            'image_url' => 'nullable|url',
        ]);

        $campaign->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Campagne mise à jour avec succès',
            'campaign' => $campaign->fresh(),
        ]);
    }

    /**
     * Admin: Delete a campaign
     */
    public function adminDestroy(int $id): JsonResponse
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->delete();

        return response()->json([
            'success' => true,
            'message' => 'Campagne supprimée avec succès',
        ]);
    }
}
