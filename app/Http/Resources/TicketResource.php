<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $booking = $this->whenLoaded('booking');
        $event = $this->whenLoaded('booking', fn () => $this->booking?->event);
        $venue = $this->whenLoaded('booking', fn () => $this->booking?->event?->venue);
        $isActive = $this->status === 'issued';

        return [
            'id' => $this->id,
            'qr_code' => $isActive ? $this->qr_code : null,
            'status' => $this->status,
            'issued_at' => optional($this->issued_at)?->toIso8601String(),
            'used_at' => optional($this->used_at)?->toIso8601String(),
            'booking_reference' => $this->whenLoaded('booking', fn () => $booking?->reference),
            'event' => $this->whenLoaded('booking', fn () => [
                'id' => $event?->id,
                'title' => $event?->title,
                'starts_at' => optional($event?->starts_at)?->toIso8601String(),
                'venue' => $venue?->name,
            ]),
            'seat' => new SeatResource($this->whenLoaded('seat')),
            'pdf_url' => $isActive && $this->pdf_path ? Storage::disk('public')->url($this->pdf_path) : null,
            'can_download' => $isActive,
            'can_show_qr' => $isActive,
        ];
    }
}
