<?php
declare(strict_types=1);

namespace App\Logger\Diff\Metadata;

use App\Logger\Diff\DiffManagerInterface;

class ObjectMetadata implements MetadataInterface
{
    private DiffManagerInterface $manager;

    private string $objectName;

    private array $excludedSet;

    public function __construct(DiffManagerInterface $manager, string $objectName, array $excludedSet = [])
    {
        $this->manager = $manager;
        $this->objectName = $objectName;
        $this->excludedSet = $excludedSet;
    }

    public function getObjectName(): string
    {
        return $this->objectName;
    }

    public function getExcludedSet(): array
    {
        return $this->excludedSet;
    }

    public function getRelatedIds(object $object): array
    {
        if (!method_exists($object, 'getId'))
            return [];

        return [
            $this->getObjectName() => (string)$object->getId(),
        ];
    }

    protected function getRelatedMetadata(string $className): MetadataInterface
    {
        return $this->manager->getMetadataClass($className);
    }
}
