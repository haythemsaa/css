<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountCodeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'min_purchase_amount' => $this->min_purchase_amount,
            'max_discount_amount' => $this->max_discount_amount,
            'usage_limit' => $this->usage_limit,
            'usage_limit_per_user' => $this->usage_limit_per_user,
            'usage_count' => $this->usage_count,
            'remaining_uses' => $this->usage_limit ? ($this->usage_limit - $this->usage_count) : null,
            'valid_from' => $this->valid_from,
            'valid_until' => $this->valid_until,
            'is_active' => $this->is_active,
            'is_valid' => $this->isValid(),
            'applicable_categories' => $this->applicable_categories,
            'applicable_products' => $this->applicable_products,
            'user_type' => $this->user_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
