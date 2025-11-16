<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FootballMatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isUpcoming = $this->match_date > now();
        $isLive = $this->status === 'live';
        $isFinished = in_array($this->status, ['finished', 'completed']);

        return [
            'id' => $this->id,

            // Match info
            'opponent' => $this->opponent,
            'opponent_logo' => $this->opponent_logo,
            'match_date' => $this->match_date->format('Y-m-d H:i:s'),
            'match_date_human' => $this->match_date->diffForHumans(),

            // Venue
            'stadium' => $this->stadium,
            'home_away' => $this->home_away,
            'is_home' => $this->home_away === 'home',

            // Competition
            'competition' => $this->competition,
            'competition_label' => match($this->competition) {
                'ligue1' => 'Ligue 1 Professionnelle',
                'cup' => 'Coupe de Tunisie',
                'champions_league' => 'Ligue des Champions CAF',
                'confederation_cup' => 'Coupe de la Confédération CAF',
                'super_cup' => 'Super Coupe',
                'friendly' => 'Match Amical',
                default => $this->competition,
            },
            'season' => $this->season,
            'matchday' => $this->matchday,

            // Score
            'css_score' => $this->css_score,
            'opponent_score' => $this->opponent_score,
            'score_display' => $isFinished || $isLive
                ? "{$this->css_score} - {$this->opponent_score}"
                : 'vs',

            // Match result
            'result' => $this->when($isFinished, $this->getMatchResult()),

            // Status
            'status' => $this->status,
            'status_label' => match($this->status) {
                'scheduled' => 'Programmé',
                'live' => 'En Direct',
                'finished' => 'Terminé',
                'completed' => 'Terminé',
                'postponed' => 'Reporté',
                'cancelled' => 'Annulé',
                default => $this->status,
            },
            'is_upcoming' => $isUpcoming,
            'is_live' => $isLive,
            'is_finished' => $isFinished,

            // Stats
            'attendance' => $this->attendance,

            // Timestamps
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    private function getMatchResult(): ?string
    {
        if ($this->css_score === null || $this->opponent_score === null) {
            return null;
        }

        if ($this->css_score > $this->opponent_score) {
            return 'win';
        } elseif ($this->css_score < $this->opponent_score) {
            return 'loss';
        }

        return 'draw';
    }
}
