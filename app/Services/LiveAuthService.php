<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Config;

class LiveAuthService
{
    /**
     * Generate a signed JWT for a live session.
     *
     * @param int|string $liveId
     * @param int|string $userId
     * @param string $role (host|moderator|audience)
     * @param int $expirySeconds
     * @return string
     */
    public function generateLiveToken($liveId, $userId, $role = 'audience', $expirySeconds = 300): string
    {
        $payload = [
            'liveId' => $liveId,
            'userId' => $userId,
            'role' => $role,
            'iat' => time(),
            'exp' => time() + $expirySeconds,
            'nonce' => bin2hex(random_bytes(16)),
        ];

        // Use APP_KEY as the secret for the prototype
        $secret = config('app.key');

        return JWT::encode($payload, $secret, 'HS256');
    }
}
