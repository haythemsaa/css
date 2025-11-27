<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'logo' => $this->logo,
            'address' => $this->address,
            'city' => $this->city,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,

            // Location
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'distance' => $this->when(isset($this->distance), round($this->distance, 2)),

            // Discounts
            'discount_premium' => $this->discount_premium,
            'discount_socios' => $this->discount_socios,
            'user_discount' => $this->when($user, function () use ($user) {
                return $this->getDiscountForUser($user);
            }),

            // Category
            'category' => new PartnerCategoryResource($this->whenLoaded('category')),

            // Offers
            'active_offers_count' => $this->whenLoaded('offers', function () {
                return $this->offers->where('is_active', true)->count();
            }),

            // Stats
            'rating' => $this->rating,
            'reviews_count' => $this->reviews_count,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,

            // User interaction
            'is_favorite' => $this->when($user, function () use ($user) {
                return \App\Models\PartnerFavorite::where('user_id', $user->id)
                    ->where('partner_id', $this->id)
                    ->exists();
            }),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
