<?php

declare(strict_types=1);

namespace App\Auth\Entry\Http\Token\Authentication;

use IWD\SymfonyEntryContract\Interfaces\InputContractInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class InputContract implements InputContractInterface
{
    #[NotNull]
    #[Email]
    #[Length(max: 255)]
    public string $email;

    #[NotNull]
    public string $password;
}
