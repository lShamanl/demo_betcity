<?php

declare(strict_types=1);

namespace App\Profile\Domain\Betcity\Type;

use App\Profile\Domain\Betcity\ValueObject\BetcityId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\BigIntType;

class BetcityIdType extends BigIntType
{
    public const NAME = 'profile_betcity_id';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToDatabaseValue(
        mixed $value,
        AbstractPlatform $platform,
    ): ?string {
        return $value instanceof BetcityId ? $value->__toString() : $value;
    }

    /**
     * @psalm-suppress InvalidNullableReturnType
     * @psalm-suppress NullableReturnStatement
     */
    public function convertToPHPValue(
        mixed $value,
        AbstractPlatform $platform,
    ): ?BetcityId {
        return !empty($value) ? new BetcityId((string) $value) : null;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
