<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Seat;
use App\Models\Ticket;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SeatRepository
{
    public function getSeatsForEvent(Event $event): Collection
    {
        return Seat::query()
            ->with('section')
            ->whereHas('section', fn ($query) => $query->where('venue_id', $event->venue_id))
            ->orderBy('section_id')
            ->orderBy('row_label')
            ->orderBy('seat_number')
            ->get();
    }

    public function lockSeatsForEvent(Event $event, array $seatIds): Collection
    {
        return Seat::query()
            ->with('section')
            ->whereIn('id', $seatIds)
            ->whereHas('section', fn ($query) => $query->where('venue_id', $event->venue_id))
            ->lockForUpdate()
            ->get();
    }

    public function expireReservations(Event $event, CarbonImmutable $now): void
    {
        Booking::query()
            ->where('event_id', $event->id)
            ->where('status', 'reserved')
            ->where('reserved_until', '<=', $now)
            ->update(['status' => 'expired']);
    }

    public function findConflictingSeatIds(Event $event, array $seatIds, CarbonImmutable $now): array
    {
        $reservedSeatIds = DB::table('booking_seat')
            ->join('bookings', 'bookings.id', '=', 'booking_seat.booking_id')
            ->where('bookings.event_id', $event->id)
            ->whereIn('booking_seat.seat_id', $seatIds)
            ->where(function ($query) use ($now): void {
                $query->where('bookings.status', 'confirmed')
                    ->orWhere(function ($reservationQuery) use ($now): void {
                        $reservationQuery->where('bookings.status', 'reserved')
                            ->where('bookings.reserved_until', '>', $now);
                    });
            })
            ->pluck('booking_seat.seat_id')
            ->all();

        $soldSeatIds = Ticket::query()
            ->whereHas('booking', fn ($query) => $query->where('event_id', $event->id))
            ->whereIn('seat_id', $seatIds)
            ->pluck('seat_id')
            ->all();

        return array_values(array_unique([...$reservedSeatIds, ...$soldSeatIds]));
    }
}
