<?php

namespace App\Repositories;

use App\Models\Booking;

class BookingRepository
{
    public function create(array $data): Booking
    {
        return Booking::query()->create($data);
    }

    public function findOwnedBookingForUpdate(int $bookingId, int $userId): Booking
    {
        return Booking::query()
            ->whereKey($bookingId)
            ->where('user_id', $userId)
            ->lockForUpdate()
            ->firstOrFail();
    }
}
