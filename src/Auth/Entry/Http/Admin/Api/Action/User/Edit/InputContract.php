<?php

declare(strict_types=1);

namespace App\Auth\Entry\Http\Admin\Api\Action\User\Edit;

use IWD\SymfonyEntryContract\Interfaces\InputContractInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Positive;

class InputContract implements InputContractInterface
{
    #[NotNull]
    #[Positive]
    public ?string $id = null;

    public ?string $email = null;

    public ?string $role = null;

    public ?string $name = null;
}
