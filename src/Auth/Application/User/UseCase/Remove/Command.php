<?php

declare(strict_types=1);

namespace App\Auth\Application\User\UseCase\Remove;

use App\Auth\Domain\User\ValueObject\UserId;

final readonly class Command
{
    public function __construct(public UserId $id)
    {
    }
}
