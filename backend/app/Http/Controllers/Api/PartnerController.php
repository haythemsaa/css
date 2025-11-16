<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PartnerCategoryResource;
use App\Http\Resources\PartnerOfferResource;
use App\Http\Resources\PartnerResource;
use App\Models\Partner;
use App\Models\PartnerCategory;
use App\Models\PartnerOffer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    /**
     * Get all partner categories
     */
    public function categories(): JsonResponse
    {
        $categories = PartnerCategory::active()
            ->orderBy('display_order')
            ->withCount('partners')
            ->get();

        return response()->json([
            'success' => true,
            'data' => PartnerCategoryResource::collection($categories),
        ]);
    }

    /**
     * Get all partners (with optional filters)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Partner::query()
            ->with(['category', 'offers' => function ($q) {
                $q->active()->orderBy('display_order');
            }])
            ->active();

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by city/governorate
        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        if ($request->has('governorate')) {
            $query->where('governorate', $request->governorate);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Geographic search (nearby partners)
        if ($request->has('latitude') && $request->has('longitude')) {
            $radius = $request->input('radius', 10); // Default 10km
            $query->nearby($request->latitude, $request->longitude, $radius);
        } else {
            // Default ordering by featured and name
            $query->orderByDesc('featured_order')->orderBy('name');
        }

        $partners = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => PartnerResource::collection($partners),
            'meta' => [
                'current_page' => $partners->currentPage(),
                'last_page' => $partners->lastPage(),
                'per_page' => $partners->perPage(),
                'total' => $partners->total(),
            ],
        ]);
    }

    /**
     * Get featured partners
     */
    public function featured(): JsonResponse
    {
        $partners = Partner::active()
            ->with(['category', 'offers' => function ($q) {
                $q->active()->orderBy('display_order');
            }])
            ->where('featured_order', '>', 0)
            ->orderBy('featured_order')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => PartnerResource::collection($partners),
        ]);
    }

    /**
     * Get single partner details
     */
    public function show(string $slug): JsonResponse
    {
        $partner = Partner::where('slug', $slug)
            ->with(['category', 'offers' => function ($q) {
                $q->active()->orderBy('display_order');
            }])
            ->active()
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => new PartnerResource($partner),
        ]);
    }

    /**
     * Get all offers (with optional filters)
     */
    public function offers(Request $request): JsonResponse
    {
        $query = PartnerOffer::query()
            ->with(['partner.category'])
            ->active();

        // Filter by offer type
        if ($request->has('type')) {
            $query->where('offer_type', $request->type);
        }

        // Filter by partner category
        if ($request->has('category')) {
            $query->whereHas('partner.category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by reduction value
        if ($request->has('min_reduction')) {
            $query->where('reduction_value', '>=', $request->min_reduction);
        }

        // Only featured offers
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // Order by
        $orderBy = $request->input('order_by', 'created_at');
        $orderDirection = $request->input('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        $offers = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => PartnerOfferResource::collection($offers),
            'meta' => [
                'current_page' => $offers->currentPage(),
                'last_page' => $offers->lastPage(),
                'per_page' => $offers->perPage(),
                'total' => $offers->total(),
            ],
        ]);
    }

    /**
     * Get single offer details
     */
    public function showOffer(string $slug): JsonResponse
    {
        $offer = PartnerOffer::where('slug', $slug)
            ->with(['partner.category'])
            ->active()
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => new PartnerOfferResource($offer),
        ]);
    }

    /**
     * Get partner's offers
     */
    public function partnerOffers(string $partnerSlug): JsonResponse
    {
        $partner = Partner::where('slug', $partnerSlug)->active()->firstOrFail();

        $offers = PartnerOffer::where('partner_id', $partner->id)
            ->active()
            ->orderBy('display_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => PartnerOfferResource::collection($offers),
        ]);
    }
}
