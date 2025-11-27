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
            'position' => $this->position,
            'jersey_number' => $this->jersey_number,
            'photo' => $this->photo,
            'nationality' => $this->nationality,
            'date_of_birth' => $this->date_of_birth?->toDateString(),
            'height' => $this->height,
            'weight' => $this->weight,

            // Stats
            'goals' => $this->goals,
            'assists' => $this->assists,
            'appearances' => $this->appearances,

            'bio' => $this->bio,
            'is_active' => $this->is_active,
        ];
    }
}
