<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\Asiento;
use App\Models\AsientoReservado;

class ReservationController extends Controller
{
    public function reserveSeat(Request $request, $seatId)
    {
        $user = $request->user();
        $seat = Asiento::findOrFail($seatId);

        $lock = Cache::lock("seat:{$seat->id}", 300);
        if (!$lock->get()) {
            return response()->json(['message' => 'Asiento no disponible'], 409);
        }

        try {
            $token = (string) Str::uuid();
            $reserva = AsientoReservado::create([
                'asiento_id' => $seat->id,
                'evento_id' => $request->input('evento_id'),
                'usuario_id' => $user ? $user->id : null,
                'token_reserva' => $token,
                'estatus' => 'activo',
                'fecha_expiracion' => now()->addMinutes(5),
            ]);

            $seat->estatus = 'reservado';
            $seat->save();

            return response()->json(['reservation_token' => $token, 'expires_at' => $reserva->fecha_expiracion], 201);
        } finally {
        }
    }
}
