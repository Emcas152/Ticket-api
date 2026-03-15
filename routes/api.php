<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\TicketController;

Route::prefix('v1')->group(function () {
    Route::get('events', function() { return response()->json(['message'=>'Lista de eventos (implementar)']); });
    Route::get('events/{id}', function($id) { return response()->json(['message'=>"Evento $id (implementar)"]); });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('seats/{seat}/reserve', [ReservationController::class, 'reserveSeat']);
        Route::get('user/tickets', [TicketController::class, 'listUserTickets']);
    });

    Route::post('payments/webhook', [PaymentWebhookController::class, 'webhook']);

    Route::middleware(['auth:sanctum','can:authorizer'])->group(function () {
        Route::post('authorizer/validate', [TicketController::class, 'validateTicket']);
    });
});
