<?php
declare(strict_types=1);

namespace App\Doctrine\EventListener;

use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\ToolEvents;

class MakeMigrationEventSubscriber implements DoctrineEventSubscriber
{
    public function postGenerateSchema(GenerateSchemaEventArgs $event): void
    {
        $this->fixDownPublicSchema($event);
    }

    /**
     * @link https://github.com/doctrine/dbal/issues/1110
     * @link https://www.doctrine-project.org/projects/doctrine-orm/en/2.11/reference/events.html#postgenerateschema
     */
    private function fixDownPublicSchema(GenerateSchemaEventArgs $event): void
    {
        $schema = $event->getSchema();

        if (!$schema->hasNamespace('public')) {
            $schema->createNamespace('public');
        }
    }

    public function getSubscribedEvents(): array
    {
        return [
            ToolEvents::postGenerateSchema,
        ];
    }
}
