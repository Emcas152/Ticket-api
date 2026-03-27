<?php

namespace App\Services;

use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly RoleRepository $roles,
        private readonly JwtService $jwtService,
    ) {
    }

    public function register(array $data): array
    {
        $role = $this->roles->findByName('user');

        $user = $this->users->create([
            'role_id' => $role?->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $user->load('role');

        return [
            'user' => $user,
            'token' => $this->jwtService->issueToken($user),
        ];
    }

    public function login(array $credentials): array
    {
        $user = $this->users->findByEmail($credentials['email']);

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw new AuthenticationException('Invalid credentials.');
        }

        if (! $user->is_active) {
            throw new AuthenticationException('User account is inactive.');
        }

        return [
            'user' => $user,
            'token' => $this->jwtService->issueToken($user),
        ];
    }

    public function logout(string $token): void
    {
        $this->jwtService->revoke($token);
    }
}
