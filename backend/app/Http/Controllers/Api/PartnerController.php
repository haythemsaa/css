<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\PartnerCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PartnerController extends Controller
{
    /**
     * Get all partners with filters
     */
    public function index(Request $request): JsonResponse
    {
        $query = Partner::query()
            ->with('category')
            ->active();

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by city
        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        // Search
        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        // Sort
        $query->orderBy('is_featured', 'desc')
              ->orderBy('priority', 'desc');

        $partners = $query->paginate($request->get('per_page', 20));

        return response()->json($partners);
    }

    /**
     * Get nearby partners
     */
    public function nearby(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:50',
        ]);

        $latitude = $validated['latitude'];
        $longitude = $validated['longitude'];
        $radius = $validated['radius'] ?? 5; // Default 5km

        $partners = Partner::query()
            ->with('category')
            ->active()
            ->nearby($latitude, $longitude, $radius)
            ->get();

        return response()->json($partners);
    }

    /**
     * Get single partner
     */
    public function show(int $id): JsonResponse
    {
        $partner = Partner::query()
            ->with(['category', 'offers', 'reviews'])
            ->findOrFail($id);

        return response()->json($partner);
    }

    /**
     * Get partner categories
     */
    public function categories(): JsonResponse
    {
        $categories = PartnerCategory::query()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return response()->json($categories);
    }

    /**
     * Add partner to favorites
     */
    public function favorite(Request $request, int $id): JsonResponse
    {
        $partner = Partner::findOrFail($id);
        $user = $request->user();

        if (!$user->favoritePartners()->where('partner_id', $id)->exists()) {
            $user->favoritePartners()->attach($id);
        }

        return response()->json([
            'message' => 'Partner added to favorites',
        ]);
    }

    /**
     * Remove partner from favorites
     */
    public function unfavorite(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $user->favoritePartners()->detach($id);

        return response()->json([
            'message' => 'Partner removed from favorites',
        ]);
    }

    /**
     * Get user's favorite partners
     */
    public function favorites(Request $request): JsonResponse
    {
        $favorites = $request->user()
            ->favoritePartners()
            ->with('category')
            ->get();

        return response()->json($favorites);
    }

    /**
     * Submit partner review
     */
    public function review(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $partner = Partner::findOrFail($id);
        $user = $request->user();

        $review = $partner->reviews()->create([
            'user_id' => $user->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'verified_purchase' => true, // TODO: Check if user actually used a code
        ]);

        // Update partner average rating
        $avgRating = $partner->reviews()->avg('rating');
        $partner->update([
            'average_rating' => round($avgRating, 2),
            'reviews_count' => $partner->reviews()->count(),
        ]);

        return response()->json([
            'message' => 'Review submitted successfully',
            'review' => $review,
        ], 201);
    }

    /**
     * Admin: Create a new partner
     */
    public function adminStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:partner_categories,id',
            'description' => 'nullable|string',
            'discount_description' => 'nullable|string',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'logo_url' => 'nullable|url',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'priority' => 'integer|min:0',
        ]);

        $partner = Partner::create($validated);

        return response()->json([
            'message' => 'Partenaire créé avec succès',
            'partner' => $partner,
        ], 201);
    }

    /**
     * Admin: Update a partner
     */
    public function adminUpdate(Request $request, int $id): JsonResponse
    {
        $partner = Partner::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:partner_categories,id',
            'description' => 'nullable|string',
            'discount_description' => 'nullable|string',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'logo_url' => 'nullable|url',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'priority' => 'integer|min:0',
        ]);

        $partner->update($validated);

        return response()->json([
            'message' => 'Partenaire mis à jour avec succès',
            'partner' => $partner,
        ]);
    }

    /**
     * Admin: Delete a partner
     */
    public function adminDestroy(int $id): JsonResponse
    {
        $partner = Partner::findOrFail($id);
        $partner->delete();

        return response()->json([
            'message' => 'Partenaire supprimé avec succès',
        ]);
    }
}
