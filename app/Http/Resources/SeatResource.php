<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'section_id' => $this->section_id,
            'section' => $this->section?->name,
            'row' => $this->row_label,
            'number' => $this->seat_number,
            'label' => $this->label,
            'price' => (float) $this->price,
            'state' => $this->resource->state ?? 'available',
        ];
    }
}
