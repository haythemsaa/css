<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DonationGoalResource;
use App\Models\DonationGoal;
use App\Models\Donation;
use App\Models\PaymentTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonationGoalController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DonationGoal::with(['milestones']);

        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->active();
            } else {
                $query->where('status', $request->status);
            }
        } else {
            $query->active();
        }

        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        if ($request->has('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->has('featured')) {
            $query->featured();
        }

        $goals = $query->orderByDesc('priority')
            ->orderBy('end_date', 'asc')
            ->paginate(20);

        return response()->json([
            'data' => DonationGoalResource::collection($goals->items()),
            'pagination' => [
                'current_page' => $goals->currentPage(),
                'last_page' => $goals->lastPage(),
                'per_page' => $goals->perPage(),
                'total' => $goals->total(),
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $goal = DonationGoal::with(['milestones', 'donations.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json(new DonationGoalResource($goal));
    }

    public function donate(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:5',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'is_anonymous' => 'boolean',
            'donor_message' => 'nullable|string|max:500',
            'dedication' => 'nullable|string|max:200',
        ]);

        $goal = DonationGoal::findOrFail($id);
        $user = $request->user();

        if ($validated['amount'] < $goal->min_donation) {
            return response()->json([
                'success' => false,
                'message' => "Le don minimum est de {$goal->min_donation} TND",
            ], 422);
        }

        if ($goal->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Cet objectif de don n\'est pas actif',
            ], 422);
        }

        // Create payment transaction
        $transaction = PaymentTransaction::create([
            'transaction_id' => PaymentTransaction::generateTransactionId(),
            'user_id' => $user->id,
            'payment_method_id' => $validated['payment_method_id'],
            'payable_type' => DonationGoal::class,
            'payable_id' => $goal->id,
            'amount' => $validated['amount'],
            'currency' => 'TND',
            'transaction_fee' => 0,
            'net_amount' => $validated['amount'],
            'status' => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Create donation
        $donation = Donation::create([
            'payment_transaction_id' => $transaction->id,
            'user_id' => $user->id,
            'goal_id' => $goal->id,
            'amount' => $validated['amount'],
            'payment_method' => 'card', // Will be updated from payment gateway
            'payment_status' => 'pending',
            'is_anonymous' => $validated['is_anonymous'] ?? false,
            'donor_message' => $validated['donor_message'] ?? null,
            'dedication' => $validated['dedication'] ?? null,
        ]);

        // In real implementation, process payment with gateway here
        // For now, mark as completed
        $transaction->markAsCompleted();
        $donation->update(['payment_status' => 'completed']);

        // Update goal
        $goal->addDonation($validated['amount'], $user);

        return response()->json([
            'success' => true,
            'message' => 'Don effectué avec succès',
            'donation' => $donation,
            'goal' => $goal->fresh(),
        ], 201);
    }

    public function myDonations(Request $request): JsonResponse
    {
        $user = $request->user();

        $donations = Donation::with(['goal'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($donations);
    }

    public function categories(): JsonResponse
    {
        $categories = [
            ['key' => 'litigation', 'name' => 'Paiement de Litiges', 'icon' => 'gavel'],
            ['key' => 'player_transfer', 'name' => 'Achat de Joueurs', 'icon' => 'users'],
            ['key' => 'stadium_renovation', 'name' => 'Rénovation du Stade', 'icon' => 'building'],
            ['key' => 'youth_academy', 'name' => 'Académie des Jeunes', 'icon' => 'graduation-cap'],
            ['key' => 'equipment', 'name' => 'Équipements', 'icon' => 'box'],
            ['key' => 'debt_payment', 'name' => 'Remboursement de Dettes', 'icon' => 'credit-card'],
            ['key' => 'other', 'name' => 'Autre', 'icon' => 'ellipsis-h'],
        ];

        return response()->json(['categories' => $categories]);
    }
}
