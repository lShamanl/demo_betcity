<?php

declare(strict_types=1);

namespace App\Tests\Integration\Profile\Domain\Betcity\Service;

use App\Profile\Domain\Betcity\Service\BetcityNextIdService;
use App\Profile\Domain\Betcity\ValueObject\BetcityId;
use App\Tests\Integration\IntegrationTestCase;

/** @covers \App\Profile\Domain\Betcity\Service\BetcityNextIdService */
class BetcityNextIdServiceTest extends IntegrationTestCase
{
    protected static BetcityNextIdService $betcityNextIdService;

    public function setUp(): void
    {
        parent::setUp();
        self::$betcityNextIdService = self::get(BetcityNextIdService::class);
    }

    public function testAllocateId(): void
    {
        $id = self::$betcityNextIdService->allocateId();

        self::assertInstanceOf(BetcityId::class, $id);
        self::assertSame(
            (int) $id->getValue() + 1,
            (int) self::$betcityNextIdService->allocateId()->getValue()
        );
    }
}
