<?php

declare(strict_types=1);

namespace App\Auth\Application\User\UseCase\Remove;

use App\Auth\Domain\User\UserRepository;
use App\Common\Service\Core\Flusher;

readonly class Handler
{
    public function __construct(
        private Flusher $flusher,
        private UserRepository $userRepository,
    ) {
    }

    public function handle(Command $command): Result
    {
        $user = $this->userRepository->findById($command->id);
        if (null === $user) {
            return Result::userNotExists();
        }

        $this->userRepository->remove($user);
        $this->flusher->flush();

        return Result::success(
            user: $user,
            context: [
                'id' => $command->id->getValue(),
            ]
        );
    }
}
