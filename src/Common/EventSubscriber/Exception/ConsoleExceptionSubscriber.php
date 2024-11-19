<?php

declare(strict_types=1);

namespace App\Common\EventSubscriber\Exception;

use App\Common\Exception\Domain\DomainException;
use App\Common\Service\Metrics\AdapterInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Throwable;

#[AsEventListener(event: ConsoleEvents::ERROR, method: 'logException', priority: 1)]
class ConsoleExceptionSubscriber
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly AdapterInterface $metrics,
    ) {
    }

    public function logException(ConsoleErrorEvent $event): void
    {
        $exception = $event->getError();
        $this->metrics->createCounter(
            name: 'error:console',
            help: 'error in console'
        )->inc();
        try {
            throw $exception;
        } catch (DomainException $exception) {
            $this->logger->warning($exception->getMessage());
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage());
        }
    }
}
