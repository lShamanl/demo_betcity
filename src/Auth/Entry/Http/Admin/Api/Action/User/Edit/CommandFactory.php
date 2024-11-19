<?php

declare(strict_types=1);

namespace App\Auth\Entry\Http\Admin\Api\Action\User\Edit;

use App\Auth\Application\User\UseCase\Edit\Command;
use App\Auth\Domain\User\User;
use App\Auth\Domain\User\ValueObject\UserId;

class CommandFactory
{
    public function create(InputContract $inputContract): Command
    {
        return new Command(
            id: new UserId((string) $inputContract->id),
            name: (string) $inputContract->name,
            email: (string) $inputContract->email,
            role: User::ROLE_ADMIN, // todo: переделать на enum
        );
    }
}
