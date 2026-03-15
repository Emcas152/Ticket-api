<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaccion;
use App\Models\Orden;
use App\Models\Entrada;
use App\Jobs\GenerateTicketPdfAndSendEmail;

class PaymentWebhookController extends Controller
{
    public function webhook(Request $request)
    {
        $payload = $request->all();
        $referencia = $payload['reference'] ?? null;
        $ordenId = $payload['metadata']['orden_id'] ?? null;

        if (!$ordenId) {
            return response()->json(['error' => 'orden_id missing'], 400);
        }

        $orden = Orden::find($ordenId);
        if (!$orden) {
            return response()->json(['error' => 'orden not found'], 404);
        }

        $transaccion = Transaccion::create([
            'orden_id' => $orden->id,
            'proveedor' => $payload['provider'] ?? 'unknown',
            'referencia_pasarela' => $referencia,
            'monto' => $payload['amount'] ?? $orden->importe_total,
            'metodo_pago' => $orden->metodo_pago,
            'estado' => 'pagada',
            'payload_respuesta' => json_encode($payload),
        ]);

        $orden->estado_pago = 'pagado';
        $orden->referencia_transaccion = $referencia;
        $orden->fecha_pagada = now();
        $orden->save();

        $entradas = Entrada::where('orden_id', $orden->id)->get();
        foreach ($entradas as $entrada) {
            GenerateTicketPdfAndSendEmail::dispatch($entrada);
        }

        return response()->json(['ok' => true]);
    }
}
