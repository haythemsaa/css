<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_type',
        'entity_type',
        'entity_id',
        'metadata',
        'ip_address',
        'user_agent',
        'device_type',
        'platform',
        'duration',
    ];

    protected $casts = [
        'metadata' => 'array',
        'duration' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Track activity helper
    public static function track(
        int $userId,
        string $activityType,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $metadata = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'activity_type' => $activityType,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'device_type' => self::detectDeviceType(),
            'platform' => self::detectPlatform(),
        ]);
    }

    private static function detectDeviceType(): string
    {
        $userAgent = request()->userAgent();

        if (preg_match('/mobile/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/tablet|ipad/i', $userAgent)) {
            return 'tablet';
        }

        return 'desktop';
    }

    private static function detectPlatform(): string
    {
        $userAgent = request()->userAgent();

        if (preg_match('/android/i', $userAgent)) {
            return 'android';
        } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
            return 'ios';
        }

        return 'web';
    }
}
