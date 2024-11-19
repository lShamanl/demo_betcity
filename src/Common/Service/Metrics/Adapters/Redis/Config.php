<?php

declare(strict_types=1);

namespace App\Common\Service\Metrics\Adapters\Redis;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class Config
{
    public function __construct(
        #[Autowire('%env(METRICS_SIDECAR_HOST)%')]
        public string $host,
        #[Autowire('%env(METRICS_NAMESPACE)%')]
        public string $namespace,
    ) {
    }
}
