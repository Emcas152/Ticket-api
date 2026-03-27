<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Ticket;
use App\Repositories\TicketRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketService
{
    public function __construct(
        private readonly TicketRepository $tickets,
    ) {
    }

    public function listForUser(int $userId): Collection
    {
        return $this->tickets->findForUser($userId);
    }

    public function findOwnedTicket(int $ticketId, int $userId): Ticket
    {
        return $this->tickets->findOwnedTicket($ticketId, $userId);
    }

    public function issueForBooking(Booking $booking): Collection
    {
        $booking->loadMissing(['event.venue', 'seats.section']);
        $issued = collect();

        foreach ($booking->seats as $seat) {
            $ticket = $this->tickets->findByBookingAndSeat($booking, $seat->id);

            if (! $ticket) {
                $qrCode = 'ticket:'.$booking->reference.':'.$seat->id.':'.Str::uuid();
                $pdfPath = 'tickets/'.$booking->reference.'-seat-'.$seat->id.'.pdf';
                $qrSvg = QrCode::format('svg')->size(180)->generate($qrCode);

                $html = $this->buildPdfHtml($booking, $seat->label, $qrSvg, $qrCode);
                Storage::disk('public')->put($pdfPath, Pdf::loadHTML($html)->output());

                $ticket = $this->tickets->create([
                    'booking_id' => $booking->id,
                    'seat_id' => $seat->id,
                    'qr_code' => $qrCode,
                    'pdf_path' => $pdfPath,
                    'status' => 'issued',
                    'issued_at' => now(),
                ]);
            }

            $issued->push($ticket->load(['seat.section', 'booking.event.venue']));
        }

        return $issued;
    }

    private function buildPdfHtml(Booking $booking, string $seatLabel, string $qrSvg, string $qrCode): string
    {
        $event = $booking->event;
        $venue = $event->venue;

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; padding: 24px; color: #111827; }
        .card { border: 1px solid #d1d5db; border-radius: 12px; padding: 24px; }
        .title { font-size: 24px; font-weight: bold; margin-bottom: 8px; }
        .meta { margin-bottom: 4px; }
        .qr { margin-top: 24px; width: 180px; }
        .code { margin-top: 12px; font-size: 12px; color: #4b5563; }
    </style>
</head>
<body>
    <div class="card">
        <div class="title">{$event->title}</div>
        <div class="meta">Venue: {$venue->name}</div>
        <div class="meta">Seat: {$seatLabel}</div>
        <div class="meta">Booking: {$booking->reference}</div>
        <div class="meta">Starts at: {$event->starts_at?->toDateTimeString()}</div>
        <div class="qr">{$qrSvg}</div>
        <div class="code">{$qrCode}</div>
    </div>
</body>
</html>
HTML;
    }
}
