<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('jwt.auth')->group(function (): void {
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::get('events', [EventController::class, 'index']);
Route::get('events/{event}', [EventController::class, 'show']);
Route::get('events/{event}/seats', [SeatController::class, 'index']);

Route::middleware('jwt.auth')->group(function (): void {
    Route::post('seats/reserve', [SeatController::class, 'reserve']);
    Route::post('bookings', [BookingController::class, 'store']);
    Route::post('payments', [PaymentController::class, 'store']);
    Route::get('tickets', [TicketController::class, 'index']);
    Route::get('tickets/{ticket}/download', [TicketController::class, 'download']);
});

Route::middleware(['jwt.auth', 'role:admin,organizer'])->group(function (): void {
    Route::post('events', [EventController::class, 'store']);
    Route::put('events/{event}', [EventController::class, 'update']);
    Route::patch('events/{event}', [EventController::class, 'update']);
    Route::delete('events/{event}', [EventController::class, 'destroy']);
});
