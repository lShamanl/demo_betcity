<?php

declare(strict_types=1);

namespace App\Auth\Domain\User\Event;

use App\Auth\Domain\User\ValueObject\UserId;

readonly class UserCreatedEvent
{
    public function __construct(
        public UserId $id,
    ) {
    }
}
