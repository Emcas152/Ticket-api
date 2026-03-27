<?php

namespace App\Http\Middleware;

use App\Repositories\UserRepository;
use App\Services\JwtService;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuthenticate
{
    public function __construct(
        private readonly JwtService $jwtService,
        private readonly UserRepository $users,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            throw new AuthenticationException('Missing bearer token.');
        }

        $payload = $this->jwtService->decode($token);
        $user = $this->users->findActiveById((int) $payload['sub']);

        if (! $user) {
            throw new AuthenticationException('User not found or inactive.');
        }

        $request->attributes->set('jwt_payload', $payload);
        $request->attributes->set('jwt_token', $token);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
