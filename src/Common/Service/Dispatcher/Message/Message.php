<?php

declare(strict_types=1);

namespace App\Common\Service\Dispatcher\Message;

class Message
{
    private readonly object $event;

    public function __construct(object $event)
    {
        $this->event = $event;
    }

    public function getEvent(): object
    {
        return $this->event;
    }
}
