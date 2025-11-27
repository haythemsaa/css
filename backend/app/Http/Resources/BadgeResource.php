<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BadgeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon,
            'category' => $this->category,
            'rarity' => $this->rarity,
            'points_reward' => $this->points_reward,

            // User progress (if user is authenticated)
            'user_progress' => $this->when(isset($this->user_progress), $this->user_progress),
            'is_earned' => $this->when(isset($this->is_earned), $this->is_earned),

            'criteria' => $this->criteria,
            'is_active' => $this->is_active,
        ];
    }
}
