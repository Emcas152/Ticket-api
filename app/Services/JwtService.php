<?php

namespace App\Services;

use App\Models\JwtBlacklist;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Str;

class JwtService
{
    public function issueToken(User $user): string
    {
        $issuedAt = CarbonImmutable::now();
        $expiresAt = $issuedAt->addSeconds((int) env('JWT_TTL', 3600));

        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $payload = [
            'sub' => $user->id,
            'role' => $user->role?->name,
            'iat' => $issuedAt->timestamp,
            'exp' => $expiresAt->timestamp,
            'jti' => (string) Str::uuid(),
        ];

        $encodedHeader = $this->base64UrlEncode(json_encode($header, JSON_THROW_ON_ERROR));
        $encodedPayload = $this->base64UrlEncode(json_encode($payload, JSON_THROW_ON_ERROR));
        $signature = hash_hmac('sha256', $encodedHeader.'.'.$encodedPayload, $this->secret(), true);

        return $encodedHeader.'.'.$encodedPayload.'.'.$this->base64UrlEncode($signature);
    }

    public function decode(string $token): array
    {
        $segments = explode('.', $token);

        if (count($segments) !== 3) {
            throw new AuthenticationException('Invalid token format.');
        }

        [$encodedHeader, $encodedPayload, $encodedSignature] = $segments;
        $expectedSignature = $this->base64UrlEncode(
            hash_hmac('sha256', $encodedHeader.'.'.$encodedPayload, $this->secret(), true)
        );

        if (! hash_equals($expectedSignature, $encodedSignature)) {
            throw new AuthenticationException('Invalid token signature.');
        }

        $payload = json_decode($this->base64UrlDecode($encodedPayload), true, 512, JSON_THROW_ON_ERROR);

        if (($payload['exp'] ?? 0) < now()->timestamp) {
            throw new AuthenticationException('Token expired.');
        }

        if ($this->isRevoked((string) ($payload['jti'] ?? ''))) {
            throw new AuthenticationException('Token revoked.');
        }

        return $payload;
    }

    public function revoke(string $token): void
    {
        $payload = $this->decode($token);

        JwtBlacklist::query()->firstOrCreate(
            ['token_id' => (string) $payload['jti']],
            ['expires_at' => CarbonImmutable::createFromTimestamp((int) $payload['exp'])]
        );
    }

    private function isRevoked(string $tokenId): bool
    {
        if ($tokenId === '') {
            return false;
        }

        return JwtBlacklist::query()->where('token_id', $tokenId)->exists();
    }

    private function secret(): string
    {
        $key = env('JWT_SECRET') ?: env('APP_KEY') ?: 'local-dev-jwt-secret';

        if (str_starts_with($key, 'base64:')) {
            return base64_decode(substr($key, 7), true) ?: 'local-dev-jwt-secret';
        }

        return $key;
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $value): string
    {
        $padding = 4 - (strlen($value) % 4);

        if ($padding < 4) {
            $value .= str_repeat('=', $padding);
        }

        return base64_decode(strtr($value, '-_', '+/'), true) ?: '';
    }
}
