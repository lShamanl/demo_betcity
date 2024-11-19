<?php

declare(strict_types=1);

namespace App\Profile\Application\Betcity\UseCase\Edit;

use App\Profile\Domain\Betcity\Enum\Gender;
use App\Profile\Domain\Betcity\ValueObject\BetcityId;

final readonly class Command
{
    public function __construct(
        public BetcityId $id,
        public ?int $userId,
        public ?string $name,
        public ?Gender $gender,
    ) {
    }
}
