<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DonationGoal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'full_details',
        'category',
        'target_amount',
        'current_amount',
        'donors_count',
        'min_donation',
        'priority',
        'start_date',
        'end_date',
        'status',
        'is_featured',
        'featured_image',
        'gallery_images',
        'milestone_updates',
        'impact_metrics',
        'thank_you_message',
        'show_donors',
        'allow_anonymous',
        'rewards',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'min_donation' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_featured' => 'boolean',
        'show_donors' => 'boolean',
        'allow_anonymous' => 'boolean',
        'gallery_images' => 'array',
        'milestone_updates' => 'array',
        'rewards' => 'array',
    ];

    // Relations
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'goal_id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(DonationGoalMilestone::class, 'goal_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>', now());
            });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    // Accessors
    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_amount == 0) {
            return 0;
        }

        return min(round(($this->current_amount / $this->target_amount) * 100, 2), 100);
    }

    public function getRemainingAmountAttribute(): float
    {
        return max($this->target_amount - $this->current_amount, 0);
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->current_amount >= $this->target_amount;
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->end_date) {
            return null;
        }

        $days = $this->end_date->diffInDays(now(), false);
        return $days < 0 ? abs($days) : 0;
    }

    // Methods
    public function addDonation(float $amount, User $user): void
    {
        $this->increment('current_amount', $amount);
        $this->increment('donors_count');

        // Check and update milestones
        $this->checkMilestones();

        // Mark as completed if target reached
        if ($this->is_completed && $this->status === 'active') {
            $this->update(['status' => 'completed']);
        }
    }

    public function checkMilestones(): void
    {
        $percentage = $this->progress_percentage;

        $this->milestones()
            ->where('is_achieved', false)
            ->where('percentage', '<=', $percentage)
            ->update([
                'is_achieved' => true,
                'achieved_at' => now(),
            ]);
    }
}
