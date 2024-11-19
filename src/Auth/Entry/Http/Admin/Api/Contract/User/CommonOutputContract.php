<?php

declare(strict_types=1);

namespace App\Auth\Entry\Http\Admin\Api\Contract\User;

use App\Auth\Domain\User\User;
use DateTimeInterface;

class CommonOutputContract
{
    public string $id;

    public string $createdAt;

    public string $updatedAt;

    public string $email;

    public string $role;

    public string $name;

    public static function create(User $user): self
    {
        $contract = new self();
        $contract->id = $user->getId()->getValue();
        $contract->createdAt = $user->getCreatedAt()->format(DateTimeInterface::ATOM);
        $contract->updatedAt = $user->getUpdatedAt()->format(DateTimeInterface::ATOM);
        $contract->email = $user->getEmail();
        $contract->role = $user->getRole();
        $contract->name = $user->getName();

        return $contract;
    }
}
