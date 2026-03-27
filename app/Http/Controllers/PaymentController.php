<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePaymentRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\TicketResource;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $payments,
    ) {
    }

    public function store(CreatePaymentRequest $request)
    {
        $result = $this->payments->pay($request->user(), $request->validated());

        return $this->success([
            'booking' => new BookingResource($result['booking']),
            'payment' => new PaymentResource($result['payment']),
            'tickets' => TicketResource::collection($result['tickets']),
        ], 'Payment processed successfully.', 201);
    }
}
