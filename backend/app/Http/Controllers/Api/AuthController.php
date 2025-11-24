<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|unique:users,phone',
            'password' => ['required', 'confirmed', Password::min(8)],
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'city' => 'nullable|string',
        ]);

        // Generate unique referral code
        $validated['referral_code'] = Str::upper(Str::random(8));
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();

        // Check if banned
        if ($user->is_banned) {
            return response()->json([
                'message' => 'Your account has been banned',
                'reason' => $user->ban_reason,
            ], 403);
        }

        // Update last login
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get user profile
     */
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user()->load([
                'badges',
                'collectibleCards',
                'favoritePartners',
            ]),
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|unique:users,phone,' . $user->id,
            'date_of_birth' => 'sometimes|date',
            'gender' => 'sometimes|in:male,female,other',
            'city' => 'sometimes|string',
            'profile_photo' => 'sometimes|string',
            'preferred_language' => 'sometimes|in:fr,ar,en',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->fresh(),
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = $request->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return response()->json([
            'message' => 'Password changed successfully',
        ]);
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        // TODO: Implement OTP verification logic
        return response()->json([
            'message' => 'OTP verification - To be implemented',
        ]);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request): JsonResponse
    {
        // TODO: Implement OTP resend logic
        return response()->json([
            'message' => 'OTP resend - To be implemented',
        ]);
    }

    /**
     * Forgot password
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        // TODO: Implement forgot password logic
        return response()->json([
            'message' => 'Forgot password - To be implemented',
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request): JsonResponse
    {
        // TODO: Implement reset password logic
        return response()->json([
            'message' => 'Reset password - To be implemented',
        ]);
    }

    /**
     * Social login
     */
    public function socialLogin(Request $request, string $provider): JsonResponse
    {
        // TODO: Implement social login logic
        return response()->json([
            'message' => "Social login with {$provider} - To be implemented",
        ]);
    }

    /**
     * Delete account
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $user = $request->user();

        // Soft delete
        $user->delete();

        return response()->json([
            'message' => 'Account deleted successfully',
        ]);
    }
}
