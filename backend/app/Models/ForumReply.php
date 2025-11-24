<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'user_id',
        'content',
        'parent_id',
        'likes_count',
        'is_edited',
    ];

    protected $casts = [
        'is_edited' => 'boolean',
    ];

    /**
     * Get the topic this reply belongs to
     */
    public function topic()
    {
        return $this->belongsTo(ForumTopic::class);
    }

    /**
     * Get the user who posted this reply
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent reply (for nested replies)
     */
    public function parent()
    {
        return $this->belongsTo(ForumReply::class, 'parent_id');
    }

    /**
     * Get child replies
     */
    public function children()
    {
        return $this->hasMany(ForumReply::class, 'parent_id');
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
     * Mark reply as edited
     */
    public function markAsEdited(): void
    {
        $this->is_edited = true;
        $this->save();
    }

    /**
     * Check if user can edit this reply
     */
    public function canBeEditedBy(User $user): bool
    {
        return $this->user_id === $user->id || $user->user_type === 'admin';
    }

    /**
     * Check if user can delete this reply
     */
    public function canBeDeletedBy(User $user): bool
    {
        return $this->user_id === $user->id || $user->user_type === 'admin';
    }

    /**
     * Scope for top-level replies
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope for replies to a specific topic
     */
    public function scopeForTopic($query, $topicId)
    {
        return $query->where('topic_id', $topicId);
    }
}
