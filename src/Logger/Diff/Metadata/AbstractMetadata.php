<?php
declare(strict_types=1);

namespace App\Logger\Diff\Metadata;

use App\Logger\Diff\DiffManagerInterface;
use App\Logger\Diff\Exception\MetadataException;
use Doctrine\Common\Util\ClassUtils;

abstract class AbstractMetadata implements MetadataInterface
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

    public function getObjectName(): string
    {
        return $this->objectName;
    }

    public function getExcludedSet(): array
    {
        return $this->excludedSet;
    }

    protected function genPartUid(string $id, string $className = null): string
    {
        $objectName = $this->getObjectName();

        if ($className !== null)
        {
            $metadata = $this->getManager()->getMetadataClass(ClassUtils::getRealClass($className));

            if (!$metadata)
                throw new MetadataException('Metadata not found');

            $objectName = $metadata->getObjectName();
        }

        return $objectName . ':' . $id;
    }
}
