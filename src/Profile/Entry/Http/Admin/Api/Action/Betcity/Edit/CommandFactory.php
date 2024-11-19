<?php

declare(strict_types=1);

namespace App\Profile\Entry\Http\Admin\Api\Action\Betcity\Edit;

use App\Profile\Application\Betcity\UseCase\Edit\Command;
use App\Profile\Domain\Betcity\Enum\Gender;
use App\Profile\Domain\Betcity\ValueObject\BetcityId;

class CommandFactory
{
    public function create(InputContract $inputContract): Command
    {
        return new Command(
            id: new BetcityId((string) $inputContract->id),
            userId: (int) $inputContract->userId,
            name: null !== $inputContract->name ? $inputContract->name : null,
            gender: Gender::from((string) $inputContract->gender),
        );
    }
}
