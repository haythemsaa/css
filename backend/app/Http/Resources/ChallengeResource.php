<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'challenge_type' => $this->challenge_type,
            'category' => $this->category,
            'difficulty' => $this->difficulty,
            'difficulty_level' => $this->difficulty_level,
            'requirements' => $this->requirements,
            'target_value' => $this->target_value,
            'points_reward' => $this->points_reward,
            'additional_rewards' => $this->additional_rewards,
            'icon' => $this->icon,
            'color' => $this->color,
            'max_completions' => $this->max_completions,
            'completion_count' => $this->completion_count,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'is_available' => $this->is_available,
            'time_remaining' => $this->time_remaining,
            'starts_at' => $this->starts_at?->format('Y-m-d H:i:s'),
            'ends_at' => $this->ends_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
