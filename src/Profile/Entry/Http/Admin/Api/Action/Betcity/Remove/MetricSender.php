<?php

declare(strict_types=1);

namespace App\Profile\Entry\Http\Admin\Api\Action\Betcity\Remove;

use App\Common\Exception\Domain\DomainException;
use App\Common\Service\Metrics\AdapterInterface;
use App\Profile\Application\Betcity\UseCase\Remove\Result;
use App\Profile\Domain\Betcity\Exception\BetcityNotFoundException;

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
        if ($exception instanceof BetcityNotFoundException) {
            $this->metrics->createCounter(
                name: $metricPrefix . ':betcity_not_found',
                help: 'Betcity not found'
            )->inc();
        }
    }
}
