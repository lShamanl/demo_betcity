<?php

declare(strict_types=1);

namespace App\Common\Service\Metrics;

use Prometheus\Counter;
use Prometheus\MetricFamilySamples;

interface AdapterInterface
{
    public function createCounter(string $name, string $help = '', array $labels = []): Counter;

    /**
     * @return MetricFamilySamples[]
     */
    public function collect(): array;
}
