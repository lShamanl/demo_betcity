<?php

declare(strict_types=1);

namespace App\Auth\Entry\Http\Token\Refresh;

use IWD\SymfonyEntryContract\Interfaces\InputContractInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class InputContract implements InputContractInterface
{
    #[NotNull]
    #[NotBlank]
    #[Length(min: 1)]
    public string $refreshToken;
}
