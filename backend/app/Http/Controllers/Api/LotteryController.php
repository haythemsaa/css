<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LotteryDraw;
use App\Models\LotteryTicket;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class LotteryController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Get active lottery draws
     */
    public function active(Request $request)
    {
        $draws = LotteryDraw::active()
            ->with(['tickets'])
            ->get();

        return response()->json($draws);
    }

    /**
     * Get a specific lottery draw
     */
    public function show($id)
    {
        $draw = LotteryDraw::with(['tickets', 'winner'])
            ->findOrFail($id);

        return response()->json($draw);
    }

    /**
     * Buy a lottery ticket
     */
    public function buyTicket(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
            'payment_method' => 'required|string',
        ]);

        $user = $request->user();
        $draw = LotteryDraw::findOrFail($id);

        if (!$draw->isActive()) {
            return response()->json(['message' => 'Lottery draw is not active'], 400);
        }

        $quantity = $request->quantity;

        if ($draw->remaining_tickets < $quantity) {
            return response()->json(['message' => 'Not enough tickets available'], 400);
        }

        $totalAmount = $draw->ticket_price * $quantity;

        // Process payment
        try {
            $payment = $this->paymentService->processPayment(
                $user,
                $totalAmount,
                $request->payment_method,
                'lottery_ticket',
                "Lottery: {$draw->name}"
            );

            if (!$payment['success']) {
                return response()->json(['message' => 'Payment failed'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Payment error: ' . $e->getMessage()], 500);
        }

        // Create tickets
        $tickets = [];
        for ($i = 0; $i < $quantity; $i++) {
            $tickets[] = LotteryTicket::create([
                'draw_id' => $draw->id,
                'user_id' => $user->id,
            ]);
        }

        // Update draw stats
        $draw->increment('tickets_sold', $quantity);

        return response()->json([
            'message' => 'Tickets purchased successfully',
            'tickets' => $tickets,
            'payment' => $payment,
        ], 201);
    }

    /**
     * Get user's lottery tickets
     */
    public function myTickets(Request $request)
    {
        $user = $request->user();

        $tickets = LotteryTicket::with('draw')
            ->forUser($user->id)
            ->latest()
            ->paginate(20);

        return response()->json($tickets);
    }

    /**
     * Get winners of a draw
     */
    public function winners($id)
    {
        $draw = LotteryDraw::with(['winner', 'tickets' => function ($query) {
            $query->where('is_winner', true);
        }])->findOrFail($id);

        if (!$draw->drawn_at) {
            return response()->json(['message' => 'Draw has not been completed yet'], 400);
        }

        return response()->json([
            'draw' => $draw,
            'winner' => $draw->winner,
            'winning_ticket' => $draw->tickets->first(),
        ]);
    }

    /**
     * Admin: Create a new lottery draw
     */
    public function adminStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prize_description' => 'required|string',
            'ticket_price' => 'required|numeric|min:0',
            'max_tickets' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'draw_date' => 'nullable|date|after:end_date',
            'is_active' => 'boolean',
            'image_url' => 'nullable|url',
        ]);

        $draw = LotteryDraw::create($validated);

        return response()->json([
            'message' => 'Tirage au sort créé avec succès',
            'draw' => $draw,
        ], 201);
    }

    /**
     * Admin: Update a lottery draw
     */
    public function adminUpdate(Request $request, $id)
    {
        $draw = LotteryDraw::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'prize_description' => 'sometimes|string',
            'ticket_price' => 'sometimes|numeric|min:0',
            'max_tickets' => 'sometimes|integer|min:1',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'draw_date' => 'nullable|date|after:end_date',
            'is_active' => 'boolean',
            'image_url' => 'nullable|url',
        ]);

        $draw->update($validated);

        return response()->json([
            'message' => 'Tirage au sort mis à jour avec succès',
            'draw' => $draw,
        ]);
    }

    /**
     * Admin: Delete a lottery draw
     */
    public function adminDestroy($id)
    {
        $draw = LotteryDraw::findOrFail($id);

        if ($draw->tickets_sold > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer un tirage avec des tickets vendus',
            ], 400);
        }

        $draw->delete();

        return response()->json([
            'message' => 'Tirage au sort supprimé avec succès',
        ]);
    }

    /**
     * Admin: Perform the lottery draw
     */
    public function adminPerformDraw($id)
    {
        $draw = LotteryDraw::findOrFail($id);

        if ($draw->drawn_at) {
            return response()->json(['message' => 'Draw already completed'], 400);
        }

        if ($draw->tickets_sold === 0) {
            return response()->json(['message' => 'No tickets sold'], 400);
        }

        // Select random winning ticket
        $winningTicket = $draw->tickets()->inRandomOrder()->first();
        $winningTicket->update(['is_winner' => true]);

        $draw->update([
            'winner_user_id' => $winningTicket->user_id,
            'drawn_at' => now(),
        ]);

        return response()->json([
            'message' => 'Tirage effectué avec succès',
            'winner' => $winningTicket->user,
            'winning_ticket' => $winningTicket,
        ]);
    }
}
