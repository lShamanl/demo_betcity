<?php

declare(strict_types=1);

namespace App\Auth\Application\User\UseCase\Create;

final readonly class Command
{
    public function __construct(
        public string $email,
        public string $plainPassword,
        public string $role,
        public string $name,
    ) {
    }
}
