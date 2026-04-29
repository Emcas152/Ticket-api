<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VenueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'coordinates' => [
                'lat' => $this->lat,
                'lng' => $this->lng,
            ],
            'seat_map_config' => $this->seat_map_config,
            'sections' => $this->whenLoaded('sections', fn () => $this->sections->map(fn ($section) => [
                'id' => $section->id,
                'name' => $section->name,
                'code' => $section->code,
                'map_config' => $section->map_config,
                'seats' => $section->relationLoaded('seats')
                    ? SeatResource::collection($section->seats)->resolve()
                    : [],
            ])->values()->all()),
        ];
    }
}
