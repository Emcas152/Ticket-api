<?php

namespace App\Repositories;

use App\Models\Role;

class RoleRepository
{
    public function findByName(string $name): ?Role
    {
        return Role::query()->where('name', $name)->first();
    }
}
