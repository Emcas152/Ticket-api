<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmBookingRequest;
use App\Http\Resources\BookingResource;
use App\Services\BookingService;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookings,
    ) {
    }

    public function store(ConfirmBookingRequest $request)
    {
        $booking = $this->bookings->confirm($request->user(), (int) $request->validated('booking_id'));

        return $this->success(new BookingResource($booking), 'Booking confirmed successfully.');
    }
}
