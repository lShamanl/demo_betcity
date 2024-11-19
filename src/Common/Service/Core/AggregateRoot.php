<?php

declare(strict_types=1);

namespace App\Common\Service\Core;

interface AggregateRoot
{
    public function releaseAllEvents(): array;

    public function releasePreFlushEvents(): array;

    public function releasePostFlushEvents(): array;
}
