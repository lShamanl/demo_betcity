<?php

declare(strict_types=1);

namespace App\Auth\Application\User\UseCase\Create;

enum ResultCase
{
    case Success;
    case EmailIsBusy;

    public function isEqual(self $enum): bool
    {
        return $this->name === $enum->name;
    }
}
