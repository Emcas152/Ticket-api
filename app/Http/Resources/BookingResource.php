<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'status' => $this->status,
            'total' => (float) $this->total,
            'reserved_until' => optional($this->reserved_until)?->toIso8601String(),
            'confirmed_at' => optional($this->confirmed_at)?->toIso8601String(),
            'event' => new EventResource($this->whenLoaded('event')),
            'seats' => SeatResource::collection($this->whenLoaded('seats')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'tickets' => TicketResource::collection($this->whenLoaded('tickets')),
        ];
    }
}
