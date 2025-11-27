<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaderboardResource extends JsonResource
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
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->avatar,
                'level' => $this->user->level ?? 'bronze',
            ],
            'leaderboard_type' => $this->leaderboard_type,
            'score' => $this->score,
            'rank' => $this->rank,
            'previous_rank' => $this->previous_rank,
            'rank_change' => $this->rank_change,
            'rank_trend' => $this->rank_trend,
            'period' => $this->period,
            'period_start' => $this->period_start?->format('Y-m-d'),
            'period_end' => $this->period_end?->format('Y-m-d'),
            'metadata' => $this->metadata,
            'last_updated_at' => $this->last_updated_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
