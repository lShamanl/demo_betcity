<?php

declare(strict_types=1);

namespace App\Common\RateLimiter\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class RateLimiting
{
    public function __construct(
        public string $limiter,
    ) {
    }
}
