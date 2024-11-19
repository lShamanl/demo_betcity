<?php

declare(strict_types=1);

namespace App\Auth\Entry\Http\Admin\Api\Action\User\Create;

use App\Auth\Application\User\UseCase\Create\Command;
use App\Auth\Domain\User\User;

class CommandFactory
{
    public function create(InputContract $inputContract): Command
    {
        return new Command(
            email: (string) $inputContract->email,
            plainPassword: (string) $inputContract->plainPassword,
            role: User::ROLE_ADMIN, // todo: переделать на enum
            name: (string) $inputContract->name,
        );
    }
}
