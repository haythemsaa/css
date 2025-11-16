<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReductionCodeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $now = now();
        $isExpired = $this->expires_at < $now;
        $isUsedUp = $this->uses_count >= $this->max_uses;
        $isActive = $this->status === 'active' && !$isExpired && !$isUsedUp;

        return [
            'id' => $this->id,
            'code' => $this->code,
            'type' => $this->type,
            'type_label' => match($this->type) {
                'qr' => 'QR Code',
                'promo' => 'Code Promo',
                'nfc' => 'NFC Code',
                default => 'Code',
            },

            // Status
            'status' => $this->status,
            'is_active' => $isActive,
            'is_expired' => $isExpired,
            'is_used_up' => $isUsedUp,

            // Usage
            'uses_count' => $this->uses_count,
            'max_uses' => $this->max_uses,
            'remaining_uses' => max(0, $this->max_uses - $this->uses_count),

            // Validity
            'expires_at' => $this->expires_at->format('Y-m-d H:i:s'),
            'days_until_expiry' => $isExpired ? 0 : $this->expires_at->diffInDays($now),
            'hours_until_expiry' => $isExpired ? 0 : $this->expires_at->diffInHours($now),

            // QR Data (for QR codes)
            'qr_data' => $this->when($this->type === 'qr', $this->qr_data),

            // Offer details
            'offer' => new PartnerOfferResource($this->whenLoaded('offer')),

            // User (only show for admin/partner views, not for user's own codes)
            'user' => $this->when(
                $request->user()?->hasRole('admin') || $request->user()?->hasRole('partner'),
                new UserResource($this->whenLoaded('user'))
            ),

            // Usages
            'usages' => $this->when(
                $this->relationLoaded('usages'),
                function () {
                    return $this->usages->map(function ($usage) {
                        return [
                            'id' => $usage->id,
                            'original_amount' => $usage->original_amount,
                            'discount_amount' => $usage->discount_amount,
                            'final_amount' => $usage->final_amount,
                            'used_at' => $usage->used_at->format('Y-m-d H:i:s'),
                        ];
                    });
                }
            ),

            // Timestamps
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
