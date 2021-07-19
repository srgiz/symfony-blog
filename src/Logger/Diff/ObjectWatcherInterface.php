<?php
namespace App\Logger\Diff;

use Doctrine\Persistence\ObjectManager;

interface ObjectWatcherInterface
{
    public function getPrimaryKeys(object $object): array;

    public function setPrimaryKeys(ObjectManager $manager, object $object): void;

    public function getChangeSet(object $object): array;

    public function setChangeSet(object $object, array $changeSet): void;
}
