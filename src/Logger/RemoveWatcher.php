<?php
declare(strict_types=1);

namespace App\Logger;

use Doctrine\Persistence\ObjectManager;
use WeakMap;

class RemoveWatcher implements RemoveWatcherInterface
{
    private WeakMap $weakMap;

    public function __construct()
    {
        $this->weakMap = new WeakMap();
    }

    public function getPrimaryKeys(object $object): array
    {
        return $this->weakMap[$object] ?? [];
    }

    public function setPrimaryKeys(ObjectManager $manager, object $object): void
    {
        $this->weakMap[$object] = $manager->getClassMetadata($object::class)->getIdentifierValues($object);
    }
}
