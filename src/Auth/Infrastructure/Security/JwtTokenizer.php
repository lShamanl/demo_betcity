<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Security;

use DateInterval;
use DateTime;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;
use OpenSSLAsymmetricKey;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use UnexpectedValueException;

class JwtTokenizer
{
    private string $accessJwtTokenExpiredInterval = 'PT12H';
    private string $refreshJwtTokenExpiredInterval = 'P30D';

    public function __construct(
        private readonly JWT $jwt,
        #[Autowire('%env(ACCESS_JWT_TOKEN_EXPIRED_INTERVAL)%')]
        string $accessJwtTokenExpiredInterval,
        #[Autowire('%env(REFRESH_JWT_TOKEN_EXPIRED_INTERVAL)%')]
        string $refreshJwtTokenExpiredInterval,
    ) {
        if (!empty($accessJwtTokenExpiredInterval)) {
            $this->accessJwtTokenExpiredInterval = $accessJwtTokenExpiredInterval;
        }
        if (empty($refreshJwtTokenExpiredInterval)) {
            $this->refreshJwtTokenExpiredInterval = $refreshJwtTokenExpiredInterval;
        }
    }

    /**
     * @throws Exception
     */
    public function generateAccessToken(UserIdentity $user, array $payload = []): string
    {
        $privateKey = $this->getPrivateKey();

        $payload = array_merge(
            [
                'iat' => (new DateTime())->getTimestamp(),
                'exp' => (new DateTime())->add(new DateInterval($this->accessJwtTokenExpiredInterval))->getTimestamp(),
                'id' => $user->id,
                'username' => $user->getUserIdentifier(),
                'roles' => $user->getRoles(),
            ],
            $payload
        );

        /** @var resource $privateKey */
        return \Firebase\JWT\JWT::encode($payload, $privateKey, 'RS256');
    }

    /**
     * @throws Exception
     */
    public function generateRefreshToken(UserIdentity $user): string
    {
        $privateKey = $this->getPrivateKey();

        $payload = [
            'iat' => (new DateTime())->getTimestamp(),
            'exp' => (new DateTime())->add(new DateInterval($this->refreshJwtTokenExpiredInterval))->getTimestamp(),
            'refresh.userId' => $user->id,
            'role' => $user->role,
        ];

        /** @var resource $privateKey */
        return \Firebase\JWT\JWT::encode($payload, $privateKey, 'RS256');
    }

    public function getUserIdByRefreshToken(string $refreshToken): string
    {
        try {
            $tokenDecode = $this->decode($refreshToken);
        } catch (UnexpectedValueException $unexpectedValueException) {
            throw new AccessDeniedException($unexpectedValueException->getMessage(), $unexpectedValueException);
        }

        if (!isset($tokenDecode['refresh.userId'])) {
            throw new AccessDeniedException('This token is not refresh');
        }

        return $tokenDecode['refresh.userId'];
    }

    public function decode(string $token): array
    {
        $publicKey = file_get_contents($this->jwt->publicKey);
        if (false === $publicKey) {
            throw new RuntimeException('Public key not found');
        }

        return (array) \Firebase\JWT\JWT::decode($token, new Key($publicKey, 'RS256'));
    }

    public function tokenIsExpired(string $token): bool
    {
        try {
            return $this->decode($token)['exp'] < (new DateTime())->getTimestamp();
        } catch (ExpiredException $expiredException) {
            return true;
        }
    }

    /**
     * @throws Exception
     */
    protected function getPrivateKey(): OpenSSLAsymmetricKey
    {
        $key = openssl_pkey_get_private(
            'file://' . $this->jwt->privateKey,
            $this->jwt->passPhrase
        );

        if (false === $key) {
            throw new Exception('Invalid private key', 500);
        }

        return $key;
    }
}
