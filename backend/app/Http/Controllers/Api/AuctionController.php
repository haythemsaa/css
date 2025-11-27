<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuctionProductResource;
use App\Http\Resources\AuctionBidResource;
use App\Http\Resources\AuctionWinnerResource;
use App\Models\AuctionProduct;
use App\Models\AuctionBid;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = AuctionProduct::with(['currentWinner', 'bids']);

        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'upcoming') {
                $query->upcoming();
            } elseif ($request->status === 'ended') {
                $query->ended();
            }
        } else {
            $query->active();
        }

        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        if ($request->has('featured')) {
            $query->featured();
        }

        $auctions = $query->orderBy('end_time', 'asc')->paginate(20);

        return response()->json([
            'data' => AuctionProductResource::collection($auctions->items()),
            'pagination' => [
                'current_page' => $auctions->currentPage(),
                'last_page' => $auctions->lastPage(),
                'per_page' => $auctions->perPage(),
                'total' => $auctions->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $auction = AuctionProduct::with(['currentWinner', 'bids.user'])->findOrFail($id);

        return response()->json(new AuctionProductResource($auction));
    }

    public function placeBid(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'bid_amount' => 'required|numeric|min:0',
            'is_auto_bid' => 'boolean',
            'max_auto_bid' => 'nullable|numeric|min:0',
        ]);

        $auction = AuctionProduct::findOrFail($id);
        $user = $request->user();

        $canBid = $auction->canBid($user, $validated['bid_amount']);

        if (!$canBid['can_bid']) {
            return response()->json([
                'success' => false,
                'message' => $canBid['reason'],
            ], 422);
        }

        $bid = $auction->placeBid(
            $user,
            $validated['bid_amount'],
            $validated['is_auto_bid'] ?? false,
            $validated['max_auto_bid'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Enchère placée avec succès',
            'bid' => new AuctionBidResource($bid),
            'auction' => new AuctionProductResource($auction->fresh()),
        ], 201);
    }

    public function myBids(Request $request): JsonResponse
    {
        $user = $request->user();

        $bids = AuctionBid::with(['auctionProduct'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'data' => AuctionBidResource::collection($bids->items()),
            'pagination' => [
                'current_page' => $bids->currentPage(),
                'last_page' => $bids->lastPage(),
                'per_page' => $bids->perPage(),
                'total' => $bids->total(),
            ],
        ]);
    }

    public function myWins(Request $request): JsonResponse
    {
        $user = $request->user();

        $wins = $user->auctionWins()
            ->with(['auctionProduct', 'paymentMethod'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'data' => AuctionWinnerResource::collection($wins->items()),
            'pagination' => [
                'current_page' => $wins->currentPage(),
                'last_page' => $wins->lastPage(),
                'per_page' => $wins->perPage(),
                'total' => $wins->total(),
            ],
        ]);
    }

    public function buyNow(Request $request, int $id): JsonResponse
    {
        $auction = AuctionProduct::findOrFail($id);

        if (!$auction->buy_now_price) {
            return response()->json([
                'success' => false,
                'message' => 'Achat immédiat non disponible pour cet article',
            ], 422);
        }

        if (!$auction->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Cette enchère n\'est pas active',
            ], 422);
        }

        $user = $request->user();

        // Create winning bid at buy now price
        $bid = $auction->placeBid($user, $auction->buy_now_price);

        // End auction immediately
        $auction->update(['status' => 'sold', 'end_time' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Achat immédiat effectué avec succès',
            'auction' => new AuctionProductResource($auction->fresh()),
        ]);
    }

    // ========================================
    // ADMIN METHODS
    // ========================================

    /**
     * Create a new auction (Admin only)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:auction_products,slug',
            'description' => 'required|string',
            'images' => 'required|array',
            'images.*' => 'string',
            'category' => 'required|in:collectibles,memorabilia,experiences,signed_items',
            'starting_price' => 'required|numeric|min:0',
            'reserve_price' => 'nullable|numeric|min:0',
            'buy_now_price' => 'nullable|numeric|min:0',
            'bid_increment' => 'required|numeric|min:1',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'is_featured' => 'boolean',
            'auto_extend' => 'boolean',
            'auto_extend_minutes' => 'nullable|integer|min:1',
            'terms_conditions' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $auction = AuctionProduct::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Enchère créée avec succès',
            'data' => new AuctionProductResource($auction),
        ], 201);
    }

    /**
     * Update an auction (Admin only)
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $auction = AuctionProduct::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'slug' => 'string|unique:auction_products,slug,' . $id,
            'description' => 'string',
            'images' => 'array',
            'images.*' => 'string',
            'category' => 'in:collectibles,memorabilia,experiences,signed_items',
            'starting_price' => 'numeric|min:0',
            'reserve_price' => 'nullable|numeric|min:0',
            'buy_now_price' => 'nullable|numeric|min:0',
            'bid_increment' => 'numeric|min:1',
            'start_time' => 'date',
            'end_time' => 'date|after:start_time',
            'is_featured' => 'boolean',
            'auto_extend' => 'boolean',
            'auto_extend_minutes' => 'nullable|integer|min:1',
            'status' => 'in:scheduled,active,ended,sold,cancelled',
            'terms_conditions' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $auction->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Enchère mise à jour avec succès',
            'data' => new AuctionProductResource($auction->fresh()),
        ]);
    }

    /**
     * Delete an auction (Admin only)
     */
    public function destroy(int $id): JsonResponse
    {
        $auction = AuctionProduct::findOrFail($id);

        // Check if auction has bids
        if ($auction->bids()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer une enchère avec des enchères actives',
            ], 422);
        }

        $auction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Enchère supprimée avec succès',
        ]);
    }

    /**
     * Get auction statistics (Admin only)
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_auctions' => AuctionProduct::count(),
            'active_auctions' => AuctionProduct::active()->count(),
            'ended_auctions' => AuctionProduct::ended()->count(),
            'total_bids' => AuctionBid::count(),
            'total_revenue' => AuctionProduct::where('status', 'sold')->sum('current_bid'),
            'featured_auctions' => AuctionProduct::featured()->count(),
            'by_category' => AuctionProduct::selectRaw('category, count(*) as count')
                ->groupBy('category')
                ->get(),
        ];

        return response()->json($stats);
    }
}
