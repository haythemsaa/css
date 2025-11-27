<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionTierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'monthly_price' => $this->monthly_price,
            'yearly_price' => $this->yearly_price,
            'yearly_savings' => $this->monthly_price > 0 ? round(($this->monthly_price * 12) - $this->yearly_price, 2) : 0,
            'features' => $this->features,
            'benefits' => $this->benefits,
            'badge' => [
                'color' => $this->badge_color,
                'icon' => $this->badge_icon,
            ],
            'points_multiplier' => $this->points_multiplier,
            'discount_percentage' => $this->discount_percentage,
            'priority_support' => $this->priority_support,
            'is_active' => $this->is_active,
            'display_order' => $this->display_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
