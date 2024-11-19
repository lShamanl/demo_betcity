<?php

declare(strict_types=1);

namespace App\Tests\Unit\Profile\Domain\Betcity\ValueObject;

use App\Profile\Domain\Betcity\ValueObject\BetcityId;
use App\Tests\Unit\UnitTestCase;

/** @covers \App\Profile\Domain\Betcity\ValueObject\BetcityId */
class BetcityIdTest extends UnitTestCase
{
    public function testToString(): void
    {
        $value = (string) random_int(1, 999);
        self::assertSame($value, (new BetcityId($value))->__toString());
    }

    public function testGetValue(): void
    {
        $value = (string) random_int(1, 999);
        self::assertSame($value, (new BetcityId($value))->getValue());
    }
}
