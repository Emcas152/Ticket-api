<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'qr_code' => $this->qr_code,
            'status' => $this->status,
            'issued_at' => optional($this->issued_at)?->toIso8601String(),
            'seat' => new SeatResource($this->whenLoaded('seat')),
            'pdf_url' => $this->pdf_path ? Storage::disk('public')->url($this->pdf_path) : null,
        ];
    }
}
