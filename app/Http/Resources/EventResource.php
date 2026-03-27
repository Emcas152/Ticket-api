<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'image_url' => $this->image_url,
            'status' => $this->status,
            'starts_at' => optional($this->starts_at)?->toIso8601String(),
            'ends_at' => optional($this->ends_at)?->toIso8601String(),
            'published_at' => optional($this->published_at)?->toIso8601String(),
            'venue' => new VenueResource($this->whenLoaded('venue')),
            'organizer' => new UserResource($this->whenLoaded('organizer')),
        ];
    }
}
