<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'home_team_id' => $this->home_team_id,
            'away_team_id' => $this->away_team_id,
            'competition' => $this->competition,
            'venue' => $this->venue,

            // Score
            'home_score' => $this->home_score,
            'away_score' => $this->away_score,
            'status' => $this->status,

            // Teams (when loaded)
            'home_team' => new TeamResource($this->whenLoaded('homeTeam')),
            'away_team' => new TeamResource($this->whenLoaded('awayTeam')),

            // Dates
            'match_date' => $this->match_date?->toIso8601String(),
            'kickoff_time' => $this->kickoff_time,

            // Streaming
            'stream_url' => $this->when(
                $request->user()?->hasActiveSubscription(),
                $this->stream_url
            ),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
