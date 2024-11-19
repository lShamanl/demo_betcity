<?php

declare(strict_types=1);

namespace App\Tests\Unit\Common\Doctrine\Type;

use App\Common\Doctrine\Type\MeasurementType;
use IWD\Measurement\Core\Math\ValueObject\Measurement;
use App\Tests\Unit\UnitTestCase;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/** @covers \App\Common\Doctrine\Type\MeasurementType */
class MeasurementTypeTest extends UnitTestCase
{
    public function testConvertToPHPValueSuccess(): void
    {
        /** @psalm-suppress InternalMethod */
        $measurementType = new MeasurementType();
        $abstractPlatformMock = $this->createMock(AbstractPlatform::class);
        $value = '997.21';

        /** @var Measurement|null $phpValue */
        $phpValue = $measurementType->convertToPHPValue($value, $abstractPlatformMock);

        self::assertSame($value, $phpValue?->getValue());
        self::assertInstanceOf(Measurement::class, $phpValue);
    }

    public function testConvertToPHPValueWithEmptyValue(): void
    {
        /** @psalm-suppress InternalMethod */
        $measurementType = new MeasurementType();
        $abstractPlatformMock = $this->createMock(AbstractPlatform::class);

        self::assertNull(
            $measurementType->convertToPHPValue(null, $abstractPlatformMock)
        );
    }

    public function testConvertToDatabaseValueSuccess(): void
    {
        /** @psalm-suppress InternalMethod */
        $measurementType = new MeasurementType();
        $abstractPlatformMock = $this->createMock(AbstractPlatform::class);
        $value = new Measurement('14.124');

        $convertedDatabaseValue = $measurementType->convertToDatabaseValue($value, $abstractPlatformMock);
        self::assertSame($value->getValue(), (string) $convertedDatabaseValue);
    }

    public function testConvertToDatabaseValueWithEmptyValue(): void
    {
        /** @psalm-suppress InternalMethod */
        $measurementType = new MeasurementType();
        $abstractPlatformMock = $this->createMock(AbstractPlatform::class);

        self::assertNull(
            $measurementType->convertToDatabaseValue(null, $abstractPlatformMock)
        );
    }

    public function testGetName(): void
    {
        /** @psalm-suppress InternalMethod */
        $measurementType = new MeasurementType();
        self::assertSame(MeasurementType::NAME, $measurementType->getName());
    }

    public function testRequiresSQLCommentHint(): void
    {
        /** @psalm-suppress InternalMethod */
        $measurementType = new MeasurementType();
        $abstractPlatformMock = $this->createMock(AbstractPlatform::class);
        self::assertTrue(
            $measurementType->requiresSQLCommentHint($abstractPlatformMock)
        );
    }
}
