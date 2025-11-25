<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

        return response()->json($auctions);
    }

    public function show(int $id): JsonResponse
    {
        $auction = AuctionProduct::with(['currentWinner', 'bids.user'])->findOrFail($id);

        return response()->json($auction);
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
            'bid' => $bid,
            'auction' => $auction->fresh(),
        ], 201);
    }

    public function myBids(Request $request): JsonResponse
    {
        $user = $request->user();

        $bids = AuctionBid::with(['auctionProduct'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($bids);
    }

    public function myWins(Request $request): JsonResponse
    {
        $user = $request->user();

        $wins = $user->auctionWins()
            ->with(['auctionProduct', 'paymentMethod'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($wins);
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
            'auction' => $auction->fresh(),
        ]);
    }
}
