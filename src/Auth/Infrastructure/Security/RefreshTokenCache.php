<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Security;

use App\Common\Service\Redis\RedisConnection;

class RefreshTokenCache
{
    public const PREFIX = 'auth:jwt:refresh:';

    private readonly RedisConnection $redisConnection;
    private readonly JwtTokenizer $jwtTokenizer;

    public function __construct(
        RedisConnection $redisConnection,
        JwtTokenizer $jwtTokenizer
    ) {
        $this->redisConnection = $redisConnection;
        $this->redisConnection->connect();
        $this->jwtTokenizer = $jwtTokenizer;
    }

    /**
     * @psalm-suppress InvalidArgument
     *
     * @throws \RedisException
     */
    public function cache(string $userId, string $token): bool
    {
        $tokens = $this->removeExpiredTokens(
            $this->getAllTokens($userId)
        );
        $tokens[] = $token;

        return (bool) $this->redisConnection->getRedis()->set(self::PREFIX . $userId, $tokens);
    }

    /**
     * @psalm-suppress InvalidArgument
     *
     * @throws \RedisException
     */
    public function invalidate(string $userId, string $token): bool
    {
        $tokens = $this->removeExpiredTokens(
            $this->getAllTokens($userId)
        );
        foreach ($tokens as $key => $tokenItem) {
            if ($token === $tokenItem) {
                unset($tokens[$key]);
            }
        }

        return (bool) $this->redisConnection->getRedis()->set(self::PREFIX . $userId, $tokens);
    }

    /**
     * @psalm-suppress InvalidArgument
     *
     * @throws \RedisException
     */
    public function invalidateAndCache(string $userId, string $invalidateToken, string $cacheToken): bool
    {
        $tokens = $this->removeExpiredTokens(
            $this->getAllTokens($userId)
        );
        foreach ($tokens as $key => $tokenItem) {
            if ($invalidateToken === $tokenItem) {
                unset($tokens[$key]);
            }
        }
        $tokens[] = $cacheToken;

        return (bool) $this->redisConnection->getRedis()->set(self::PREFIX . $userId, $tokens);
    }

    public function invalidateAll(string $userId): bool
    {
        return 0 === $this->redisConnection->getRedis()->del(self::PREFIX . $userId);
    }

    /**
     * @psalm-suppress InvalidArgument
     *
     * @throws \RedisException
     */
    public function invalidateAllExceptCurrent(string $userId, string $token): bool
    {
        return (bool) $this->redisConnection->getRedis()->set(self::PREFIX . $userId, [$token]);
    }

    public function validate(string $userId, string $token): bool
    {
        $tokens = $this->removeExpiredTokens(
            $this->getAllTokens($userId)
        );

        return in_array($token, $tokens, true);
    }

    protected function getAllTokens(string $userId): array
    {
        /** @var array|bool $tokens */
        $tokens = $this->redisConnection->getRedis()->get(self::PREFIX . $userId);
        if (!is_array($tokens)) {
            return [];
        }

        return $tokens;
    }

    protected function removeExpiredTokens(array $tokens): array
    {
        $actualTokens = [];
        foreach ($tokens as $token) {
            if (!$this->jwtTokenizer->tokenIsExpired($token)) {
                $actualTokens[] = $token;
            }
        }

        return $actualTokens;
    }
}
