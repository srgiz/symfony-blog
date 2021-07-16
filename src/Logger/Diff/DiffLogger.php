<?php
declare(strict_types=1);

namespace App\Logger\Diff;

use App\Logger\Diff\Uid\UidInterface;
use Psr\Log\LoggerInterface;
use ReflectionAttribute;
use ReflectionClass;
use stdClass;
use WeakMap;

class DiffLogger implements DiffLoggerInterface
{
    private WeakMap $weakMap;

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $objLogger)
    {
        $this->weakMap = new WeakMap();
        $this->logger = $objLogger;
    }

    public function watch(object $object): void
    {
        $this->observe($object);
    }

    public function log(string $level, string $event, object $object, array $changeSet): void
    {
        $data = $this->observe($object);
        $attribute = $data->attribute ?? null;

        if (!$attribute)
            return;

        $this->logger->log($level, $event, [
            //'obj' => $this->fetchName($object, $attribute),
            'uid' => $data->uid ?? $this->fetchUid($object, $attribute),
            'diff' => $this->prepareChangeSet($changeSet, $attribute),
        ]);
    }

    private function observe(object $object): stdClass
    {
        if (isset($this->weakMap[$object]))
            return $this->weakMap[$object];

        $data = new stdClass();
        $attribute = (new ReflectionClass($object))->getAttributes(DiffLog::class)[0] ?? null;

        if ($attribute)
        {
            $data->attribute = $attribute;
            $data->uid = $this->fetchUid($object, $attribute);
        }

        return $this->weakMap[$object] = $data;
    }

    private function fetchName(object $object, ReflectionAttribute $attribute): string
    {
        $name = trim($attribute->getArguments()['name'] ?? '');

        if (!empty($name))
            return $name;

        $name = explode('\\', $object::class);
        return end($name);
    }

    private function fetchUid(object $object, ReflectionAttribute $attribute): mixed
    {
        $className = $attribute->getArguments()['uidClass'];

        /** @var UidInterface $handler */
        $handler = new $className();
        return $handler->uid($object);
    }

    private function prepareChangeSet(array $changeSet, ReflectionAttribute $attribute): array
    {
        $exclude = $attribute->getArguments()['exclude'] ?? [];

        foreach ($exclude as $key)
        {
            unset($changeSet[$key]);
        }

        return $changeSet;
    }
}
