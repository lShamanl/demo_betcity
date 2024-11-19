<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\EventSubscriber;

use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/** @codeCoverageIgnore */
#[AutoconfigureTag(
    name: 'doctrine.event_listener',
    attributes: ['event' => 'postGenerateSchema'],
)]
readonly class FixSequenceSchemaSubscriber
{
    /** @throws SchemaException */
    public function postGenerateSchema(GenerateSchemaEventArgs $args): void
    {
        $schema = $args->getSchema();

        $schema->createSequence('auth_user_id_seq');
    }
}
