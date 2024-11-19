<?php

declare(strict_types=1);

namespace App\Tests\Unit\Auth\Domain\User\Type;

use App\Auth\Domain\User\Type\UserIdType;
use App\Auth\Domain\User\ValueObject\UserId;
use App\Tests\Unit\UnitTestCase;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/** @covers \App\Auth\Domain\User\Type\UserIdType */
class IdTypeTest extends UnitTestCase
{
    public function testConvertToPHPValueSuccess(): void
    {
        /** @psalm-suppress InternalMethod */
        $idType = new UserIdType();
        $abstractPlatformMock = $this->createMock(AbstractPlatform::class);
        $value = '708';

        /** @var UserId|null $phpValue */
        $phpValue = $idType->convertToPHPValue($value, $abstractPlatformMock);

        self::assertSame($value, $phpValue?->getValue());
        self::assertInstanceOf(UserId::class, $phpValue);
    }

    public function testConvertToPHPValueWithEmptyValue(): void
    {
        /** @psalm-suppress InternalMethod */
        $idType = new UserIdType();
        $abstractPlatformMock = $this->createMock(AbstractPlatform::class);

        self::assertNull(
            $idType->convertToPHPValue(null, $abstractPlatformMock)
        );
    }

    public function testConvertToDatabaseValueSuccess(): void
    {
        /** @psalm-suppress InternalMethod */
        $idType = new UserIdType();
        $abstractPlatformMock = $this->createMock(AbstractPlatform::class);
        $value = new UserId('697');

        $convertedDatabaseValue = $idType->convertToDatabaseValue($value, $abstractPlatformMock);
        self::assertSame($value->getValue(), $convertedDatabaseValue);
    }

    public function testConvertToDatabaseValueWithEmptyValue(): void
    {
        /** @psalm-suppress InternalMethod */
        $idType = new UserIdType();
        $abstractPlatformMock = $this->createMock(AbstractPlatform::class);

        self::assertNull(
            $idType->convertToDatabaseValue(null, $abstractPlatformMock)
        );
    }

    public function testGetName(): void
    {
        /** @psalm-suppress InternalMethod */
        $idType = new UserIdType();
        self::assertSame(UserIdType::NAME, $idType->getName());
    }

    public function testRequiresSQLCommentHint(): void
    {
        /** @psalm-suppress InternalMethod */
        $idType = new UserIdType();
        $abstractPlatformMock = $this->createMock(AbstractPlatform::class);
        self::assertTrue(
            $idType->requiresSQLCommentHint($abstractPlatformMock)
        );
    }
}
