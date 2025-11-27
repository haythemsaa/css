<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ForumTopicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,

            // Status
            'is_pinned' => $this->is_pinned,
            'is_locked' => $this->is_locked,
            'is_active' => $this->is_active,

            // Author
            'author' => new UserResource($this->whenLoaded('user')),

            // Category
            'category' => new ForumCategoryResource($this->whenLoaded('category')),

            // Stats
            'views_count' => $this->views_count,
            'likes_count' => $this->likes_count,
            'replies_count' => $this->when(isset($this->replies_count), $this->replies_count),

            // Latest reply
            'latest_reply' => new ForumReplyResource($this->whenLoaded('latestReply')),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
