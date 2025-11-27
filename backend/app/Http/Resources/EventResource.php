<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'event_type' => $this->event_type,
            'venue' => $this->venue,
            'address' => $this->address,
            'location' => [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ],
            'start_datetime' => $this->start_datetime,
            'end_datetime' => $this->end_datetime,
            'max_attendees' => $this->max_attendees,
            'registered_count' => $this->registered_count,
            'available_spots' => $this->max_attendees ? ($this->max_attendees - $this->registered_count) : null,
            'is_full' => $this->max_attendees && $this->registered_count >= $this->max_attendees,
            'requires_registration' => $this->requires_registration,
            'is_free' => $this->is_free,
            'price' => $this->price,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'featured_image' => $this->featured_image,
            'gallery_images' => $this->gallery_images,
            'organizer_name' => $this->organizer_name,
            'organizer_contact' => $this->organizer_contact,
            'tags' => $this->tags,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
