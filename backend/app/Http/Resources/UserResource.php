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
            'photo' => $this->photo,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'city' => $this->city,
            'governorate' => $this->governorate,

            // User type and subscription
            'user_type' => $this->user_type,
            'subscription_status' => $this->subscription_status,
            'subscription_expires_at' => $this->subscription_expires_at?->format('Y-m-d H:i:s'),
            'is_premium_active' => $this->user_type === 'premium' &&
                                   $this->subscription_status === 'active' &&
                                   $this->subscription_expires_at?->isFuture(),

            // Socios information
            'socios_number' => $this->socios_number,
            'socios_verified' => (bool) $this->socios_verified,
            'socios_verified_at' => $this->socios_verified_at?->format('Y-m-d H:i:s'),

            // Loyalty program
            'loyalty_points' => $this->loyalty_points,
            'loyalty_level' => $this->loyalty_level,

            // Preferences
            'preferences' => $this->preferences ?? [],

            // Status
            'is_active' => (bool) $this->is_active,
            'last_login_at' => $this->last_login_at?->format('Y-m-d H:i:s'),

            // Timestamps
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
