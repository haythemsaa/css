<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Get the appropriate reduction value based on user type
        $userType = $request->user()?->user_type ?? 'free';
        $reductionValue = match($userType) {
            'socios' => $this->reduction_value_socios,
            'premium' => $this->reduction_value_premium,
            default => 0,
        };

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'logo' => $this->logo,
            'cover_image' => $this->cover_image,

            // Category
            'category' => new PartnerCategoryResource($this->whenLoaded('category')),

            // Contact info
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,

            // Location
            'address' => $this->address,
            'city' => $this->city,
            'governorate' => $this->governorate,
            'postal_code' => $this->postal_code,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'distance' => $this->when(isset($this->distance), round($this->distance, 2) . ' km'),

            // Opening hours
            'opening_hours' => $this->opening_hours,

            // Reduction info (personalized based on user type)
            'has_discount' => $userType !== 'free',
            'reduction_type' => $this->reduction_type,
            'reduction_value' => $reductionValue,
            'discount_label' => $reductionValue > 0 ? "-{$reductionValue}%" : null,

            // Status
            'status' => $this->status,
            'featured_order' => $this->featured_order,
            'is_featured' => $this->featured_order > 0,

            // Offers
            'offers' => PartnerOfferResource::collection($this->whenLoaded('offers')),
            'offers_count' => $this->whenCounted('offers'),

            // Timestamps
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
