<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'user_type' => $this->user_type,
            'city' => $this->city,
            'avatar' => $this->avatar,
            'bio' => $this->bio,
            'loyalty_points' => $this->loyalty_points,
            'loyalty_level' => $this->loyalty_level,
            'referral_code' => $this->referral_code,

            // Socios info
            'is_socios' => $this->user_type === 'socios',
            'socios_number' => $this->when($this->user_type === 'socios', $this->socios_number),
            'socios_since' => $this->when($this->user_type === 'socios', $this->socios_since),

            // Subscription info
            'has_active_subscription' => $this->hasActiveSubscription(),
            'subscription_expires_at' => $this->subscription_expires_at,

            // Stats
            'total_donations' => $this->when($request->user()?->id === $this->id, function () {
                return $this->donations()->sum('amount');
            }),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
