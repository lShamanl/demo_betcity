<?php

declare(strict_types=1);

namespace App\Profile\Entry\Http\Admin\Api\Action\Betcity\Edit;

use IWD\SymfonyEntryContract\Interfaces\InputContractInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Positive;

class InputContract implements InputContractInterface
{
    #[NotNull]
    #[Positive]
    public ?string $id = null;

    public ?int $userId = null;

    public ?string $name = null;

    public ?string $gender = null;
}
