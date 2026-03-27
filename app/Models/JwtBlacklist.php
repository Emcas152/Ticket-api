<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JwtBlacklist extends Model
{
    protected $table = 'jwt_blacklist';

    protected $fillable = [
        'token_id',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }
}
