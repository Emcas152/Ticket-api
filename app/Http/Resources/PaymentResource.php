<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'provider' => $this->provider,
            'provider_reference' => $this->provider_reference,
            'amount' => (float) $this->amount,
            'status' => $this->status,
            'paid_at' => optional($this->paid_at)?->toIso8601String(),
        ];
    }
}
