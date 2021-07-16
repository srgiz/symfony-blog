<?php
declare(strict_types=1);

namespace App\Logger\Diff\Factory;

abstract class AbstractDiffFactory implements DiffFactoryInterface
{
    private string $objectName;

    private array $excludedSet;

    public function __construct(string $objectName, array $excludedSet = [])
    {
        $this->objectName = $objectName;
        $this->excludedSet = $excludedSet;
    }

    public function objectName(): string
    {
        return $this->objectName;
    }

    public function excludedSet(): array
    {
        return $this->excludedSet;
    }

    final public function generateUid(object $object): array
    {
        $map = $this->generateMap($object);
        sort($map);
        return $map;
    }

    abstract protected function generateMap(object $object): array;
}
