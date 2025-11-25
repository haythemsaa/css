<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimelinePostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->avatar,
                'user_type' => $this->user->user_type,
                'socios_verified' => $this->user->socios_verified,
            ],
            'content' => $this->content,
            'post_type' => $this->post_type,
            'media' => $this->media,
            'visibility' => $this->visibility,
            'likes_count' => $this->likes_count,
            'comments_count' => $this->comments_count,
            'shares_count' => $this->shares_count,
            'is_pinned' => $this->is_pinned,
            'liked_by_user' => $this->whenLoaded('likes', function () use ($request) {
                return $this->likes->contains('user_id', $request->user()?->id);
            }),
            'comments' => TimelinePostCommentResource::collection($this->whenLoaded('comments')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
