<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $fillable = [
        'booking_id',
        'seat_id',
        'qr_code',
        'pdf_path',
        'status',
        'issued_at',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }
}
