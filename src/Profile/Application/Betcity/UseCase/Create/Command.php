<?php

declare(strict_types=1);

namespace App\Profile\Application\Betcity\UseCase\Create;

use App\Profile\Domain\Betcity\Enum\Gender;

final readonly class Command
{
    public function __construct(
        public int $userId,
        public ?string $name,
        public Gender $gender,
    ) {
    }
}
