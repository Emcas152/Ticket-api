<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use App\Repositories\BookingRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BookingService
{
    public function __construct(
        private readonly BookingRepository $bookings,
    ) {
    }

    public function confirm(User $user, int $bookingId): Booking
    {
        return DB::transaction(function () use ($user, $bookingId) {
            $booking = $this->bookings->findOwnedBookingForUpdate($bookingId, $user->id);

            if ($booking->status === 'cancelled' || $booking->status === 'expired') {
                throw new HttpException(409, 'Booking can no longer be confirmed.');
            }

            if ($booking->status === 'reserved' && $booking->reserved_until && $booking->reserved_until->isPast()) {
                $booking->update(['status' => 'expired']);

                throw new HttpException(409, 'Reservation expired.');
            }

            if ($booking->status === 'reserved') {
                $booking->update([
                    'status' => 'confirmed',
                    'confirmed_at' => CarbonImmutable::now(),
                ]);
            }

            return $booking->fresh(['event.venue', 'seats.section', 'payments', 'tickets']);
        });
    }
}
