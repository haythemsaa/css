<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuctionBidResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'auction_product_id' => $this->auction_product_id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->full_name,
            ],
            'bid_amount' => $this->bid_amount,
            'is_auto_bid' => $this->is_auto_bid,
            'max_auto_bid' => $this->when($this->user_id === $request->user()?->id, $this->max_auto_bid),
            'is_winning' => $this->is_winning,
            'was_outbid' => $this->was_outbid,
            'outbid_at' => $this->outbid_at,
            'created_at' => $this->created_at,
        ];
    }
}
