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

    public function getManager(): DiffManagerInterface
    {
        return $this->manager;
    }

    public function log(string $level, string $event, object $object, array $changeSet): void
    {
        $factory = $this->manager->getFactory($object);

        if (!$factory)
            return;

        $uid = $this->manager->fetchUid($object);

        if (!$uid)
            return;

        $this->logger->log($level, $event, [
            'obj' => $factory->objectName(),
            'uid' => $uid,
            'diff' => $this->prepareChangeSet($changeSet, $factory->excludedSet()),
        ]);
    }

    private function prepareChangeSet(array $changeSet, array $excludedSet): array
    {
        foreach ($excludedSet as $key)
        {
            unset($changeSet[$key]);
        }

        return $changeSet;
    }
}
