<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PollResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'options' => $this->options,

            // Status
            'is_active' => $this->isActive(),
            'has_ended' => $this->hasEnded(),

            // User interaction
            'has_voted' => $this->when(isset($this->has_voted), $this->has_voted),
            'can_vote' => $this->when(isset($this->can_vote), $this->can_vote),
            'user_vote' => $this->when(isset($this->user_vote), $this->user_vote),

            // Settings
            'allow_multiple' => $this->allow_multiple,
            'show_results_before_vote' => $this->show_results_before_vote,
            'min_user_type' => $this->min_user_type,

            // Stats
            'total_votes' => $this->total_votes,

            // Dates
            'starts_at' => $this->starts_at?->toIso8601String(),
            'ends_at' => $this->ends_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
