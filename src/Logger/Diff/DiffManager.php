<?php
declare(strict_types=1);

namespace App\Logger\Diff;

use App\Logger\Diff\Metadata\MetadataInterface;

use Doctrine\Common\Util\ClassUtils;
use ReflectionClass;
use ReflectionException;

class DiffManager implements DiffManagerInterface
{
    private array $cache = [];

    public function getMetadataClass(string $className): ?MetadataInterface
    {
        $className = ClassUtils::getRealClass($className);

        if (isset($this->cache[$className]))
            return $this->cache[$className];

        try
        {
            $attribute = (new ReflectionClass($className))->getAttributes(DiffLog::class)[0] ?? null;
            $metadata = null;

            if ($attribute)
            {
                $metadataClass = $attribute->getArguments()['metadataClass'];

                $metadata = new $metadataClass(
                    manager: $this,
                    objectName: $attribute->getArguments()['name'] ?? $this->getObjectName($className),
                    excludedSet: $attribute->getArguments()['exclude'] ?? []
                );
            }

            return $this->cache[$className] = $metadata;
        }
        catch (ReflectionException)
        {
            return null;
        }
    }

    private function getObjectName(string $className): string
    {
        $name = explode('\\', $className);
        return end($name);
    }

    public function clear(): void
    {
        $this->cache = [];
    }
}
