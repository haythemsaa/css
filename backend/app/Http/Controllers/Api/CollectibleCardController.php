<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CollectibleCard;
use App\Models\UserCard;
use App\Models\CardTrade;
use Illuminate\Http\Request;

class CollectibleCardController extends Controller
{
    /**
     * Get available collectible cards
     */
    public function available(Request $request)
    {
        $query = CollectibleCard::with('player')->active();

        // Filter by rarity
        if ($request->has('rarity')) {
            $query->byRarity($request->rarity);
        }

        // Filter by season
        if ($request->has('season')) {
            $query->bySeason($request->season);
        }

        $cards = $query->paginate(20);

        return response()->json($cards);
    }

    /**
     * Get user's card collection
     */
    public function myCollection(Request $request)
    {
        $user = $request->user();

        $userCards = UserCard::with(['card', 'card.player'])
            ->forUser($user->id)
            ->latest()
            ->paginate(20);

        $stats = [
            'total_cards' => UserCard::forUser($user->id)->count(),
            'unique_cards' => UserCard::forUser($user->id)->distinct('card_id')->count('card_id'),
            'favorites' => UserCard::forUser($user->id)->favorites()->count(),
        ];

        return response()->json([
            'cards' => $userCards,
            'stats' => $stats,
        ]);
    }

    /**
     * Acquire a card (through purchase, achievement, etc.)
     */
    public function acquire(Request $request, $id)
    {
        $request->validate([
            'method' => 'required|string|in:purchase,achievement,gift',
        ]);

        $user = $request->user();
        $card = CollectibleCard::findOrFail($id);

        if (!$card->is_active) {
            return response()->json(['message' => 'Card is not available'], 400);
        }

        // Create user card
        $userCard = UserCard::create([
            'user_id' => $user->id,
            'card_id' => $card->id,
            'acquisition_method' => $request->method,
        ]);

        // Award loyalty points based on rarity
        $points = (int) ($card->getRarityMultiplier() * 10);
        $user->addLoyaltyPoints($points);

        return response()->json([
            'message' => 'Card acquired successfully',
            'card' => $userCard->load('card'),
            'points_earned' => $points,
        ], 201);
    }

    /**
     * Get trade offers
     */
    public function tradeOffers(Request $request)
    {
        $user = $request->user();

        $offers = CardTrade::with(['initiator', 'offeredCard.card', 'requestedCard.card'])
            ->forUser($user->id)
            ->pending()
            ->latest()
            ->paginate(20);

        return response()->json($offers);
    }

    /**
     * Propose a trade
     */
    public function proposeTrade(Request $request)
    {
        $request->validate([
            'offered_user_card_id' => 'required|exists:user_cards,id',
            'requested_user_card_id' => 'required|exists:user_cards,id',
            'message' => 'nullable|string|max:500',
        ]);

        $user = $request->user();

        // Verify user owns the offered card
        $offeredCard = UserCard::findOrFail($request->offered_user_card_id);
        if ($offeredCard->user_id !== $user->id) {
            return response()->json(['message' => 'You do not own this card'], 403);
        }

        if (!$offeredCard->isAvailableForTrade()) {
            return response()->json(['message' => 'Card is not available for trade'], 400);
        }

        // Verify requested card belongs to another user
        $requestedCard = UserCard::findOrFail($request->requested_user_card_id);
        if ($requestedCard->user_id === $user->id) {
            return response()->json(['message' => 'Cannot trade with yourself'], 400);
        }

        if (!$requestedCard->isAvailableForTrade()) {
            return response()->json(['message' => 'Requested card is not available for trade'], 400);
        }

        // Create trade
        $trade = CardTrade::create([
            'initiator_id' => $user->id,
            'recipient_id' => $requestedCard->user_id,
            'offered_user_card_id' => $offeredCard->id,
            'requested_user_card_id' => $requestedCard->id,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Trade offer sent successfully',
            'trade' => $trade->load(['offeredCard.card', 'requestedCard.card']),
        ], 201);
    }

    /**
     * Accept a trade
     */
    public function acceptTrade(Request $request, $id)
    {
        $user = $request->user();
        $trade = CardTrade::findOrFail($id);

        if (!$trade->canBeRespondedBy($user)) {
            return response()->json(['message' => 'Cannot respond to this trade'], 403);
        }

        $success = $trade->accept();

        if (!$success) {
            return response()->json(['message' => 'Trade could not be completed'], 400);
        }

        return response()->json([
            'message' => 'Trade accepted successfully',
            'trade' => $trade->fresh(),
        ]);
    }

    /**
     * Reject a trade
     */
    public function rejectTrade(Request $request, $id)
    {
        $user = $request->user();
        $trade = CardTrade::findOrFail($id);

        if (!$trade->canBeRespondedBy($user)) {
            return response()->json(['message' => 'Cannot respond to this trade'], 403);
        }

        $trade->reject();

        return response()->json([
            'message' => 'Trade rejected',
        ]);
    }
}
