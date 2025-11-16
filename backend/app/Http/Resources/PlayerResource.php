<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'jersey_number' => $this->jersey_number,
            'position' => $this->position,
            'position_label' => match($this->position) {
                'goalkeeper' => 'Gardien',
                'defender' => 'Défenseur',
                'midfielder' => 'Milieu',
                'forward' => 'Attaquant',
                default => $this->position,
            },

            // Details
            'photo' => $this->photo,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'age' => $this->birth_date?->age,
            'nationality' => $this->nationality,
            'height' => $this->height,
            'weight' => $this->weight,

            // Bio
            'bio' => $this->bio,

            // Stats
            'goals' => $this->goals,
            'assists' => $this->assists,
            'yellow_cards' => $this->yellow_cards,
            'red_cards' => $this->red_cards,
            'matches_played' => $this->matches_played,

            // Contract
            'contract_start' => $this->contract_start?->format('Y-m-d'),
            'contract_end' => $this->contract_end?->format('Y-m-d'),

            // Status
            'is_active' => (bool) $this->is_active,

            // Timestamps
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
