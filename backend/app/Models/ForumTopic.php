<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ForumTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'user_id',
        'title',
        'slug',
        'content',
        'is_pinned',
        'is_locked',
        'is_active',
        'views_count',
        'likes_count',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($topic) {
            if (!$topic->slug) {
                $topic->slug = Str::slug($topic->title);
            }
        });
    }

    /**
     * Get the category this topic belongs to
     */
    public function category()
    {
        return $this->belongsTo(ForumCategory::class);
    }

    /**
     * Get the user who created this topic
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all replies to this topic
     */
    public function replies()
    {
        return $this->hasMany(ForumReply::class, 'topic_id');
    }

    /**
     * Increment views count
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Toggle like for a user
     */
    public function toggleLike(User $user): void
    {
        // This would normally use a pivot table, simplified here
        $this->increment('likes_count');
    }

    /**
     * Lock/Unlock the topic
     */
    public function toggleLock(): void
    {
        $this->is_locked = !$this->is_locked;
        $this->save();
    }

    /**
     * Pin/Unpin the topic
     */
    public function togglePin(): void
    {
        $this->is_pinned = !$this->is_pinned;
        $this->save();
    }

    /**
     * Get latest reply
     */
    public function latestReply()
    {
        return $this->hasOne(ForumReply::class, 'topic_id')->latestOfMany();
    }

    /**
     * Scope for active topics
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for pinned topics
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Scope for unlocked topics
     */
    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }

    /**
     * Scope for popular topics (by replies and views)
     */
    public function scopePopular($query)
    {
        return $query->withCount('replies')
            ->orderByDesc('views_count')
            ->orderByDesc('replies_count');
    }
}
