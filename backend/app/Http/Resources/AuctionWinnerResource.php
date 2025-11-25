<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuctionWinnerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'auction_product' => new AuctionProductResource($this->whenLoaded('auctionProduct')),
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->full_name,
                'email' => $this->user->email,
            ],
            'final_price' => $this->final_price,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->paymentMethod ? [
                'id' => $this->paymentMethod->id,
                'name' => $this->paymentMethod->name,
                'code' => $this->paymentMethod->code,
            ] : null,
            'transaction_id' => $this->transaction_id,
            'paid_at' => $this->paid_at,
            'delivery_status' => $this->delivery_status,
            'tracking_number' => $this->tracking_number,
            'delivered_at' => $this->delivered_at,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
