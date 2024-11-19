<?php

declare(strict_types=1);

namespace App\Auth\Domain\User\ValueObject;

use Webmozart\Assert\Assert;

readonly class UserId
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
