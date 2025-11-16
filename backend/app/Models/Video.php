<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'content_id',
        'video_url',
        'video_path',
        'duration',
        'resolution',
        'file_size',
        'encoding_status',
    ];

    protected $casts = [
        'duration' => 'integer',
    ];

    // Relations
    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }
}
