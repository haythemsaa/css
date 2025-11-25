<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonationGoalMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'goal_id',
        'title',
        'description',
        'target_amount',
        'percentage',
        'is_achieved',
        'achieved_at',
        'reward_badge',
        'display_order',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'is_achieved' => 'boolean',
        'achieved_at' => 'datetime',
    ];

    // Relations
    public function goal(): BelongsTo
    {
        return $this->belongsTo(DonationGoal::class, 'goal_id');
    }
}
