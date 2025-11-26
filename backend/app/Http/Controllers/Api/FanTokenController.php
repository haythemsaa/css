<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FanTokenWallet;
use App\Models\FanTokenTransaction;
use App\Models\Reward;
use App\Models\RewardRedemption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FanTokenController extends Controller
{
    /**
     * Get user's wallet info
     */
    public function getWallet(Request $request): JsonResponse
    {
        $wallet = $this->getOrCreateWallet($request->user());

        return response()->json([
            'wallet' => [
                'balance' => $wallet->balance,
                'lifetime_earned' => $wallet->lifetime_earned,
                'lifetime_spent' => $wallet->lifetime_spent,
                'level' => $wallet->level,
                'level_name' => $wallet->level_name,
                'experience_points' => $wallet->experience_points,
                'progress_to_next_level' => $wallet->progress_to_next_level,
                'can_claim_daily_bonus' => $wallet->canClaimDailyBonus(),
                'last_daily_bonus_at' => $wallet->last_daily_bonus_at,
            ],
        ]);
    }

    /**
     * Get transaction history
     */
    public function getTransactions(Request $request): JsonResponse
    {
        $wallet = $this->getOrCreateWallet($request->user());

        $transactions = FanTokenTransaction::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json($transactions);
    }

    /**
     * Claim daily bonus
     */
    public function claimDailyBonus(Request $request): JsonResponse
    {
        $wallet = $this->getOrCreateWallet($request->user());

        if (!$wallet->canClaimDailyBonus()) {
            return response()->json([
                'error' => 'Vous avez déjà réclamé votre bonus quotidien'
            ], 422);
        }

        $transaction = $wallet->claimDailyBonus();

        return response()->json([
            'message' => 'Bonus quotidien réclamé!',
            'tokens_earned' => $transaction->amount,
            'new_balance' => $wallet->balance,
            'transaction' => $transaction,
        ]);
    }

    /**
     * Get rewards catalog
     */
    public function getRewards(Request $request): JsonResponse
    {
        $query = Reward::with('redemptions')
            ->available();

        // Filters
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        if ($request->boolean('featured')) {
            $query->featured();
        }

        if ($request->has('min_cost')) {
            $query->where('token_cost', '>=', $request->min_cost);
        }

        if ($request->has('max_cost')) {
            $query->where('token_cost', '<=', $request->max_cost);
        }

        // User's balance for affordability check
        $wallet = $this->getOrCreateWallet($request->user());
        if ($request->boolean('affordable')) {
            $query->affordable($wallet->balance);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'popularity_score');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $rewards = $query->paginate($request->get('per_page', 15));

        // Add user-specific info
        $rewards->getCollection()->transform(function ($reward) use ($request) {
            $reward->can_redeem = $reward->canBeRedeemedBy($request->user());
            return $reward;
        });

        return response()->json([
            'rewards' => $rewards->items(),
            'user_balance' => $wallet->balance,
            'user_level' => $wallet->level,
            'pagination' => [
                'current_page' => $rewards->currentPage(),
                'per_page' => $rewards->perPage(),
                'total' => $rewards->total(),
                'last_page' => $rewards->lastPage(),
            ],
        ]);
    }

    /**
     * Get reward details
     */
    public function getRewardDetails(Request $request, int $id): JsonResponse
    {
        $reward = Reward::with('redemptions')->findOrFail($id);
        $wallet = $this->getOrCreateWallet($request->user());

        $userRedemptions = $reward->redemptions()
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['pending', 'processing', 'fulfilled'])
            ->count();

        return response()->json([
            'reward' => $reward,
            'can_redeem' => $reward->canBeRedeemedBy($request->user()),
            'user_balance' => $wallet->balance,
            'user_level' => $wallet->level,
            'user_redemptions' => $userRedemptions,
        ]);
    }

    /**
     * Redeem a reward
     */
    public function redeemReward(Request $request, int $id): JsonResponse
    {
        $reward = Reward::findOrFail($id);
        $wallet = $this->getOrCreateWallet($request->user());

        // Validation
        if (!$reward->canBeRedeemedBy($request->user())) {
            return response()->json([
                'error' => 'Vous ne pouvez pas échanger cette récompense'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'delivery_method' => 'nullable|in:pickup,shipping,digital',
            'delivery_address' => 'required_if:delivery_method,shipping',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create redemption
            $redemption = RewardRedemption::create([
                'user_id' => $request->user()->id,
                'reward_id' => $reward->id,
                'tokens_spent' => $reward->token_cost,
                'delivery_method' => $request->delivery_method ?? 'digital',
                'delivery_address' => $request->delivery_address,
            ]);

            // Deduct tokens
            $transaction = $wallet->deductTokens(
                $reward->token_cost,
                'spend',
                "Échange: {$reward->name}",
                ['reward_id' => $reward->id, 'redemption_id' => $redemption->id]
            );

            // Link transaction
            $redemption->update(['transaction_id' => $transaction->id]);

            // Update reward stock
            $reward->decrementStock();

            // Add experience points
            $xp = (int)($reward->token_cost / 10);
            $wallet->addExperience($xp);

            DB::commit();

            return response()->json([
                'message' => 'Récompense échangée avec succès!',
                'redemption' => $redemption->load('reward'),
                'redemption_code' => $redemption->redemption_code,
                'new_balance' => $wallet->fresh()->balance,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Erreur lors de l\'échange: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's redemptions
     */
    public function getMyRedemptions(Request $request): JsonResponse
    {
        $redemptions = RewardRedemption::with('reward')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($redemptions);
    }

    /**
     * Get specific redemption details
     */
    public function getRedemptionDetails(Request $request, int $id): JsonResponse
    {
        $redemption = RewardRedemption::with(['reward', 'transaction'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json($redemption);
    }

    /**
     * Cancel a redemption (if pending)
     */
    public function cancelRedemption(Request $request, int $id): JsonResponse
    {
        $redemption = RewardRedemption::where('user_id', $request->user()->id)
            ->findOrFail($id);

        if ($redemption->status !== 'pending') {
            return response()->json([
                'error' => 'Seules les rédemptions en attente peuvent être annulées'
            ], 422);
        }

        $redemption->cancel();

        return response()->json([
            'message' => 'Rédemption annulée et tokens remboursés',
        ]);
    }

    /**
     * Get leaderboard
     */
    public function getLeaderboard(Request $request): JsonResponse
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $leaderboard = DB::table('fan_token_wallets')
            ->join('users', 'fan_token_wallets.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                'users.avatar',
                'fan_token_wallets.balance',
                'fan_token_wallets.level',
                'fan_token_wallets.lifetime_earned',
                DB::raw('ROW_NUMBER() OVER (ORDER BY fan_token_wallets.lifetime_earned DESC) as rank')
            )
            ->orderBy('fan_token_wallets.lifetime_earned', 'desc')
            ->limit(100)
            ->get();

        // Get current user's rank
        $userWallet = $this->getOrCreateWallet($request->user());
        $userRank = DB::table('fan_token_wallets')
            ->where('lifetime_earned', '>', $userWallet->lifetime_earned)
            ->count() + 1;

        return response()->json([
            'leaderboard' => $leaderboard,
            'user_rank' => $userRank,
            'user_stats' => [
                'balance' => $userWallet->balance,
                'level' => $userWallet->level,
                'lifetime_earned' => $userWallet->lifetime_earned,
            ],
        ]);
    }

    /**
     * Get or create user's wallet
     */
    private function getOrCreateWallet($user): FanTokenWallet
    {
        return FanTokenWallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'balance' => 100, // Welcome bonus
                'lifetime_earned' => 100,
            ]
        );
    }
}
