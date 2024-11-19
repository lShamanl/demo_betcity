<?php

declare(strict_types=1);

namespace App\Profile\Application\Betcity\UseCase\Create;

use App\Common\Service\Core\Flusher;
use App\Profile\Domain\Betcity\Betcity;
use App\Profile\Domain\Betcity\BetcityRepository;
use App\Profile\Domain\Betcity\Exception\BetcityUserIdAlreadyTakenException;
use App\Profile\Domain\Betcity\Service\BetcityNextIdService;
use DateTimeImmutable;

final readonly class Handler
{
    public function __construct(
        private Flusher $flusher,
        private BetcityRepository $betcityRepository,
        private BetcityNextIdService $betcityNextIdService,
    ) {
    }

    public function handle(Command $command): Result
    {
        if ($this->betcityRepository->hasByUserId($command->userId)) {
            throw new BetcityUserIdAlreadyTakenException(
                message: "Betcity #{$command->userId} already created",
                context: ['userId' => $command->userId],
            );
        }
        $now = new DateTimeImmutable();
        $betcity = new Betcity(
            id: $this->betcityNextIdService->allocateId(),
            createdAt: $now,
            updatedAt: $now,
            userId: $command->userId,
            name: $command->name,
            gender: $command->gender
        );

        $this->betcityRepository->add($betcity);
        $this->flusher->flush();

        return new Result(
            betcity: $betcity,
        );
    }
}
