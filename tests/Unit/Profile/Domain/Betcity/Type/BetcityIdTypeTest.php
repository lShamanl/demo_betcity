<?php

declare(strict_types=1);

namespace App\Tests\Unit\Profile\Domain\Betcity\Type;

use App\Profile\Domain\Betcity\Type\BetcityIdType;
use App\Profile\Domain\Betcity\ValueObject\BetcityId;
use App\Tests\Unit\UnitTestCase;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/** @covers \App\Profile\Domain\Betcity\Type\BetcityIdType */
class BetcityIdTypeTest extends UnitTestCase
{
    public function testConvertToPHPValueSuccess(): void
    {
        /** @psalm-suppress InternalMethod */
        $idType = new BetcityIdType();
        $abstractPlatformMock = $this->createMock(AbstractPlatform::class);
        $value = (string) random_int(1, 999);

        /** @var BetcityId|null $phpValue */
        $phpValue = $idType->convertToPHPValue($value, $abstractPlatformMock);

        self::assertSame($value, $phpValue?->getValue());
        self::assertInstanceOf(BetcityId::class, $phpValue);
    }

    public function testConvertToPHPValueWithEmptyValue(): void
    {
        /** @psalm-suppress InternalMethod */
        $idType = new BetcityIdType();
        $abstractPlatformMock = $this->createMock(AbstractPlatform::class);

        self::assertNull(
            $idType->convertToPHPValue(null, $abstractPlatformMock)
        );
    }

    public function testConvertToDatabaseValueSuccess(): void
    {
        /** @psalm-suppress InternalMethod */
        $idType = new BetcityIdType();
        $abstractPlatformMock = $this->createMock(AbstractPlatform::class);
        $value = new BetcityId((string) random_int(1, 999));

        $convertedDatabaseValue = $idType->convertToDatabaseValue($value, $abstractPlatformMock);
        self::assertSame($value->getValue(), $convertedDatabaseValue);
    }

    public function testConvertToDatabaseValueWithEmptyValue(): void
    {
        /** @psalm-suppress InternalMethod */
        $idType = new BetcityIdType();
        $abstractPlatformMock = $this->createMock(AbstractPlatform::class);

        self::assertNull(
            $idType->convertToDatabaseValue(null, $abstractPlatformMock)
        );
    }

    public function testGetName(): void
    {
        /** @psalm-suppress InternalMethod */
        $idType = new BetcityIdType();
        self::assertSame(BetcityIdType::NAME, $idType->getName());
    }

    public function testRequiresSQLCommentHint(): void
    {
        /** @psalm-suppress InternalMethod */
        $idType = new BetcityIdType();
        $abstractPlatformMock = $this->createMock(AbstractPlatform::class);
        self::assertTrue(
            $idType->requiresSQLCommentHint($abstractPlatformMock)
        );
    }
}
