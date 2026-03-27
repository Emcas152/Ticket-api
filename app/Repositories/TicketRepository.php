<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Collection;

class TicketRepository
{
    public function findForUser(int $userId): Collection
    {
        return Ticket::query()
            ->with(['seat.section', 'booking.event.venue'])
            ->whereHas('booking', fn ($query) => $query->where('user_id', $userId))
            ->latest()
            ->get();
    }

    public function findOwnedTicket(int $ticketId, int $userId): Ticket
    {
        return Ticket::query()
            ->with(['seat.section', 'booking.event'])
            ->whereKey($ticketId)
            ->whereHas('booking', fn ($query) => $query->where('user_id', $userId))
            ->firstOrFail();
    }

    public function create(array $data): Ticket
    {
        return Ticket::query()->create($data);
    }

    public function findByBookingAndSeat(Booking $booking, int $seatId): ?Ticket
    {
        return Ticket::query()
            ->where('booking_id', $booking->id)
            ->where('seat_id', $seatId)
            ->first();
    }
}
