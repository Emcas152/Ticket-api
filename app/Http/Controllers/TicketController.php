<?php

namespace App\Http\Controllers;

use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function __construct(
        private readonly TicketService $tickets,
    ) {
    }

    public function index(Request $request)
    {
        return $this->success(TicketResource::collection($this->tickets->listForUser($request->user()->id)));
    }

    public function download(Request $request, Ticket $ticket)
    {
        $ownedTicket = $this->tickets->findOwnedTicket($ticket->id, $request->user()->id);

        if (! $ownedTicket->pdf_path || ! Storage::disk('public')->exists($ownedTicket->pdf_path)) {
            abort(404, 'Ticket PDF not found.');
        }

        return Storage::disk('public')->download($ownedTicket->pdf_path, 'ticket-'.$ownedTicket->id.'.pdf');
    }
}
