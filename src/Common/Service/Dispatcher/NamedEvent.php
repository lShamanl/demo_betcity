<?php

declare(strict_types=1);

namespace App\Common\Service\Dispatcher;

interface NamedEvent
{
    public static function getEventName(): string;
}
