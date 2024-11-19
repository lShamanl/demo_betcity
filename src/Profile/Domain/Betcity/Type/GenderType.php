<?php

declare(strict_types=1);

namespace App\Profile\Domain\Betcity\Type;

use App\Profile\Domain\Betcity\Enum\Gender;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type as DoctrineBaseType;
use InvalidArgumentException;

class GenderType extends DoctrineBaseType
{
    public const NAME = 'profile_gender';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(
        array $column,
        AbstractPlatform $platform,
    ): string {
        return self::NAME;
    }

    public function convertToDatabaseValue(
        mixed $value,
        AbstractPlatform $platform,
    ): mixed {
        if (null === Gender::tryFrom((string) $value)) {
            throw new InvalidArgumentException('Invalid enum value');
        }

        return $value;
    }

    /**
     * @psalm-suppress InvalidNullableReturnType
     * @psalm-suppress NullableReturnStatement
     */
    public function convertToPHPValue(
        mixed $value,
        AbstractPlatform $platform,
    ): mixed {
        return $value;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
