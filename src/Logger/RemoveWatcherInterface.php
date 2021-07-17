<?php
namespace App\Logger;

use Doctrine\Persistence\ObjectManager;

interface RemoveWatcherInterface
{
    public function getPrimaryKeys(object $object): array;

    public function setPrimaryKeys(ObjectManager $manager, object $object): void;
}
