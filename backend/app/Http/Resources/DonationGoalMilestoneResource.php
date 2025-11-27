<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonationGoalMilestoneResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'target_amount' => $this->target_amount,
            'percentage' => $this->percentage,
            'is_achieved' => $this->is_achieved,
            'achieved_at' => $this->achieved_at,
            'reward_badge' => $this->reward_badge,
            'display_order' => $this->display_order,
        ];
    }
}
