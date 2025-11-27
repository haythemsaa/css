<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketPurchaseResource;
use App\Http\Resources\TicketResource;
use App\Models\Match;
use App\Models\Ticket;
use App\Models\TicketPurchase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    /**
     * Get available tickets for a match
     */
    public function index(Request $request, int $matchId): AnonymousResourceCollection
    {
        $request->validate([
            'category' => 'nullable|string|in:vip,tribune,pelouse,family',
        ]);

        $query = Ticket::query()
            ->forMatch($matchId)
            ->available();

        if ($request->has('category')) {
            $query->ofCategory($request->input('category'));
        }

        $tickets = $query->orderBy('price', 'asc')->get();

        return TicketResource::collection($tickets);
    }

    /**
     * Get user's ticket purchases
     */
    public function myTickets(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Authentification requise',
            ], 401);
        }

        $request->validate([
            'status' => 'nullable|string|in:pending,confirmed,used,cancelled',
        ]);

        $query = TicketPurchase::query()
            ->with(['ticket', 'match'])
            ->where('user_id', $user->id);

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        $purchases = $query->orderBy('created_at', 'desc')->paginate(20);

        return TicketPurchaseResource::collection($purchases);
    }

    /**
     * Purchase tickets
     */
    public function purchase(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Authentification requise',
            ], 401);
        }

        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'quantity' => 'required|integer|min:1|max:10',
            'payment_method' => 'required|string|in:d17,konnect,paymee,sadad,stripe',
            'attendee_info' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            $ticket = Ticket::with('match')->findOrFail($request->input('ticket_id'));

            if (!$ticket->is_sale_active) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Ce billet n\'est pas disponible à la vente',
                ], 400);
            }

            $quantity = $request->input('quantity');

            if ($ticket->available_quantity < $quantity) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Quantité insuffisante disponible',
                ], 400);
            }

            // Reserve tickets
            if (!$ticket->reserveTickets($quantity)) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Impossible de réserver les billets',
                ], 400);
            }

            $totalPrice = $ticket->price * $quantity;

            $purchase = TicketPurchase::create([
                'ticket_number' => TicketPurchase::generateTicketNumber(),
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'match_id' => $ticket->match_id,
                'quantity' => $quantity,
                'unit_price' => $ticket->price,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'payment_method' => $request->input('payment_method'),
                'payment_status' => 'pending',
                'attendee_info' => $request->input('attendee_info'),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Billets réservés avec succès',
                'purchase' => new TicketPurchaseResource($purchase->load(['ticket', 'match'])),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erreur lors de l\'achat des billets',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get single ticket purchase
     */
    public function show(Request $request, TicketPurchase $purchase): JsonResponse
    {
        $user = $request->user();

        if (!$user || $purchase->user_id !== $user->id) {
            return response()->json([
                'message' => 'Non autorisé',
            ], 403);
        }

        $purchase->load(['ticket', 'match']);

        return response()->json([
            'purchase' => new TicketPurchaseResource($purchase),
        ]);
    }

    /**
     * Cancel ticket purchase
     */
    public function cancel(Request $request, TicketPurchase $purchase): JsonResponse
    {
        $user = $request->user();

        if (!$user || $purchase->user_id !== $user->id) {
            return response()->json([
                'message' => 'Non autorisé',
            ], 403);
        }

        if (!in_array($purchase->status, ['pending', 'confirmed'])) {
            return response()->json([
                'message' => 'Ce billet ne peut pas être annulé',
            ], 400);
        }

        // Check if match is not too soon (allow cancellation up to 24h before match)
        if ($purchase->match->match_date->subHours(24)->isPast()) {
            return response()->json([
                'message' => 'Les billets ne peuvent être annulés moins de 24h avant le match',
            ], 400);
        }

        $purchase->cancel();

        return response()->json([
            'message' => 'Billet annulé avec succès',
        ]);
    }

    /**
     * Verify QR code (for gate entry)
     */
    public function verifyQRCode(Request $request): JsonResponse
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $purchase = TicketPurchase::where('qr_code', $request->input('qr_code'))
            ->with(['ticket', 'match', 'user'])
            ->first();

        if (!$purchase) {
            return response()->json([
                'valid' => false,
                'message' => 'QR code invalide',
            ], 404);
        }

        if ($purchase->status === 'used') {
            return response()->json([
                'valid' => false,
                'message' => 'Ce billet a déjà été utilisé',
                'used_at' => $purchase->used_at,
            ], 400);
        }

        if ($purchase->status !== 'confirmed') {
            return response()->json([
                'valid' => false,
                'message' => 'Ce billet n\'est pas confirmé',
            ], 400);
        }

        return response()->json([
            'valid' => true,
            'purchase' => new TicketPurchaseResource($purchase),
            'message' => 'Billet valide',
        ]);
    }

    /**
     * Mark ticket as used (gate entry)
     */
    public function markAsUsed(Request $request): JsonResponse
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $purchase = TicketPurchase::where('qr_code', $request->input('qr_code'))->first();

        if (!$purchase) {
            return response()->json([
                'message' => 'QR code invalide',
            ], 404);
        }

        if ($purchase->status === 'used') {
            return response()->json([
                'message' => 'Ce billet a déjà été utilisé',
            ], 400);
        }

        $purchase->markAsUsed();

        return response()->json([
            'message' => 'Billet marqué comme utilisé',
        ]);
    }
}
