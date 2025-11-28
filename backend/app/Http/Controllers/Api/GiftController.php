<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GiftCampaign;
use App\Models\GiftDistribution;
use Illuminate\Http\Request;

class GiftController extends Controller
{
    /**
     * Get available gifts for current user
     */
    public function available(Request $request)
    {
        $user = $request->user();

        $campaigns = GiftCampaign::active()
            ->get()
            ->filter(function ($campaign) use ($user) {
                return $campaign->isUserEligible($user);
            });

        return response()->json($campaigns);
    }

    /**
     * Get user's gifts
     */
    public function myGifts(Request $request)
    {
        $user = $request->user();

        $gifts = GiftDistribution::with('campaign')
            ->forUser($user->id)
            ->latest()
            ->paginate(20);

        return response()->json($gifts);
    }

    /**
     * Claim a gift
     */
    public function claim(Request $request, $id)
    {
        $user = $request->user();
        $campaign = GiftCampaign::findOrFail($id);

        if (!$campaign->isActive()) {
            return response()->json(['message' => 'Campaign is not active'], 400);
        }

        if (!$campaign->isUserEligible($user)) {
            return response()->json(['message' => 'You are not eligible for this gift'], 403);
        }

        // Check if user already claimed today
        $alreadyClaimed = GiftDistribution::forUser($user->id)
            ->where('campaign_id', $campaign->id)
            ->whereDate('created_at', today())
            ->exists();

        if ($alreadyClaimed) {
            return response()->json(['message' => 'Gift already claimed today'], 400);
        }

        // Create gift distribution
        $gift = GiftDistribution::create([
            'campaign_id' => $campaign->id,
            'user_id' => $user->id,
            'gift_type' => $campaign->gift_type,
            'gift_value' => $campaign->gift_value,
            'expires_at' => now()->addDays(7),
            'redemption_code' => strtoupper(\Str::random(10)),
        ]);

        // Update campaign stats
        $campaign->increment('gifts_distributed');

        return response()->json([
            'message' => 'Gift claimed successfully',
            'gift' => $gift,
        ], 201);
    }

    /**
     * Get gift calendar (upcoming campaigns)
     */
    public function calendar(Request $request)
    {
        $campaigns = GiftCampaign::where('is_active', true)
            ->where('end_date', '>=', now())
            ->orderBy('start_date')
            ->get()
            ->groupBy(function ($campaign) {
                return $campaign->start_date->format('Y-m-d');
            });

        return response()->json($campaigns);
    }

    // ========================================
    // ADMIN METHODS
    // ========================================

    /**
     * Admin: Get all gift campaigns
     */
    public function adminIndex(Request $request)
    {
        $query = GiftCampaign::query();

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Filter by gift type
        if ($request->has('gift_type')) {
            $query->where('gift_type', $request->gift_type);
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
     * Admin: Create a new gift campaign
     */
    public function adminStore(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'gift_type' => 'required|in:discount,product,token,points',
            'gift_value' => 'required|numeric|min:0',
            'eligibility_criteria' => 'nullable|array',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
            'max_gifts' => 'nullable|integer|min:1',
        ]);

        $campaign = GiftCampaign::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Campagne cadeau créée avec succès',
            'campaign' => $campaign,
        ], 201);
    }

    /**
     * Admin: Update a gift campaign
     */
    public function adminUpdate(Request $request, $id)
    {
        $campaign = GiftCampaign::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'gift_type' => 'sometimes|in:discount,product,token,points',
            'gift_value' => 'sometimes|numeric|min:0',
            'eligibility_criteria' => 'nullable|array',
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
            'max_gifts' => 'nullable|integer|min:1',
        ]);

        $campaign->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Campagne cadeau mise à jour avec succès',
            'campaign' => $campaign->fresh(),
        ]);
    }

    /**
     * Admin: Delete a gift campaign
     */
    public function adminDestroy($id)
    {
        $campaign = GiftCampaign::findOrFail($id);

        // Delete distributions
        GiftDistribution::where('campaign_id', $campaign->id)->delete();

        // Delete campaign
        $campaign->delete();

        return response()->json([
            'success' => true,
            'message' => 'Campagne cadeau supprimée avec succès',
        ]);
    }
}
