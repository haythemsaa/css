<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'total_gifts',
        'gifts_distributed',
        'gift_type',
        'gift_value',
        'frequency',
        'eligibility_criteria',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'eligibility_criteria' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the distributions for this campaign
     */
    public function distributions()
    {
        return $this->hasMany(GiftDistribution::class, 'campaign_id');
    }

    /**
     * Check if campaign is currently active
     */
    public function isActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        return $now->between($this->start_date, $this->end_date)
            && $this->gifts_distributed < $this->total_gifts;
    }

    /**
     * Check if user is eligible for this campaign
     */
    public function isUserEligible(User $user): bool
    {
        if (!$this->eligibility_criteria) {
            return true;
        }

        // Check user type eligibility
        if (isset($this->eligibility_criteria['user_types'])) {
            if (!in_array($user->user_type, $this->eligibility_criteria['user_types'])) {
                return false;
            }
        }

        // Check minimum loyalty points
        if (isset($this->eligibility_criteria['min_loyalty_points'])) {
            if ($user->loyalty_points < $this->eligibility_criteria['min_loyalty_points']) {
                return false;
            }
        }

        return true;
    }

    /**
     * Scope for active campaigns
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->whereColumn('gifts_distributed', '<', 'total_gifts');
    }
}
