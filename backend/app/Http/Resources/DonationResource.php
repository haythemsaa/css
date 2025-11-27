<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => (float) $this->amount,
            'currency' => $this->currency,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'is_anonymous' => $this->is_anonymous,
            'message' => $this->message,

            // Donor (only if not anonymous or if viewing own donation)
            'donor' => $this->when(
                !$this->is_anonymous || $request->user()?->id === $this->user_id,
                new UserResource($this->whenLoaded('user'))
            ),

            // Campaign
            'campaign' => new CampaignResource($this->whenLoaded('campaign')),

            'donated_at' => $this->donated_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
