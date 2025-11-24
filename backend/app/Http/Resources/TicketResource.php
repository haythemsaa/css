<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'match_id' => $this->match_id,
            'match' => [
                'id' => $this->match->id,
                'home_team' => $this->match->home_team,
                'away_team' => $this->match->away_team,
                'match_date' => $this->match->match_date->format('Y-m-d H:i:s'),
                'venue' => $this->match->venue,
            ],
            'category' => $this->category,
            'section' => $this->section,
            'price' => $this->price,
            'total_quantity' => $this->total_quantity,
            'available_quantity' => $this->available_quantity,
            'sold_quantity' => $this->sold_quantity,
            'occupancy_rate' => $this->occupancy_rate,
            'is_available' => $this->is_available,
            'is_sale_active' => $this->is_sale_active,
            'sale_starts_at' => $this->sale_starts_at?->format('Y-m-d H:i:s'),
            'sale_ends_at' => $this->sale_ends_at?->format('Y-m-d H:i:s'),
            'benefits' => $this->benefits,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
