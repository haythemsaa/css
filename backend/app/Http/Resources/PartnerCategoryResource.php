<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnerCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name_fr' => $this->name_fr,
            'name_ar' => $this->name_ar,
            'slug' => $this->slug,
            'description' => $this->description,
            'icon' => $this->icon,
            'color' => $this->color,
            'display_order' => $this->display_order,
            'partners_count' => $this->when(isset($this->partners_count), $this->partners_count),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
