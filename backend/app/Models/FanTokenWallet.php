<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FanTokenWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'lifetime_earned',
        'lifetime_spent',
        'level',
        'experience_points',
        'last_daily_bonus_at',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'lifetime_earned' => 'decimal:2',
        'lifetime_spent' => 'decimal:2',
        'level' => 'integer',
        'experience_points' => 'integer',
        'last_daily_bonus_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(FanTokenTransaction::class, 'user_id', 'user_id');
    }

    // Methods
    public function addTokens(float $amount, string $type, string $description, array $metadata = []): FanTokenTransaction
    {
        $this->increment('balance', $amount);
        $this->increment('lifetime_earned', $amount);

        return FanTokenTransaction::create([
            'user_id' => $this->user_id,
            'transaction_number' => $this->generateTransactionNumber(),
            'type' => $type,
            'amount' => $amount,
            'balance_after' => $this->fresh()->balance,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    public function deductTokens(float $amount, string $type, string $description, array $metadata = []): FanTokenTransaction
    {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient token balance');
        }

        $this->decrement('balance', $amount);
        $this->increment('lifetime_spent', $amount);

        return FanTokenTransaction::create([
            'user_id' => $this->user_id,
            'transaction_number' => $this->generateTransactionNumber(),
            'type' => $type,
            'amount' => -$amount,
            'balance_after' => $this->fresh()->balance,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    public function addExperience(int $xp): void
    {
        $this->increment('experience_points', $xp);
        $this->checkLevelUp();
    }

    private function checkLevelUp(): void
    {
        $requiredXP = $this->getRequiredXPForNextLevel();
        
        if ($this->experience_points >= $requiredXP) {
            $this->increment('level');
            // Award bonus tokens for leveling up
            $bonusTokens = $this->level * 100;
            $this->addTokens($bonusTokens, 'bonus', "Bonus de niveau {$this->level} atteint!");
        }
    }

    private function getRequiredXPForNextLevel(): int
    {
        return $this->level * 1000; // Example: Level 2 needs 2000 XP
    }

    public function canClaimDailyBonus(): bool
    {
        if (!$this->last_daily_bonus_at) {
            return true;
        }
        
        return $this->last_daily_bonus_at->isYesterday() || $this->last_daily_bonus_at->isPast();
    }

    public function claimDailyBonus(): ?FanTokenTransaction
    {
        if (!$this->canClaimDailyBonus()) {
            return null;
        }

        $bonusAmount = 50 + ($this->level * 10); // Increases with level
        $this->update(['last_daily_bonus_at' => now()]);
        
        return $this->addTokens($bonusAmount, 'bonus', 'Bonus quotidien');
    }

    private function generateTransactionNumber(): string
    {
        return 'TKN-' . strtoupper(uniqid());
    }

    // Accessors
    public function getLevelNameAttribute(): string
    {
        return match(true) {
            $this->level >= 50 => 'Légende',
            $this->level >= 40 => 'Champion',
            $this->level >= 30 => 'Elite',
            $this->level >= 20 => 'Expert',
            $this->level >= 10 => 'Passionné',
            $this->level >= 5 => 'Supporter',
            default => 'Débutant',
        };
    }

    public function getProgressToNextLevelAttribute(): float
    {
        $required = $this->getRequiredXPForNextLevel();
        $currentLevelXP = ($this->level - 1) * 1000;
        $progress = $this->experience_points - $currentLevelXP;
        $needed = $required - $currentLevelXP;
        
        return ($progress / $needed) * 100;
    }
}
