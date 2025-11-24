<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SociosBenefit;
use App\Models\SociosBenefitRedemption;
use Illuminate\Http\Request;

class SociosController extends Controller
{
    /**
     * Verify Socios status (admin only)
     */
    public function verify(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'socios_number' => 'required|string',
        ]);

        // This would normally verify against a database or external system
        $user = \App\Models\User::findOrFail($request->user_id);

        $user->user_type = 'socios';
        $user->socios_verified = true;
        $user->socios_number = $request->socios_number;
        $user->socios_since = now();
        $user->save();

        return response()->json([
            'message' => 'Socios status verified successfully',
            'user' => $user,
        ]);
    }

    /**
     * Get available Socios benefits
     */
    public function benefits(Request $request)
    {
        $user = $request->user();

        if ($user->user_type !== 'socios') {
            return response()->json(['message' => 'Socios membership required'], 403);
        }

        $benefits = SociosBenefit::active()
            ->get()
            ->map(function ($benefit) use ($user) {
                $benefit->can_redeem = $benefit->canBeRedeemedBy($user);
                $benefit->remaining_stock = $benefit->remaining_stock;
                return $benefit;
            });

        return response()->json($benefits);
    }

    /**
     * Redeem a benefit
     */
    public function redeemBenefit(Request $request, $id)
    {
        $user = $request->user();

        if ($user->user_type !== 'socios') {
            return response()->json(['message' => 'Socios membership required'], 403);
        }

        $benefit = SociosBenefit::findOrFail($id);

        $redemption = $benefit->redeemFor($user);

        if (!$redemption) {
            return response()->json(['message' => 'Cannot redeem this benefit'], 400);
        }

        return response()->json([
            'message' => 'Benefit redeemed successfully',
            'redemption' => $redemption->load('benefit'),
            'remaining_points' => $user->fresh()->loyalty_points,
        ], 201);
    }

    /**
     * Get Socios events (exclusive events)
     */
    public function events(Request $request)
    {
        $user = $request->user();

        if ($user->user_type !== 'socios') {
            return response()->json(['message' => 'Socios membership required'], 403);
        }

        // This would fetch events from a dedicated events table
        // Simplified here for demonstration
        $events = [
            [
                'id' => 1,
                'name' => 'Meet & Greet with Players',
                'date' => now()->addDays(15)->toDateString(),
                'location' => 'Stade Taïeb Mhiri',
                'capacity' => 50,
                'registered' => 23,
            ],
            [
                'id' => 2,
                'name' => 'Socios Exclusive Match Viewing',
                'date' => now()->addDays(7)->toDateString(),
                'location' => 'CSS Club House',
                'capacity' => 100,
                'registered' => 87,
            ],
        ];

        return response()->json($events);
    }

    /**
     * Get loyalty points history for Socios
     */
    public function pointsHistory(Request $request)
    {
        $user = $request->user();

        if ($user->user_type !== 'socios') {
            return response()->json(['message' => 'Socios membership required'], 403);
        }

        // This would fetch from an activity/transaction log
        // Simplified here
        $history = \App\Models\ActivityLog::forUser($user->id)
            ->byAction('loyalty_points')
            ->recent(30)
            ->latest()
            ->paginate(20);

        return response()->json([
            'current_points' => $user->loyalty_points,
            'loyalty_level' => $user->loyalty_level,
            'history' => $history,
        ]);
    }
}
