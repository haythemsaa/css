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
}
