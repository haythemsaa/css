<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReductionCode;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ReductionCodeController extends Controller
{
    /**
     * Generate a new reduction code
     */
    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'offer_id' => 'nullable|exists:partner_offers,id',
        ]);

        $user = $request->user();
        $partner = Partner::findOrFail($validated['partner_id']);

        // Check if partner is active
        if (!$partner->is_active) {
            return response()->json([
                'message' => 'This partner is currently unavailable',
            ], 400);
        }

        // Check daily capacity
        if ($partner->hasReachedDailyCapacity()) {
            return response()->json([
                'message' => 'Daily capacity reached for this partner',
            ], 429);
        }

        // Get discount based on user type
        $discount = $partner->getDiscountForUser($user);

        if ($discount <= 0) {
            return response()->json([
                'message' => 'No discount available for your user level',
            ], 403);
        }

        // Generate unique code
        $code = strtoupper(Str::random(8));

        // Create reduction code
        $reductionCode = ReductionCode::create([
            'user_id' => $user->id,
            'partner_id' => $partner->id,
            'offer_id' => $validated['offer_id'] ?? null,
            'code' => $code,
            'type' => 'qr',
            'discount_percentage' => $discount,
            'max_discount_amount' => $partner->max_discount_amount,
            'generated_at' => now(),
            'expires_at' => now()->addMinutes(15), // 15 minutes expiration
            'is_active' => true,
        ]);

        // TODO: Generate QR code image
        // $qrCodePath = $this->generateQRCode($code);
        // $reductionCode->update(['qr_code' => $qrCodePath]);

        return response()->json([
            'message' => 'Reduction code generated successfully',
            'code' => $reductionCode,
            'partner' => $partner,
            'expires_in_minutes' => 15,
        ], 201);
    }

    /**
     * Get active reduction codes
     */
    public function active(Request $request): JsonResponse
    {
        $codes = ReductionCode::query()
            ->with('partner')
            ->where('user_id', $request->user()->id)
            ->where('is_active', true)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($codes);
    }

    /**
     * Get reduction code history
     */
    public function history(Request $request): JsonResponse
    {
        $history = ReductionCode::query()
            ->with('partner')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($history);
    }

    /**
     * Validate and use reduction code
     */
    public function validate(Request $request, string $code): JsonResponse
    {
        $validated = $request->validate([
            'original_amount' => 'required|numeric|min:0',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $reductionCode = ReductionCode::query()
            ->with('partner')
            ->where('code', $code)
            ->firstOrFail();

        // Check if already used
        if ($reductionCode->is_used) {
            return response()->json([
                'message' => 'This code has already been used',
            ], 400);
        }

        // Check if expired
        if ($reductionCode->expires_at < now()) {
            return response()->json([
                'message' => 'This code has expired',
            ], 400);
        }

        // Check if active
        if (!$reductionCode->is_active) {
            return response()->json([
                'message' => 'This code is not active',
            ], 400);
        }

        // Calculate discount
        $originalAmount = $validated['original_amount'];
        $partner = $reductionCode->partner;
        $discountAmount = $partner->calculateDiscountAmount($originalAmount, $request->user());
        $finalAmount = $originalAmount - $discountAmount;

        // Calculate commission for club
        $commission = ($discountAmount * $partner->commission_percentage) / 100;

        // Mark as used
        $reductionCode->update([
            'is_used' => true,
            'used_at' => now(),
            'original_amount' => $originalAmount,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
            'usage_latitude' => $validated['latitude'] ?? null,
            'usage_longitude' => $validated['longitude'] ?? null,
        ]);

        // Create usage record
        $reductionCode->usages()->create([
            'user_id' => $request->user()->id,
            'partner_id' => $partner->id,
            'original_amount' => $originalAmount,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
            'commission_earned' => $commission,
            'location_lat' => $validated['latitude'] ?? null,
            'location_lng' => $validated['longitude'] ?? null,
            'validated_at' => now(),
        ]);

        // Update partner stats
        $partner->increment('total_uses');
        $partner->increment('total_revenue', $discountAmount);
        $partner->increment('total_commission', $commission);

        // Award loyalty points to user
        $points = (int) ($discountAmount / 10); // 1 point per 10 TND saved
        $request->user()->addLoyaltyPoints($points);

        return response()->json([
            'message' => 'Code validated successfully',
            'original_amount' => $originalAmount,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
            'you_saved' => $discountAmount,
            'points_earned' => $points,
        ]);
    }

    /**
     * Rate reduction usage
     */
    public function rate(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:500',
        ]);

        $reductionCode = ReductionCode::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        if (!$reductionCode->is_used) {
            return response()->json([
                'message' => 'Cannot rate unused code',
            ], 400);
        }

        // Update usage record
        $usage = $reductionCode->usages()->first();
        if ($usage) {
            $usage->update([
                'user_satisfaction_rating' => $validated['rating'],
                'user_feedback' => $validated['feedback'] ?? null,
            ]);
        }

        return response()->json([
            'message' => 'Rating submitted successfully',
        ]);
    }

    /**
     * Get user's savings stats
     */
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        $totalSaved = ReductionCode::query()
            ->where('user_id', $user->id)
            ->where('is_used', true)
            ->sum('discount_amount');

        $totalCodes = ReductionCode::query()
            ->where('user_id', $user->id)
            ->where('is_used', true)
            ->count();

        $mostUsedPartner = ReductionCode::query()
            ->where('user_id', $user->id)
            ->where('is_used', true)
            ->groupBy('partner_id')
            ->selectRaw('partner_id, COUNT(*) as uses')
            ->orderBy('uses', 'desc')
            ->first();

        return response()->json([
            'total_saved' => round($totalSaved, 2),
            'total_codes_used' => $totalCodes,
            'average_saving' => $totalCodes > 0 ? round($totalSaved / $totalCodes, 2) : 0,
            'most_used_partner_id' => $mostUsedPartner?->partner_id,
        ]);
    }
}
