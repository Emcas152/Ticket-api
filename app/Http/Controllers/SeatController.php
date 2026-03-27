<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReserveSeatsRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\SeatResource;
use App\Models\Event;
use App\Services\SeatService;

class SeatController extends Controller
{
    public function __construct(
        private readonly SeatService $seatService,
    ) {
    }

    public function index(Event $event)
    {
        return $this->success(SeatResource::collection($this->seatService->getEventSeats($event)));
    }

    public function reserve(ReserveSeatsRequest $request)
    {
        $event = Event::query()->findOrFail($request->integer('event_id'));
        $booking = $this->seatService->reserve($event, $request->user(), $request->validated('seat_ids'));

        return $this->success(new BookingResource($booking), 'Seats reserved successfully.', 201);
    }
}
