<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seat extends Model
{
    protected $fillable = ['section_id', 'row_label', 'seat_number', 'price'];

    protected $appends = ['label'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class)
            ->withPivot(['price_snapshot'])
            ->withTimestamps();
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function getLabelAttribute(): string
    {
        return trim(collect([$this->row_label, $this->seat_number])->filter()->implode('-'));
    }
}
