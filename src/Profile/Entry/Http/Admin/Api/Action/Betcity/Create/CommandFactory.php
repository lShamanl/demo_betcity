<?php

declare(strict_types=1);

namespace App\Profile\Entry\Http\Admin\Api\Action\Betcity\Create;

use App\Profile\Application\Betcity\UseCase\Create\Command;
use App\Profile\Domain\Betcity\Enum\Gender;

class CommandFactory
{
    public function create(InputContract $inputContract): Command
    {
        return new Command(
            userId: (int) $inputContract->userId,
            name: null !== $inputContract->name ? $inputContract->name : null,
            gender: Gender::from((string) $inputContract->gender),
        );
    }
}
