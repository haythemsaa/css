<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PartnerOffer;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    /**
     * Get all offers
     */
    public function index(Request $request)
    {
        $query = PartnerOffer::with(['partner', 'partner.category'])
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());

        // Filter by partner category
        if ($request->has('category_id')) {
            $query->whereHas('partner', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        // Filter by user type discount
        $user = $request->user();
        if ($user && $user->user_type === 'socios') {
            $query->where('discount_socios', '>', 0);
        } elseif ($user && $user->user_type === 'premium') {
            $query->where('discount_premium', '>', 0);
        }

        $offers = $query->latest()->paginate(20);

        return response()->json($offers);
    }

    /**
     * Get a single offer
     */
    public function show($id)
    {
        $offer = PartnerOffer::with(['partner', 'partner.category'])
            ->findOrFail($id);

        if (!$offer->is_active || $offer->ends_at->isPast()) {
            return response()->json(['message' => 'Offer not available'], 404);
        }

        return response()->json($offer);
    }

    /**
     * Get flash offers (limited time)
     */
    public function flash(Request $request)
    {
        $offers = PartnerOffer::with(['partner', 'partner.category'])
            ->where('is_active', true)
            ->where('is_flash', true)
            ->where('ends_at', '>=', now())
            ->where('ends_at', '<=', now()->addHours(24))
            ->latest()
            ->get();

        return response()->json($offers);
    }

    /**
     * Get featured offers
     */
    public function featured(Request $request)
    {
        $offers = PartnerOffer::with(['partner', 'partner.category'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->where('ends_at', '>=', now())
            ->latest()
            ->limit(10)
            ->get();

        return response()->json($offers);
    }
}
