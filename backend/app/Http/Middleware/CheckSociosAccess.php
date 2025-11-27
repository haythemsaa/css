<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSociosAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        if ($user->user_type !== 'socios' || !$user->socios_verified) {
            return response()->json([
                'message' => 'Socios membership required',
                'user_type' => $user->user_type,
                'socios_verified' => $user->socios_verified,
            ], 403);
        }

        return $next($request);
    }
}
