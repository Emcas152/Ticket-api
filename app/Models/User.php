<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuario';

    protected $fillable = [
        'nombre', 'email', 'clave_hash', 'rol_global', 'telefono', 'estado'
    ];

    protected $hidden = [
        'clave_hash'
    ];

    public function getAuthPassword()
    {
        return $this->clave_hash;
    }
}
