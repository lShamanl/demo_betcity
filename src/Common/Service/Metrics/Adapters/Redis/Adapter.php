<?php

declare(strict_types=1);

namespace App\Common\Service\Metrics\Adapters\Redis;

use App\Common\Service\Metrics\AdapterInterface;
use Prometheus\Counter;

class Adapter implements AdapterInterface
{
    private readonly \Prometheus\Storage\Redis $adapter;

    public function __construct(
        private readonly Config $config
    ) {
        $this->adapter = new \Prometheus\Storage\Redis([
            'host' => $config->host,
        ]);
    }

    public function collect(): array
    {
        return $this->adapter->collect();
    }

    public function createCounter(
        string $name,
        string $help = '',
        array $labels = []
    ): Counter {
        return new Counter(
            $this->adapter,
            $this->config->namespace,
            $name,
            $help,
            $labels
        );
    }
}
