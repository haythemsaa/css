<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'type',
        'category_id',
        'author_id',
        'featured_image',
        'video_url',
        'video_id',
        'video_duration',
        'gallery_images',
        'podcast_url',
        'podcast_duration',
        'access_level',
        'is_featured',
        'is_trending',
        'status',
        'published_at',
        'scheduled_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'views_count',
        'likes_count',
        'comments_count',
        'shares_count',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'meta_keywords' => 'array',
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_trending' => 'boolean',
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(ContentCategory::class, 'category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ContentTag::class, 'content_tag');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeTrending($query)
    {
        return $query->where('is_trending', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByAccessLevel($query, $accessLevel)
    {
        return $query->where('access_level', $accessLevel);
    }

    public function scopeFree($query)
    {
        return $query->where('access_level', 'free');
    }

    public function scopePremium($query)
    {
        return $query->whereIn('access_level', ['premium', 'socios']);
    }

    // Methods
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function canBeAccessedBy(User $user = null): bool
    {
        if ($this->access_level === 'free') {
            return true;
        }

        if (!$user) {
            return false;
        }

        if ($this->access_level === 'premium') {
            return in_array($user->user_type, ['premium', 'socios']);
        }

        if ($this->access_level === 'socios') {
            return $user->user_type === 'socios' && $user->socios_verified;
        }

        return false;
    }
}
