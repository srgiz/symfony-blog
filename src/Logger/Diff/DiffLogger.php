<?php
declare(strict_types=1);

namespace App\Logger\Diff;

use Psr\Log\LoggerInterface;

class DiffLogger implements DiffLoggerInterface
{
    private DiffManagerInterface $manager;

    private LoggerInterface $logger;

    public function __construct(DiffManagerInterface $manager, LoggerInterface $objLogger)
    {
        $this->manager = $manager;
        $this->logger = $objLogger;
    }

    public function log(string $event, object $object, array $changeSet): void
    {
        $metadata = $this->manager->getMetadataClass($object::class);

        if (!$metadata)
            return;

        $this->logger->info($event, [
            'objectName' => $metadata->getObjectName(),
            'relatedIds' => $metadata->getRelatedIds($object),
            'changeSet' => $changeSet,
        ]);
    }
}
