<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventRegistrationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'event' => new EventResource($this->whenLoaded('event')),
            'user_id' => $this->user_id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'status' => $this->status,
            'qr_code' => $this->qr_code,
            'checked_in_at' => $this->checked_in_at,
            'number_of_guests' => $this->number_of_guests,
            'special_requirements' => $this->special_requirements,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
