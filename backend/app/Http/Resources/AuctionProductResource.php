<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuctionProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'images' => $this->images,
            'category' => $this->category,
            'starting_price' => $this->starting_price,
            'reserve_price' => $this->reserve_price,
            'current_bid' => $this->current_bid,
            'buy_now_price' => $this->buy_now_price,
            'bid_increment' => $this->bid_increment,
            'total_bids' => $this->total_bids,
            'current_winner' => $this->currentWinner ? [
                'id' => $this->currentWinner->id,
                'name' => $this->currentWinner->full_name,
            ] : null,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'time_remaining' => $this->time_remaining,
            'is_active' => $this->is_active,
            'reserve_met' => $this->reserve_met,
            'is_featured' => $this->is_featured,
            'auto_extend' => $this->auto_extend,
            'auto_extend_minutes' => $this->auto_extend_minutes,
            'status' => $this->status,
            'terms_conditions' => $this->terms_conditions,
            'metadata' => $this->metadata,
            'bids' => AuctionBidResource::collection($this->whenLoaded('bids')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
