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

    // ========================================
    // ADMIN METHODS
    // ========================================

    /**
     * Admin: Get all offers
     */
    public function adminIndex(Request $request)
    {
        $query = PartnerOffer::query()->with(['partner', 'partner.category']);

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Filter by featured
        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        // Filter by flash
        if ($request->has('is_flash')) {
            $query->where('is_flash', $request->boolean('is_flash'));
        }

        // Search
        if ($request->has('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $offers = $query->paginate($request->get('per_page', 20));

        return response()->json($offers);
    }

    /**
     * Admin: Create a new offer
     */
    public function adminStore(Request $request)
    {
        $validated = $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'discount_free' => 'nullable|numeric|min:0|max:100',
            'discount_premium' => 'nullable|numeric|min:0|max:100',
            'discount_socios' => 'nullable|numeric|min:0|max:100',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_flash' => 'boolean',
        ]);

        $offer = PartnerOffer::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Offre créée avec succès',
            'offer' => $offer->load(['partner', 'partner.category']),
        ], 201);
    }

    /**
     * Admin: Update an offer
     */
    public function adminUpdate(Request $request, $id)
    {
        $offer = PartnerOffer::findOrFail($id);

        $validated = $request->validate([
            'partner_id' => 'sometimes|exists:partners,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'discount_free' => 'nullable|numeric|min:0|max:100',
            'discount_premium' => 'nullable|numeric|min:0|max:100',
            'discount_socios' => 'nullable|numeric|min:0|max:100',
            'starts_at' => 'sometimes|date',
            'ends_at' => 'sometimes|date|after:starts_at',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_flash' => 'boolean',
        ]);

        $offer->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Offre mise à jour avec succès',
            'offer' => $offer->fresh(['partner', 'partner.category']),
        ]);
    }

    /**
     * Admin: Delete an offer
     */
    public function adminDestroy($id)
    {
        $offer = PartnerOffer::findOrFail($id);
        $offer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Offre supprimée avec succès',
        ]);
    }
}
