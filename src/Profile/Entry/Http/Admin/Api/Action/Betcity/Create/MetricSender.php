<?php

declare(strict_types=1);

namespace App\Profile\Entry\Http\Admin\Api\Action\Betcity\Create;

use App\Common\Exception\Domain\DomainException;
use App\Common\Service\Metrics\AdapterInterface;
use App\Profile\Application\Betcity\UseCase\Create\Result;
use App\Profile\Domain\Betcity\Exception\BetcityUserIdAlreadyTakenException;

/** @codeCoverageIgnore */
readonly class MetricSender
{
    public function __construct(private AdapterInterface $metrics)
    {
    }

    public function send(
        ?Result $result,
        ?DomainException $exception,
    ): void {
        $metricPrefix = str_replace('.', '_', Action::NAME);

        if (null !== $result) {
            $this->metrics->createCounter(
                name: $metricPrefix . ':success',
                help: 'Success'
            )->inc();
        }
        if ($exception instanceof BetcityUserIdAlreadyTakenException) {
            $this->metrics->createCounter(
                name: $metricPrefix . ':user_id_already_taken',
                help: 'UserId already taken'
            )->inc();
        }
    }
}
