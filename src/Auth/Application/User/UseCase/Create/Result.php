<?php

declare(strict_types=1);

namespace App\Auth\Application\User\UseCase\Create;

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

    public static function emailIsBusy(array $context = []): self
    {
        return new self(case: ResultCase::EmailIsBusy, context: $context);
    }

    public function isEmailIsBusy(): bool
    {
        return $this->case->isEqual(ResultCase::EmailIsBusy);
    }
}
