<?php

declare(strict_types=1);

namespace App\Profile\Application\Betcity\UseCase\Edit;

use App\Common\Service\Core\Flusher;
use App\Profile\Domain\Betcity\BetcityRepository;
use App\Profile\Domain\Betcity\Exception\BetcityNotFoundException;
use App\Profile\Domain\Betcity\Exception\BetcityUserIdAlreadyTakenException;

final readonly class Handler
{
    public function __construct(
        private Flusher $flusher,
        private BetcityRepository $betcityRepository,
    ) {
    }

    public function handle(Command $command): Result
    {
        $betcity = $this->betcityRepository->findById($command->id);
        if (null === $betcity) {
            throw new BetcityNotFoundException(
                message: "Betcity #{$command->id} not found",
                context: ['id' => $command->id],
            );
        }

        $userId = $command->userId ?? $betcity->getUserId();
        $name = $command->name ?? $betcity->getName();
        $gender = $command->gender ?? $betcity->getGender();

        if (null !== $command->userId && $command->userId !== $betcity->getUserId() && $this->betcityRepository->hasByUserId($command->userId)) {
            throw new BetcityUserIdAlreadyTakenException(
                message: "Betcity #{$command->userId} not found",
                context: ['userId' => $command->userId],
            );
        }

        $betcity->edit(
            userId: $userId,
            name: $name,
            gender: $gender
        );

        $this->flusher->flush();

        return new Result(
            betcity: $betcity,
        );
    }
}
