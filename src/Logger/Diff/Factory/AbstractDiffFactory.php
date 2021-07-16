<?php
declare(strict_types=1);

namespace App\Logger\Diff\Factory;

use App\Logger\Diff\DiffManagerInterface;

abstract class AbstractDiffFactory implements DiffFactoryInterface
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

    public function getManager(): DiffManagerInterface
    {
        return $this->manager;
    }

    public function objectName(): string
    {
        return $this->objectName;
    }

    public function excludedSet(): array
    {
        return $this->excludedSet;
    }

    // todo: возможно все это надо переделать на выборку фабрики по имени класса
    protected function generatePartUid(string $id, object $object = null): string
    {
        $objectName = $this->objectName();

        if ($object !== null)
        {
            $objectName = $this->getManager()->getFactory($object)->objectName();
        }

        return $objectName . ':' . $id;
    }
}
