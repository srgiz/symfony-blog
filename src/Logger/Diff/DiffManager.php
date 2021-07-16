<?php
declare(strict_types=1);

namespace App\Logger\Diff;

use App\Logger\Diff\Factory\DiffFactoryInterface;
use ReflectionAttribute;
use ReflectionClass;
use stdClass;
use WeakMap;

class DiffManager implements DiffManagerInterface
{
    private WeakMap $objects;

    private WeakMap $factories;

    public function __construct()
    {
        $this->objects = new WeakMap();
        $this->factories = new WeakMap();
    }

    public function watch(object $object): void
    {
        $this->observe($object);
    }

    public function unwatch(object $object): void
    {
        unset($this->objects[$object]);
    }

    public function fetchUid(object $object): array
    {
        $data = $this->observe($object);
        $uid = $data->uid ?? null;

        if ($uid)
            return $uid;

        /** @var DiffFactoryInterface $factory */
        $factory = $data->factory ?? null;
        return $factory ? $factory->generateUid($object) : [];
    }

    public function getFactory(object $object): ?DiffFactoryInterface
    {
        $data = $this->observe($object);
        return $data->factory ?? null;
    }

    private function observe(object $object): stdClass
    {
        if (isset($this->objects[$object]))
            return $this->objects[$object];

        $data = new stdClass();
        $attribute = (new ReflectionClass($object))->getAttributes(DiffLog::class)[0] ?? null;

        if ($attribute)
        {
            $data->factory = $this->getOrSetUidFactory($object, $attribute);
            $data->uid = $data->factory->generateUid($object);
        }

        return $this->objects[$object] = $data;
    }

    private function getOrSetUidFactory(object $object, ReflectionAttribute $attribute): DiffFactoryInterface
    {
        $factoryClass = $attribute->getArguments()['factoryClass'];

        /** @var DiffFactoryInterface $factory */
        foreach ($this->factories as $factory => $item)
        {
            if ($factory instanceof $factoryClass)
                return $factory;
        }

        $factory = new $factoryClass(
            objectName: $this->fetchName($object, $attribute),
            excludedSet: $attribute->getArguments()['exclude'] ?? []
        );

        $this->factories[$factory] = true;
        return $factory;
    }

    private function fetchName(object $object, ReflectionAttribute $attribute): string
    {
        $name = trim($attribute->getArguments()['name'] ?? '');

        if (!empty($name))
            return $name;

        $name = explode('\\', $object::class);
        return end($name);
    }
}
