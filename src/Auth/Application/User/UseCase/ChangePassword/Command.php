<?php

declare(strict_types=1);

namespace App\Auth\Application\User\UseCase\ChangePassword;

use App\Auth\Domain\User\ValueObject\UserId;

final readonly class Command
{
    public function __construct(
        public UserId $id,
        public string $newPassword,
        public string $oldPassword,
    ) {
    }
}
