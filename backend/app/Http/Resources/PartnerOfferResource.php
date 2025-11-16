<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnerOfferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $now = now();
        $isValid = $this->valid_from <= $now && $this->valid_until >= $now;
        $isExpiringSoon = $this->valid_until->diffInDays($now) <= 7;
        $stockPercentage = $this->stock_available > 0
            ? (($this->stock_available - $this->stock_used) / $this->stock_available) * 100
            : 0;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'image' => $this->image,

            // Partner
            'partner' => new PartnerResource($this->whenLoaded('partner')),

            // Offer details
            'offer_type' => $this->offer_type,
            'offer_type_label' => match($this->offer_type) {
                'standard' => 'Offre Standard',
                'flash' => 'Offre Flash',
                'seasonal' => 'Offre Saisonnière',
                'exclusive' => 'Offre Exclusive',
                default => 'Offre',
            },

            // Reduction
            'reduction_type' => $this->reduction_type,
            'reduction_value' => $this->reduction_value,
            'discount_label' => $this->reduction_type === 'percentage'
                ? "-{$this->reduction_value}%"
                : "-{$this->reduction_value} TND",

            // Validity
            'valid_from' => $this->valid_from->format('Y-m-d H:i:s'),
            'valid_until' => $this->valid_until->format('Y-m-d H:i:s'),
            'is_valid' => $isValid,
            'is_expiring_soon' => $isExpiringSoon,
            'days_remaining' => $this->valid_until->diffInDays($now),

            // Stock
            'stock_available' => $this->stock_available,
            'stock_used' => $this->stock_used,
            'stock_remaining' => $this->stock_available - $this->stock_used,
            'stock_percentage' => round($stockPercentage, 1),
            'is_low_stock' => $stockPercentage < 20 && $stockPercentage > 0,
            'is_out_of_stock' => $stockPercentage <= 0,

            // Display
            'status' => $this->status,
            'is_featured' => (bool) $this->is_featured,
            'display_order' => $this->display_order,

            // Conditions
            'conditions' => $this->conditions,
            'usage_limit_per_user' => $this->usage_limit_per_user,

            // Timestamps
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
