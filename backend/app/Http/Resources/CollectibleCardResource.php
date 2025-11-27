<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CollectibleCardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'card_type' => $this->card_type,
            'rarity' => $this->rarity,
            'season' => $this->season,
            'image_url' => $this->image_url,
            'stats' => $this->stats,

            // Player (if applicable)
            'player' => new PlayerResource($this->whenLoaded('player')),

            // Supply
            'total_supply' => $this->total_supply,
            'circulation' => $this->when(isset($this->circulation), $this->circulation),
            'estimated_value' => $this->estimated_value,

            'is_tradeable' => $this->is_tradeable,
            'is_active' => $this->is_active,
            'release_date' => $this->release_date?->toDateString(),
        ];
    }
}
