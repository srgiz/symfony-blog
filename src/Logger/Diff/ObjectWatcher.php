<?php
declare(strict_types=1);

namespace App\Logger\Diff;

use Doctrine\Persistence\ObjectManager;
use WeakMap;

class ObjectWatcher implements ObjectWatcherInterface
{
    private WeakMap $primaryKeys;

    private WeakMap $updateSets;

    public function __construct()
    {
        $this->primaryKeys = new WeakMap();
        $this->updateSets = new WeakMap();
    }

    public function getPrimaryKeys(object $object): array
    {
        return $this->primaryKeys[$object] ?? [];
    }

    public function setPrimaryKeys(ObjectManager $manager, object $object): void
    {
        $this->primaryKeys[$object] = $manager->getClassMetadata($object::class)->getIdentifierValues($object);
    }

    public function getChangeSet(object $object): array
    {
        return $this->updateSets[$object] ?? [];
    }

    public function setChangeSet(object $object, array $changeSet): void
    {
        $this->updateSets[$object] = $changeSet;
    }
}
