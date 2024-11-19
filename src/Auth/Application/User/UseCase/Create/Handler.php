<?php

declare(strict_types=1);

namespace App\Auth\Application\User\UseCase\Create;

use App\Auth\Domain\User\Service\UserNextIdService;
use App\Auth\Domain\User\User;
use App\Auth\Domain\User\UserRepository;
use App\Common\Service\Core\Flusher;
use DateTimeImmutable;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class Handler
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private Flusher $flusher,
        private UserRepository $userRepository,
        private UserNextIdService $userNextIdService,
    ) {
    }

    public function handle(Command $command): Result
    {
        if ($this->userRepository->hasByEmail($command->email)) {
            return Result::emailIsBusy();
        }
        $now = new DateTimeImmutable();
        $user = User::create(
            id: $this->userNextIdService->allocateId(),
            createdAt: $now,
            updatedAt: $now,
            email: $command->email,
            roles: [$command->role],
            name: $command->name
        );
        $this->userRepository->add($user);
        $user->changePassword(
            $this->passwordHasher->hashPassword($user, $command->plainPassword)
        );

        $this->flusher->flush();

        return Result::success(
            user: $user,
            context: [
                'id' => $user->getId()->getValue(),
            ]
        );
    }
}
