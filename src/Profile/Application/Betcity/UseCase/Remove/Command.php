<?php

declare(strict_types=1);

namespace App\Profile\Application\Betcity\UseCase\Remove;

use App\Profile\Domain\Betcity\ValueObject\BetcityId;

final readonly class Command
{
    public function __construct(public BetcityId $id)
    {
    }
}
