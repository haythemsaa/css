<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketPurchaseResource extends JsonResource
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
            'ticket_number' => $this->ticket_number,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'ticket' => [
                'id' => $this->ticket->id,
                'category' => $this->ticket->category,
                'section' => $this->ticket->section,
            ],
            'match' => [
                'id' => $this->match->id,
                'home_team' => $this->match->home_team,
                'away_team' => $this->match->away_team,
                'match_date' => $this->match->match_date->format('Y-m-d H:i:s'),
                'venue' => $this->match->venue,
            ],
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'qr_code' => $this->qr_code,
            'attendee_info' => $this->attendee_info,
            'used_at' => $this->used_at?->format('Y-m-d H:i:s'),
            'paid_at' => $this->paid_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
