<?php

declare(strict_types=1);

namespace App\Profile\Entry\Http\Admin\Api\Contract\Betcity;

use App\Profile\Domain\Betcity\Betcity;
use DateTimeInterface;

class CommonOutputContract
{
    public string $id;

    public string $createdAt;

    public string $updatedAt;

    public int $userId;

    public ?string $name;

    public string $gender;

    public static function create(Betcity $betcity): self
    {
        $contract = new self();
        $contract->id = $betcity->getId()->getValue();
        $contract->createdAt = $betcity->getCreatedAt()->format(DateTimeInterface::ATOM);
        $contract->updatedAt = $betcity->getUpdatedAt()->format(DateTimeInterface::ATOM);
        $contract->userId = $betcity->getUserId();
        $contract->name = $betcity->getName();
        $contract->gender = $betcity->getGender()->value;

        return $contract;
    }
}
