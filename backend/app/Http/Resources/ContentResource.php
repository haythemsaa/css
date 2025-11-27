<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'type' => $this->type,
            'status' => $this->status,
            'access_level' => $this->access_level,

            // Media
            'featured_image' => $this->featured_image,
            'video_url' => $this->video_url,
            'audio_url' => $this->audio_url,
            'duration' => $this->duration,

            // Relations
            'category' => new ContentCategoryResource($this->whenLoaded('category')),
            'tags' => ContentTagResource::collection($this->whenLoaded('tags')),
            'author' => new UserResource($this->whenLoaded('author')),

            // Stats
            'views_count' => $this->views_count,
            'likes_count' => $this->likes_count,
            'shares_count' => $this->shares_count,

            // Dates
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
