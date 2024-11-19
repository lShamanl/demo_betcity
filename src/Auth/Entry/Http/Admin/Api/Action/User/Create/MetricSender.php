<?php

declare(strict_types=1);

namespace App\Auth\Entry\Http\Admin\Api\Action\User\Create;

use App\Auth\Application\User\UseCase\Create\Result;
use App\Common\Service\Metrics\AdapterInterface;

/** @codeCoverageIgnore */
readonly class MetricSender
{
    public function __construct(private AdapterInterface $metrics)
    {
    }

    public function send(Result $result): void
    {
        $metricPrefix = str_replace('.', '_', Action::NAME);

        if ($result->isSuccess()) {
            $this->metrics->createCounter(
                name: $metricPrefix . ':success',
                help: 'success'
            )->inc();
        }
        if ($result->isEmailIsBusy()) {
            $this->metrics->createCounter(
                name: $metricPrefix . ':email_is_busy',
                help: 'email is busy'
            )->inc();
        }
    }
}
