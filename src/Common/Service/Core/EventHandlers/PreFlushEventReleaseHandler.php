<?php

declare(strict_types=1);

namespace App\Common\Service\Core\EventHandlers;

use App\Common\Service\Core\AggregateRoot;
use App\Common\Service\Core\EventDispatcher;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(
    name: 'doctrine.event_listener',
    attributes: [
        'event' => Events::preFlush,
    ]
)]
readonly class PreFlushEventReleaseHandler
{
    public function __construct(
        private EventDispatcher $dispatcher,
    ) {
    }

    public function preFlush(PreFlushEventArgs $args): void
    {
        $objectManager = $args->getObjectManager();
        $unitOfWork = $objectManager->getUnitOfWork();

        foreach ($unitOfWork->getIdentityMap() as $entityScope) {
            foreach ($entityScope as $entity) {
                if ($entity instanceof AggregateRoot) {
                    $this->dispatcher->dispatch($entity->releasePreFlushEvents());
                }
            }
        }
    }
}
