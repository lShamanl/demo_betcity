<?php

declare(strict_types=1);

namespace App\Profile\Domain\Betcity\ValueObject;

use Webmozart\Assert\Assert;

readonly class BetcityId
{
    public function __construct(private string $value)
    {
        Assert::notEmpty($value);
        Assert::numeric($value);
        Assert::greaterThan($value, 0);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
