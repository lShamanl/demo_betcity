<?php

declare(strict_types=1);

namespace App\Auth\Application\User\UseCase\Edit;

use App\Auth\Domain\User\UserRepository;
use App\Common\Service\Core\Flusher;

final readonly class Handler
{
    public function __construct(
        private UserRepository $userRepository,
        private Flusher $flusher,
    ) {
    }

    public function handle(Command $command): Result
    {
        $user = $this->userRepository->findById($command->id);
        if (null === $user) {
            return Result::userNotExists();
        }
        if (null !== $command->email && $command->email !== $user->getEmail() && $this->userRepository->hasByEmail($command->email)) {
            return Result::emailIsBusy();
        }

        $name = $command->name ?? $user->getName();
        $email = $command->email ?? $user->getEmail();
        $role = $command->role ?? $user->getRole();

        $user->edit(
            name: $name,
            email: $email,
            roles: [$role]
        );

        $this->flusher->flush();

        return Result::success($user);
    }
}
