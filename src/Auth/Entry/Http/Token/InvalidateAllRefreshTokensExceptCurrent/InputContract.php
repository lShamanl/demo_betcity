<?php

declare(strict_types=1);

namespace App\Auth\Entry\Http\Token\InvalidateAllRefreshTokensExceptCurrent;

use IWD\SymfonyEntryContract\Interfaces\InputContractInterface;
use Symfony\Component\Validator\Constraints\NotNull;

class InputContract implements InputContractInterface
{
    #[NotNull]
    public string $refreshToken;
}
