<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'goal_amount' => (float) $this->goal_amount,
            'current_amount' => (float) $this->current_amount,
            'currency' => $this->currency,
            'progress_percentage' => $this->current_amount > 0
                ? round(($this->current_amount / $this->goal_amount) * 100, 2)
                : 0,

            'image' => $this->image,
            'status' => $this->status,

            // Stats
            'donors_count' => $this->whenLoaded('donations', function () {
                return $this->donations->unique('user_id')->count();
            }),

            'starts_at' => $this->starts_at?->toIso8601String(),
            'ends_at' => $this->ends_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
