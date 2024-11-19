<?php

declare(strict_types=1);

namespace App\Common\Doctrine\Type;

use IWD\Measurement\Core\Math\ValueObject\Measurement;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\FloatType;

class MeasurementType extends FloatType
{
    public const NAME = 'common_measurement';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?float
    {
        return $value instanceof Measurement ? (float) $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Measurement
    {
        return is_numeric($value) ? new Measurement((string) $value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
