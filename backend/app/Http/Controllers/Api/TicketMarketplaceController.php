<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TicketListing;
use App\Models\TicketTransaction;
use App\Models\MarketplaceReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TicketMarketplaceController extends Controller
{
    /**
     * Get all ticket listings
     */
    public function index(Request $request): JsonResponse
    {
        $query = TicketListing::with(['seller', 'match'])
            ->available();

        // Filters
        if ($request->has('match_id')) {
            $query->forMatch($request->match_id);
        }

        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        if ($request->has('min_price')) {
            $query->where('selling_price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('selling_price', '<=', $request->max_price);
        }

        if ($request->boolean('featured')) {
            $query->featured();
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'listed_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $listings = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'listings' => $listings->items(),
            'pagination' => [
                'current_page' => $listings->currentPage(),
                'per_page' => $listings->perPage(),
                'total' => $listings->total(),
                'last_page' => $listings->lastPage(),
            ],
        ]);
    }

    /**
     * Get listing details
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $listing = TicketListing::with([
            'seller',
            'match',
            'transaction'
        ])->findOrFail($id);

        // Get seller rating
        $sellerRating = MarketplaceReview::forUser($listing->seller_id)
            ->avg('rating');

        $sellerReviews = MarketplaceReview::forUser($listing->seller_id)
            ->count();

        return response()->json([
            'listing' => $listing,
            'seller_rating' => round($sellerRating, 1),
            'seller_reviews_count' => $sellerReviews,
        ]);
    }

    /**
     * Create a new ticket listing
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'match_id' => 'required|exists:matches,id',
            'ticket_number' => 'required|string|unique:ticket_listings',
            'category' => 'required|in:vip,tribune,populaire,virage',
            'section' => 'nullable|string',
            'row' => 'nullable|string',
            'seat_number' => 'nullable|string',
            'quantity' => 'required|integer|min:1|max:10',
            'original_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'images' => 'nullable|array',
            'images.*' => 'string',
            'allow_negotiation' => 'boolean',
            'minimum_price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $listing = TicketListing::create([
            'seller_id' => $request->user()->id,
            ...$validator->validated(),
        ]);

        return response()->json([
            'message' => 'Annonce créée avec succès. Elle sera vérifiée sous peu.',
            'listing' => $listing,
        ], 201);
    }

    /**
     * Update a listing
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $listing = TicketListing::findOrFail($id);

        // Only seller can update
        if ($listing->seller_id !== $request->user()->id) {
            return response()->json([
                'error' => 'Non autorisé'
            ], 403);
        }

        // Can't update if sold or reserved
        if (in_array($listing->status, ['sold', 'reserved'])) {
            return response()->json([
                'error' => 'Impossible de modifier une annonce vendue ou réservée'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'selling_price' => 'numeric|min:0',
            'description' => 'string|max:1000',
            'allow_negotiation' => 'boolean',
            'minimum_price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $listing->update($validator->validated());

        return response()->json([
            'message' => 'Annonce mise à jour',
            'listing' => $listing,
        ]);
    }

    /**
     * Reserve a ticket
     */
    public function reserve(Request $request, int $id): JsonResponse
    {
        $listing = TicketListing::findOrFail($id);

        if (!$listing->is_available) {
            return response()->json([
                'error' => 'Ce billet n\'est plus disponible'
            ], 422);
        }

        if ($listing->seller_id === $request->user()->id) {
            return response()->json([
                'error' => 'Vous ne pouvez pas réserver votre propre billet'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $listing->reserve($request->user());
            
            return response()->json([
                'message' => 'Billet réservé pour 15 minutes',
                'listing' => $listing->fresh(),
                'expires_at' => $listing->reserved_until,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Erreur lors de la réservation'
            ], 500);
        } finally {
            DB::commit();
        }
    }

    /**
     * Purchase a ticket
     */
    public function purchase(Request $request, int $id): JsonResponse
    {
        $listing = TicketListing::with('seller')->findOrFail($id);

        // Validate reservation
        if ($listing->status === 'reserved' && 
            $listing->reserved_by !== $request->user()->id) {
            return response()->json([
                'error' => 'Ce billet est réservé par quelqu\'un d\'autre'
            ], 422);
        }

        if (!in_array($listing->status, ['available', 'reserved'])) {
            return response()->json([
                'error' => 'Ce billet n\'est plus disponible'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|string',
            'delivery_method' => 'required|in:electronic,meeting,courier',
            'meeting_details' => 'required_if:delivery_method,meeting|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create transaction
            $transaction = TicketTransaction::create([
                'listing_id' => $listing->id,
                'buyer_id' => $request->user()->id,
                'seller_id' => $listing->seller_id,
                'ticket_price' => $listing->selling_price,
                'platform_fee' => $listing->platform_fee,
                'total_amount' => $listing->selling_price + $listing->platform_fee,
                'payment_status' => 'pending',
                'delivery_method' => $request->delivery_method,
                'meeting_details' => $request->meeting_details,
            ]);

            // Mark listing as sold
            $listing->markAsSold();

            DB::commit();

            return response()->json([
                'message' => 'Achat initié avec succès',
                'transaction' => $transaction,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Erreur lors de l\'achat'
            ], 500);
        }
    }

    /**
     * Cancel a listing
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $listing = TicketListing::findOrFail($id);

        if ($listing->seller_id !== $request->user()->id) {
            return response()->json([
                'error' => 'Non autorisé'
            ], 403);
        }

        if ($listing->status === 'sold') {
            return response()->json([
                'error' => 'Impossible d\'annuler un billet déjà vendu'
            ], 422);
        }

        $listing->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Annonce annulée',
        ]);
    }

    /**
     * Get user's listings
     */
    public function myListings(Request $request): JsonResponse
    {
        $listings = TicketListing::with(['match', 'transaction'])
            ->where('seller_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($listings);
    }

    /**
     * Get user's purchases
     */
    public function myPurchases(Request $request): JsonResponse
    {
        $transactions = TicketTransaction::with(['listing.match', 'seller'])
            ->where('buyer_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($transactions);
    }

    /**
     * Submit a review
     */
    public function submitReview(Request $request, int $transactionId): JsonResponse
    {
        $transaction = TicketTransaction::findOrFail($transactionId);

        $user = $request->user();
        $isBuyer = $transaction->buyer_id === $user->id;
        $isSeller = $transaction->seller_id === $user->id;

        if (!$isBuyer && !$isSeller) {
            return response()->json([
                'error' => 'Non autorisé'
            ], 403);
        }

        if (!$transaction->is_completed) {
            return response()->json([
                'error' => 'La transaction doit être complétée'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
            'would_trade_again' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $review = MarketplaceReview::create([
            'transaction_id' => $transaction->id,
            'reviewer_id' => $user->id,
            'reviewed_user_id' => $isBuyer ? $transaction->seller_id : $transaction->buyer_id,
            'role' => $isBuyer ? 'buyer' : 'seller',
            ...$validator->validated(),
        ]);

        // Update transaction review status
        if ($isBuyer) {
            $transaction->update(['buyer_reviewed' => true]);
        } else {
            $transaction->update(['seller_reviewed' => true]);
        }

        return response()->json([
            'message' => 'Avis soumis avec succès',
            'review' => $review,
        ], 201);
    }

    /**
     * Get user reviews (for trust score)
     */
    public function getUserReviews(int $userId): JsonResponse
    {
        $reviews = MarketplaceReview::with(['reviewer', 'transaction'])
            ->forUser($userId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'average_rating' => MarketplaceReview::forUser($userId)->avg('rating'),
            'total_reviews' => MarketplaceReview::forUser($userId)->count(),
            'positive_reviews' => MarketplaceReview::forUser($userId)->positive()->count(),
            'negative_reviews' => MarketplaceReview::forUser($userId)->negative()->count(),
        ];

        return response()->json([
            'reviews' => $reviews->items(),
            'stats' => $stats,
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }
}
