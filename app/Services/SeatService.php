<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use App\Repositories\BookingRepository;
use App\Repositories\SeatRepository;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SeatService
{
    public function __construct(
        private readonly SeatRepository $seats,
        private readonly BookingRepository $bookings,
    ) {
    }

    public function getEventSeats(Event $event): Collection
    {
        $now = CarbonImmutable::now();
        $this->seats->expireReservations($event, $now);

        $seats = $this->seats->getSeatsForEvent($event);
        $conflicts = $this->seats->findConflictingSeatIds($event, $seats->pluck('id')->all(), $now);
        $soldSeats = Ticket::query()
            ->whereHas('booking', fn ($query) => $query->where('event_id', $event->id))
            ->pluck('seat_id')
            ->all();

        $soldMap = array_flip($soldSeats);
        $conflictMap = array_flip($conflicts);

        return $seats->map(function ($seat) use ($conflictMap, $soldMap) {
            $seat->state = isset($soldMap[$seat->id])
                ? 'sold'
                : (isset($conflictMap[$seat->id]) ? 'reserved' : 'available');

            return $seat;
        });
    }

    public function reserve(Event $event, User $user, array $seatIds): Booking
    {
        return DB::transaction(function () use ($event, $user, $seatIds) {
            $now = CarbonImmutable::now();
            $ttl = (int) env('RESERVATION_TTL_MINUTES', 10);

            $this->seats->expireReservations($event, $now);
            $lockedSeats = $this->seats->lockSeatsForEvent($event, $seatIds);

            if ($lockedSeats->count() !== count($seatIds)) {
                throw new HttpException(422, 'Some seats do not belong to the event venue.');
            }

            $conflicts = $this->seats->findConflictingSeatIds($event, $seatIds, $now);

            if ($conflicts !== []) {
                throw new HttpException(409, 'One or more seats are already reserved or sold.');
            }

            $booking = $this->bookings->create([
                'reference' => (string) Str::uuid(),
                'user_id' => $user->id,
                'event_id' => $event->id,
                'status' => 'reserved',
                'total' => $lockedSeats->sum('price'),
                'reserved_until' => $now->addMinutes($ttl),
            ]);

            $booking->seats()->attach(
                $lockedSeats->mapWithKeys(fn ($seat) => [
                    $seat->id => ['price_snapshot' => $seat->price],
                ])->all()
            );

            return $booking->load(['event.venue', 'seats.section', 'payments', 'tickets']);
        });
    }
}
