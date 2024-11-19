<?php

declare(strict_types=1);

namespace App\Common\Service\Dispatcher;

use App\Common\Service\Core\EventDispatcher;
use App\Common\Service\Dispatcher\Message\Message;
use Symfony\Component\Messenger\MessageBusInterface;

class MessengerEventDispatcher implements EventDispatcher
{
    public function __construct(
        private readonly MessageBusInterface $bus
    ) {
    }

    public function dispatch(array $events): void
    {
        foreach ($events as $event) {
            $this->bus->dispatch(new Message($event));
        }
    }
}
