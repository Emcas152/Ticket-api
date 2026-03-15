<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entrada;
use App\Models\AccesoRegistro;

class TicketController extends Controller
{
    public function listUserTickets(Request $request)
    {
        $user = $request->user();
        $tickets = Entrada::where('comprador_id', $user->id)->get();
        return response()->json($tickets);
    }

    public function validateTicket(Request $request)
    {
        $ticketCode = $request->input('codigo_qr');
        $authorizer = $request->user();

        $entrada = Entrada::where('codigo_qr', $ticketCode)->first();
        if (!$entrada) {
            return response()->json(['result' => 'denegado', 'reason' => 'no existe'], 404);
        }

        if ($entrada->estado !== 'emitida') {
            return response()->json(['result' => 'denegado', 'reason' => 'estado no válido'], 409);
        }

        $entrada->estado = 'usada';
        $entrada->fecha_utilizado = now();
        $entrada->save();

        AccesoRegistro::create([
            'entrada_id' => $entrada->id,
            'autorizado_por' => $authorizer->id,
            'resultado' => 'permitido',
            'fecha_acceso' => now(),
        ]);

        return response()->json(['result' => 'permitido']);
    }
}
