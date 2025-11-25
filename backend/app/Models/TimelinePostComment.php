<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimelinePostComment extends Model
{
    protected $fillable = ['post_id', 'user_id', 'comment', 'parent_id', 'likes_count'];

    protected $casts = ['likes_count' => 'integer'];

    public function post(): BelongsTo
    {
        return $this->belongsTo(TimelinePost::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
