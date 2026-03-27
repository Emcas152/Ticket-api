<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function create(array $data): User
    {
        return User::query()->create($data);
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()->with('role')->where('email', $email)->first();
    }

    public function findActiveById(int $id): ?User
    {
        return User::query()->with('role')->whereKey($id)->where('is_active', true)->first();
    }
}
