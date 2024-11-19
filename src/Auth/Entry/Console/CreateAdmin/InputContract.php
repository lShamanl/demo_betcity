<?php

declare(strict_types=1);

namespace App\Auth\Entry\Console\CreateAdmin;

use IWD\SymfonyEntryContract\Interfaces\InputContractInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class InputContract implements InputContractInterface
{
    /** Email admin */
    #[NotNull]
    #[NotBlank]
    public string $email = 'admin@dev.com';

    /** Root user password */
    #[NotNull]
    #[NotBlank]
    #[Length(min: 4)]
    public string $password = 'root';

    /** User name */
    #[NotNull]
    #[NotBlank]
    #[Length(min: 1)]
    public string $name = 'Administration';
}
