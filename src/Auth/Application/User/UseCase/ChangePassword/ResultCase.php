<?php

declare(strict_types=1);

namespace App\Auth\Application\User\UseCase\ChangePassword;

enum ResultCase
{
    case Success;
    case InvalidCredentials;

    public function isEqual(self $enum): bool
    {
        return $this->name === $enum->name;
    }
}
