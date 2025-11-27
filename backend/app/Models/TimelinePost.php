<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimelinePost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'content', 'post_type', 'media', 'entity_type',
        'entity_id', 'likes_count', 'comments_count', 'shares_count',
        'is_pinned',
    ];

    protected $casts = [
        'media' => 'array',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
        'shares_count' => 'integer',
        'is_pinned' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(TimelinePostLike::class, 'post_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TimelinePostComment::class, 'post_id');
    }
}
