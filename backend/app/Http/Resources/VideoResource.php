<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'thumbnail_url' => $this->thumbnail_url,
            'video_url' => $this->video_url,
            'duration' => $this->duration,
            'duration_formatted' => $this->formatDuration($this->duration),
            'quality' => $this->quality,
            'quality_label' => match($this->quality) {
                'sd' => 'SD (480p)',
                'hd' => 'HD (720p)',
                'fullhd' => 'Full HD (1080p)',
                '4k' => '4K (2160p)',
                default => 'Standard',
            },
        ];
    }

    private function formatDuration(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
        }

        return sprintf('%d:%02d', $minutes, $secs);
    }
}
