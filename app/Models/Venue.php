<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    protected $fillable = ['name', 'address', 'city', 'country', 'lat', 'lng', 'seat_map_config'];

    protected function casts(): array
    {
        return [
            'seat_map_config' => 'array',
        ];
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }
}
