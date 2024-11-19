<?php

declare(strict_types=1);

namespace App\Profile\Entry\Http\Admin\Api\Action\Betcity\Create;

use IWD\SymfonyEntryContract\Interfaces\InputContractInterface;

class InputContract implements InputContractInterface
{
    public ?int $userId = null;

    public ?string $name = null;

    public ?string $gender = null;
}
