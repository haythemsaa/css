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
        $isUsed = $this->status === 'used';
        $isActive = $this->status === 'active' && !$isExpired;

        return [
            'id' => $this->id,
            'code' => $this->code,
            'type' => $this->code_type,
            'type_label' => match($this->code_type) {
                'qr' => 'QR Code',
                'promo' => 'Code Promo',
                'nfc' => 'NFC Code',
                'wallet' => 'Wallet',
                default => 'Code',
            },

            // Status
            'status' => $this->status,
            'is_active' => $isActive,
            'is_expired' => $isExpired,
            'is_used' => $isUsed,

            // Validity
            'generated_at' => $this->generated_at?->format('Y-m-d H:i:s'),
            'expires_at' => $this->expires_at->format('Y-m-d H:i:s'),
            'days_until_expiry' => $isExpired ? 0 : $this->expires_at->diffInDays($now),
            'hours_until_expiry' => $isExpired ? 0 : $this->expires_at->diffInHours($now),

            // QR Code image URL (if available)
            'qr_code_image_url' => $this->qr_code_image_url,

            // Reduction details
            'reduction_value' => $this->reduction_value,
            'reduction_type' => $this->reduction_type,

            // Offer details
            'offer' => new PartnerOfferResource($this->whenLoaded('offer')),

            // User (only show if loaded)
            'user' => $this->when(
                $this->relationLoaded('user'),
                new UserResource($this->whenLoaded('user'))
            ),

            // Timestamps
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
