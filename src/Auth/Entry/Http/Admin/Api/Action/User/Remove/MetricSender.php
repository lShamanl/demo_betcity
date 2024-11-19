<?php

declare(strict_types=1);

namespace App\Auth\Entry\Http\Admin\Api\Action\User\Remove;

use App\Auth\Application\User\UseCase\Remove\Result;
use App\Common\Service\Metrics\AdapterInterface;

/** @codeCoverageIgnore */
class MetricSender
{
    public function __construct(private readonly AdapterInterface $metrics)
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
        if ($result->isUserNotExists()) {
            $this->metrics->createCounter(
                name: $metricPrefix . ':user_not_exists',
                help: 'user not exists'
            )->inc();
        }
    }
}
