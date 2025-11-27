<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'description', 'event_type', 'venue', 'address',
        'latitude', 'longitude', 'start_datetime', 'end_datetime',
        'max_attendees', 'registered_count', 'requires_registration',
        'is_free', 'price', 'images', 'status', 'is_featured',
        'registration_requirements',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'max_attendees' => 'integer',
        'registered_count' => 'integer',
        'requires_registration' => 'boolean',
        'is_free' => 'boolean',
        'price' => 'decimal:2',
        'images' => 'array',
        'is_featured' => 'boolean',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }
}
