<?php

declare(strict_types=1);

namespace App\Profile\Infrastructure\EventSubscriber;

use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\ToolEvents;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/** @codeCoverageIgnore */
#[AutoconfigureTag(
    name: 'doctrine.event_listener',
    attributes: ['event' => ToolEvents::postGenerateSchema],
)]
readonly class FixSequenceSchemaSubscriber
{
    /** @throws SchemaException */
    public function postGenerateSchema(GenerateSchemaEventArgs $args): void
    {
        $schema = $args->getSchema();

        $schema->createSequence('profile_betcity_id_seq');
    }
}
