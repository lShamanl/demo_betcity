<?php

declare(strict_types=1);

namespace App\Auth\Entry\Http\Admin\Api\Action\User\Create;

use IWD\SymfonyEntryContract\Interfaces\InputContractInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class InputContract implements InputContractInterface
{
    #[NotNull]
    #[Email]
    public ?string $email;

    #[NotNull]
    #[Length(min: 6, max: 4096)]
    public ?string $plainPassword;

    #[NotNull]
    public ?string $name;
}
