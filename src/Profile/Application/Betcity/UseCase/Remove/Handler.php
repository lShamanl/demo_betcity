<?php

declare(strict_types=1);

namespace App\Profile\Application\Betcity\UseCase\Remove;

use App\Common\Service\Core\Flusher;
use App\Profile\Domain\Betcity\BetcityRepository;
use App\Profile\Domain\Betcity\Exception\BetcityNotFoundException;

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

        $this->betcityRepository->remove($betcity);
        $this->flusher->flush();

        return new Result(
            betcity: $betcity,
        );
    }
}
