<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPremiumAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        if (!in_array($user->user_type, ['premium', 'socios'])) {
            return response()->json([
                'message' => 'Premium subscription required',
                'user_type' => $user->user_type,
                'required_type' => 'premium or socios',
            ], 403);
        }

        // Check if subscription is active
        if ($user->user_type === 'premium' && !$user->hasActiveSubscription()) {
            return response()->json([
                'message' => 'Your subscription has expired',
                'expired_at' => $user->subscription_expires_at,
            ], 403);
        }

        return $next($request);
    }
}
