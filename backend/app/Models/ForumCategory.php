<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'order',
        'is_active',
        'min_user_type',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all topics in this category
     */
    public function topics()
    {
        return $this->hasMany(ForumTopic::class, 'category_id');
    }

    /**
     * Get active topics count
     */
    public function getActiveTopicsCountAttribute(): int
    {
        return $this->topics()->where('is_active', true)->count();
    }

    /**
     * Get total replies in this category
     */
    public function getTotalRepliesAttribute(): int
    {
        return ForumReply::whereIn('topic_id', $this->topics()->pluck('id'))->count();
    }

    /**
     * Get latest topic
     */
    public function latestTopic()
    {
        return $this->hasOne(ForumTopic::class, 'category_id')->latestOfMany();
    }

    /**
     * Check if user can access this category
     */
    public function canBeAccessedBy(User $user): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (!$this->min_user_type) {
            return true;
        }

        $typeHierarchy = ['free' => 0, 'premium' => 1, 'socios' => 2, 'admin' => 3];
        $userLevel = $typeHierarchy[$user->user_type] ?? 0;
        $requiredLevel = $typeHierarchy[$this->min_user_type] ?? 0;

        return $userLevel >= $requiredLevel;
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}
