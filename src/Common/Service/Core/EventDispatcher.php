<?php

declare(strict_types=1);

namespace App\Common\Service\Core;

interface EventDispatcher
{
    public function dispatch(array $events): void;
}
