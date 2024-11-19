<?php

declare(strict_types=1);

namespace App\Profile\Entry\Http\Admin\Api\Action\Betcity\Remove;

use App\Profile\Application\Betcity\UseCase\Remove\Command;
use App\Profile\Domain\Betcity\ValueObject\BetcityId;

class CommandFactory
{
    public function create(InputContract $inputContract): Command
    {
        return new Command(
            id: new BetcityId((string) $inputContract->id),
        );
    }
}
