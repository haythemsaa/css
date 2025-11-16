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
            'type' => $this->type,
            'type_label' => match($this->type) {
                'article' => 'Article',
                'video' => 'Vidéo',
                'gallery' => 'Galerie',
                'podcast' => 'Podcast',
                default => 'Contenu',
            },

            // Premium status
            'is_premium' => (bool) $this->is_premium,
            'is_featured' => (bool) $this->is_featured,

            // Author
            'author' => $this->when($this->relationLoaded('author'), function () {
                return [
                    'id' => $this->author->id,
                    'name' => $this->author->name,
                    'photo' => $this->author->photo,
                ];
            }),

            // Video details (if type is video)
            'video' => $this->when(
                $this->type === 'video' && $this->relationLoaded('video'),
                new VideoResource($this->video)
            ),

            // Engagement
            'views_count' => $this->views_count,
            'likes_count' => $this->likes_count,
            'is_liked' => $this->when(
                $request->user(),
                fn() => $this->likes()->where('user_id', $request->user()->id)->exists()
            ),

            // Publishing
            'published_at' => $this->published_at?->format('Y-m-d H:i:s'),
            'published_at_human' => $this->published_at?->diffForHumans(),

            // Timestamps
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
