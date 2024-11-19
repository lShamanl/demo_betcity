<?php

declare(strict_types=1);

namespace App\Auth\Domain\User\Type;

use App\Auth\Domain\User\ValueObject\UserId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\BigIntType;

class UserIdType extends BigIntType
{
    public const NAME = 'auth_user_id';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToDatabaseValue(
        mixed $value,
        AbstractPlatform $platform,
    ): ?string {
        return $value instanceof UserId ? $value->__toString() : $value;
    }

    /**
     * @psalm-suppress InvalidNullableReturnType
     * @psalm-suppress NullableReturnStatement
     */
    public function convertToPHPValue(
        mixed $value,
        AbstractPlatform $platform,
    ): ?UserId {
        return !empty($value) ? new UserId((string) $value) : null;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
