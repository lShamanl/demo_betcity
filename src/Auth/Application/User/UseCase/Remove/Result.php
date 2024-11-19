<?php

declare(strict_types=1);

namespace App\Auth\Application\User\UseCase\Remove;

use App\Auth\Domain\User\User;

class Result
{
    public function __construct(
        public readonly ResultCase $case,
        public ?User $user = null,
        public array $context = [],
    ) {
    }

    public static function success(
        User $user,
        array $context = [],
    ): self {
        return new self(
            case: ResultCase::Success,
            user: $user,
            context: $context
        );
    }

    public function isSuccess(): bool
    {
        return $this->case->isEqual(ResultCase::Success);
    }

    public static function userNotExists(array $context = []): self
    {
        return new self(case: ResultCase::UserNotExists, context: $context);
    }

    public function isUserNotExists(): bool
    {
        return $this->case->isEqual(ResultCase::UserNotExists);
    }
}
