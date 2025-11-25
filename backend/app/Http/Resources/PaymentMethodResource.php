<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'description' => $this->description,
            'logo_url' => $this->logo_url,
            'provider' => $this->provider,
            'is_active' => $this->is_active,
            'is_default' => $this->is_default,
            'supported_currencies' => $this->supported_currencies,
            'min_amount' => $this->min_amount,
            'max_amount' => $this->max_amount,
            'transaction_fee' => $this->transaction_fee,
            'transaction_fee_percentage' => $this->transaction_fee_percentage,
            'processing_time' => $this->processing_time,
            'supports_refund' => $this->supports_refund,
            'instructions' => $this->instructions,
            'display_order' => $this->display_order,
            'available_for' => $this->available_for,
        ];
    }
}
