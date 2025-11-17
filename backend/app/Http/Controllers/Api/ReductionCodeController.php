<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReductionCodeResource;
use App\Models\PartnerOffer;
use App\Models\ReductionCode;
use App\Models\ReductionCodeUsage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReductionCodeController extends Controller
{
    /**
     * Generate a new reduction code for an offer
     */
    public function generate(Request $request, string $offerSlug): JsonResponse
    {
        $user = $request->user();

        // Check if user has access (Premium or Socios only)
        if ($user->user_type === 'free') {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être membre Premium ou Socios pour générer des codes de réduction.',
                'upgrade_required' => true,
            ], 403);
        }

        // Get the offer
        $offer = PartnerOffer::where('slug', $offerSlug)
            ->with('partner')
            ->where('status', 'active')
            ->firstOrFail();

        // Check if offer is still valid
        if ($offer->valid_until && $offer->valid_until < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Cette offre n\'est plus valide.',
            ], 400);
        }

        // Check stock
        if ($offer->stock_available !== null && $offer->stock_available <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cette offre n\'est plus disponible.',
            ], 400);
        }

        // Generate the code
        $codeType = $request->input('type', 'qr'); // qr, promo, or nfc

        DB::beginTransaction();
        try {
            $code = ReductionCode::create([
                'offer_id' => $offer->id,
                'user_id' => $user->id,
                'partner_id' => $offer->partner_id,
                'code' => $this->generateUniqueCode($offer, $codeType),
                'code_type' => $codeType,
                'reduction_value' => $offer->reduction_value,
                'reduction_type' => $offer->reduction_type,
                'status' => 'active',
                'generated_at' => now(),
                'expires_at' => $offer->valid_until ?? now()->addDays(30),
            ]);

            // Update stock
            $offer->increment('stock_used');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Code de réduction généré avec succès',
                'data' => new ReductionCodeResource($code->load(['offer.partner', 'user'])),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du code',
                'error' => $e->getMessage(),
                'trace' => app()->environment('local') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * Get user's reduction codes
     */
    public function myCodes(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = ReductionCode::where('user_id', $user->id)
            ->with(['offer.partner']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('code_type', $request->type);
        }

        // Filter active codes only
        if ($request->boolean('active_only')) {
            $query->where('status', 'active')
                  ->where('expires_at', '>', now());
        }

        $codes = $query->orderByDesc('created_at')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => ReductionCodeResource::collection($codes),
            'meta' => [
                'current_page' => $codes->currentPage(),
                'last_page' => $codes->lastPage(),
                'per_page' => $codes->perPage(),
                'total' => $codes->total(),
            ],
        ]);
    }

    /**
     * Get specific code details
     */
    public function show(Request $request, string $code): JsonResponse
    {
        $user = $request->user();

        $reductionCode = ReductionCode::where('code', $code)
            ->where('user_id', $user->id)
            ->with(['offer.partner'])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => new ReductionCodeResource($reductionCode),
        ]);
    }

    /**
     * Validate a code (for partner use)
     */
    public function validate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string',
        ]);

        $reductionCode = ReductionCode::where('code', $validated['code'])
            ->with(['offer.partner', 'user'])
            ->first();

        if (!$reductionCode) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'Code invalide',
            ], 404);
        }

        // Check if code is still valid
        $isValid = $reductionCode->status === 'active' &&
                   $reductionCode->expires_at > now();

        return response()->json([
            'success' => true,
            'valid' => $isValid,
            'data' => new ReductionCodeResource($reductionCode),
            'message' => $isValid ? 'Code valide' : 'Code non valide ou expiré',
        ]);
    }

    /**
     * Mark a code as used (for partner use)
     */
    public function use(Request $request, string $code): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $reductionCode = ReductionCode::where('code', $code)
                ->with(['offer.partner', 'user'])
                ->lockForUpdate()
                ->first();

            if (!$reductionCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code invalide',
                ], 404);
            }

            // Validate code
            if ($reductionCode->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce code a été ' . ($reductionCode->status === 'used' ? 'déjà utilisé' : 'annulé'),
                ], 400);
            }

            if ($reductionCode->expires_at < now()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce code a expiré',
                ], 400);
            }

            // Calculate discount amount
            $discountAmount = $this->calculateDiscount(
                $validated['amount'],
                $reductionCode->offer->reduction_type,
                $reductionCode->offer->reduction_value
            );

            // Record usage - TODO: Create ReductionCodeUsage model and migration
            // $usage = ReductionCodeUsage::create([
            //     'reduction_code_id' => $reductionCode->id,
            //     'user_id' => $reductionCode->user_id,
            //     'partner_id' => $reductionCode->offer->partner_id,
            //     'original_amount' => $validated['amount'],
            //     'discount_amount' => $discountAmount,
            //     'final_amount' => $validated['amount'] - $discountAmount,
            //     'used_at' => now(),
            // ]);

            // Update code status to used
            $reductionCode->update(['status' => 'used']);

            // Award loyalty points (10% of final amount)
            $loyaltyPoints = (int) floor(($validated['amount'] - $discountAmount) * 0.10);
            $reductionCode->user->increment('loyalty_points', $loyaltyPoints);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Code utilisé avec succès',
                'data' => [
                    'original_amount' => $validated['amount'],
                    'discount_amount' => $discountAmount,
                    'final_amount' => $validated['amount'] - $discountAmount,
                    'loyalty_points_earned' => $loyaltyPoints,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'utilisation du code',
            ], 500);
        }
    }

    /**
     * Generate a unique code
     */
    private function generateUniqueCode(PartnerOffer $offer, string $type): string
    {
        do {
            $prefix = match($type) {
                'qr' => 'QR',
                'promo' => 'PROMO',
                'nfc' => 'NFC',
                default => 'CODE',
            };

            $code = $prefix . '-' . strtoupper(Str::random(8));
        } while (ReductionCode::where('code', $code)->exists());

        return $code;
    }

    /**
     * Generate QR code data
     */
    private function generateQRData(PartnerOffer $offer, $user): string
    {
        return json_encode([
            'offer_id' => $offer->id,
            'user_id' => $user->id,
            'partner_id' => $offer->partner_id,
            'reduction' => $offer->reduction_value,
            'type' => $offer->reduction_type,
            'timestamp' => now()->timestamp,
        ]);
    }

    /**
     * Calculate discount amount
     */
    private function calculateDiscount(float $amount, string $type, float $value): float
    {
        if ($type === 'percentage') {
            return round($amount * ($value / 100), 2);
        }

        return min($value, $amount); // Fixed amount, but not more than the original amount
    }
}
