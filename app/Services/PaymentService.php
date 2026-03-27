<?php

namespace App\Services;

use App\Events\BookingPaid;
use App\Models\User;
use App\Repositories\BookingRepository;
use App\Repositories\PaymentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PaymentService
{
    public function __construct(
        private readonly BookingRepository $bookings,
        private readonly PaymentRepository $payments,
        private readonly TicketService $tickets,
    ) {
    }

    public function pay(User $user, array $data): array
    {
        return DB::transaction(function () use ($user, $data) {
            $booking = $this->bookings->findOwnedBookingForUpdate((int) $data['booking_id'], $user->id);

            if ($booking->status === 'cancelled' || $booking->status === 'expired') {
                throw new HttpException(409, 'Booking is not payable.');
            }

            if ($booking->reserved_until && $booking->reserved_until->isPast() && $booking->status === 'reserved') {
                $booking->update(['status' => 'expired']);

                throw new HttpException(409, 'Reservation expired.');
            }

            if ($booking->status === 'reserved') {
                $booking->update([
                    'status' => 'confirmed',
                    'confirmed_at' => now(),
                ]);
            }

            $payment = $this->payments->create([
                'booking_id' => $booking->id,
                'provider' => $data['provider'],
                'provider_reference' => $data['provider_reference'] ?? $data['provider'].'_'.Str::uuid(),
                'amount' => $booking->total,
                'status' => ($data['simulate_success'] ?? true) ? 'paid' : 'pending',
                'payload' => [
                    'payment_method_token' => $data['payment_method_token'] ?? null,
                    'simulated' => true,
                ],
                'paid_at' => ($data['simulate_success'] ?? true) ? now() : null,
            ]);

            $issuedTickets = collect();

            if ($payment->status === 'paid') {
                $issuedTickets = $this->tickets->issueForBooking($booking->fresh(['event.venue', 'seats.section']));
                event(new BookingPaid($booking->fresh(['user', 'event.venue']), $payment));
            }

            return [
                'payment' => $payment->fresh(),
                'booking' => $booking->fresh(['event.venue', 'seats.section', 'payments', 'tickets']),
                'tickets' => $issuedTickets,
            ];
        });
    }
}
