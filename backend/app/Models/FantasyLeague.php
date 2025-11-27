<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class FantasyLeague extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'creator_id',
        'type',
        'max_members',
        'budget',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'max_members' => 'integer',
        'budget' => 'integer',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($league) {
            if (!$league->code) {
                $league->code = strtoupper(Str::random(8));
            }
        });
    }

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(FantasyLeagueMember::class, 'league_id');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(FantasyTeam::class, 'league_id');
    }

    // Methods
    public function addMember(User $user): bool
    {
        if ($this->members()->count() >= $this->max_members) {
            return false;
        }

        if ($this->members()->where('user_id', $user->id)->exists()) {
            return false;
        }

        $this->members()->create([
            'user_id' => $user->id,
            'joined_at' => now(),
        ]);

        return true;
    }
}
