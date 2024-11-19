<?php

declare(strict_types=1);

namespace App\Common\Service\Dispatcher\Message;

use App\Common\Service\Dispatcher\NamedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class MessageHandler
{
    public function __construct(
        private EventDispatcherInterface $dispatcher
    ) {
    }

    public function __invoke(Message $message): void
    {
        $event = $message->getEvent();
        $eventName = $event instanceof NamedEvent ? $event::getEventName() : $event::class;
        $this->dispatcher->dispatch(
            $event,
            $eventName
        );
    }
}
