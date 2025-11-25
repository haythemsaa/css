<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonationGoalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'full_details' => $this->full_details,
            'category' => $this->category,
            'target_amount' => $this->target_amount,
            'current_amount' => $this->current_amount,
            'remaining_amount' => $this->remaining_amount,
            'progress_percentage' => $this->progress_percentage,
            'donors_count' => $this->donors_count,
            'min_donation' => $this->min_donation,
            'priority' => $this->priority,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'days_remaining' => $this->days_remaining,
            'status' => $this->status,
            'is_completed' => $this->is_completed,
            'is_featured' => $this->is_featured,
            'featured_image' => $this->featured_image,
            'gallery_images' => $this->gallery_images,
            'milestone_updates' => $this->milestone_updates,
            'impact_metrics' => $this->impact_metrics,
            'thank_you_message' => $this->thank_you_message,
            'show_donors' => $this->show_donors,
            'allow_anonymous' => $this->allow_anonymous,
            'rewards' => $this->rewards,
            'milestones' => DonationGoalMilestoneResource::collection($this->whenLoaded('milestones')),
            'recent_donations' => $this->when($this->show_donors, function () {
                return $this->donations()
                    ->with('user')
                    ->where('is_anonymous', false)
                    ->latest()
                    ->limit(10)
                    ->get()
                    ->map(function ($donation) {
                        return [
                            'donor_name' => $donation->user->full_name,
                            'amount' => $donation->amount,
                            'message' => $donation->donor_message,
                            'created_at' => $donation->created_at,
                        ];
                    });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
