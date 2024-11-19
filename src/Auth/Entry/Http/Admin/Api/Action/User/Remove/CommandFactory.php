<?php

declare(strict_types=1);

namespace App\Auth\Entry\Http\Admin\Api\Action\User\Remove;

use App\Auth\Application\User\UseCase\Remove\Command;
use App\Auth\Domain\User\ValueObject\UserId;

class CommandFactory
{
    public function create(InputContract $inputContract): Command
    {
        return new Command(
            id: new UserId((string) $inputContract->id),
        );
    }
}
