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
}
