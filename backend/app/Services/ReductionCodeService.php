<?php

namespace App\Services;

use App\Models\ReductionCode;
use App\Models\Partner;
use App\Models\User;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class ReductionCodeService
{
    /**
     * Generate a reduction code with QR
     */
    public function generate(User $user, Partner $partner, ?int $offerId = null): ReductionCode
    {
        // Generate unique code
        $code = strtoupper(Str::random(8));

        // Get discount based on user type
        $discount = $partner->getDiscountForUser($user);

        // Create reduction code
        $reductionCode = ReductionCode::create([
            'user_id' => $user->id,
            'partner_id' => $partner->id,
            'offer_id' => $offerId,
            'code' => $code,
            'type' => 'qr',
            'discount_percentage' => $discount,
            'max_discount_amount' => $partner->max_discount_amount,
            'generated_at' => now(),
            'expires_at' => now()->addMinutes(15),
            'is_active' => true,
        ]);

        // Generate QR code
        $qrCodePath = $this->generateQRCode($code);
        $reductionCode->update(['qr_code' => $qrCodePath]);

        return $reductionCode;
    }

    /**
     * Generate QR code image
     */
    protected function generateQRCode(string $code): string
    {
        $path = 'qrcodes/' . $code . '.png';

        // TODO: Generate actual QR code and save to storage
        // QrCode::format('png')->size(300)->generate($code, storage_path('app/public/' . $path));

        return $path;
    }

    /**
     * Validate and use a reduction code
     */
    public function validate(
        string $code,
        float $originalAmount,
        ?float $latitude = null,
        ?float $longitude = null
    ): array {
        $reductionCode = ReductionCode::where('code', $code)->firstOrFail();

        if (!$reductionCode->isValid()) {
            throw new \Exception('Invalid or expired code');
        }

        $partner = $reductionCode->partner;
        $user = $reductionCode->user;

        // Calculate discount
        $discountAmount = $partner->calculateDiscountAmount($originalAmount, $user);
        $finalAmount = $originalAmount - $discountAmount;
        $commission = ($discountAmount * $partner->commission_percentage) / 100;

        // Mark as used
        $reductionCode->update([
            'is_used' => true,
            'used_at' => now(),
            'original_amount' => $originalAmount,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
            'usage_latitude' => $latitude,
            'usage_longitude' => $longitude,
        ]);

        // Create usage record
        $reductionCode->usages()->create([
            'user_id' => $user->id,
            'partner_id' => $partner->id,
            'original_amount' => $originalAmount,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
            'commission_earned' => $commission,
            'location_lat' => $latitude,
            'location_lng' => $longitude,
            'validated_at' => now(),
        ]);

        // Update partner stats
        $partner->increment('total_uses');
        $partner->increment('total_revenue', $discountAmount);
        $partner->increment('total_commission', $commission);

        // Award loyalty points
        $points = (int) ($discountAmount / 10);
        $user->addLoyaltyPoints($points);

        return [
            'original_amount' => $originalAmount,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
            'commission' => $commission,
            'points_earned' => $points,
        ];
    }

    /**
     * Clean expired codes
     */
    public function cleanExpiredCodes(): int
    {
        return ReductionCode::expired()
            ->where('is_used', false)
            ->update(['is_active' => false]);
    }
}
