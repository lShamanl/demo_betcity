<?php

declare(strict_types=1);

namespace App\Tests\Integration\Auth\Domain\User\Service;

use App\Auth\Domain\User\Service\UserNextIdService;
use App\Auth\Domain\User\ValueObject\UserId;
use App\Tests\Integration\IntegrationTestCase;

/** @covers \App\Auth\Domain\User\Service\UserNextIdService */
class UserNextIdServiceTest extends IntegrationTestCase
{
    protected static UserNextIdService $userNextIdService;

    public function setUp(): void
    {
        parent::setUp();
        self::$userNextIdService = self::get(UserNextIdService::class);
    }

    public function testAllocateId(): void
    {
        $id = self::$userNextIdService->allocateId();

        self::assertInstanceOf(UserId::class, $id);
        self::assertSame(
            (int) $id->getValue() + 1,
            (int) self::$userNextIdService->allocateId()->getValue()
        );
    }
}
