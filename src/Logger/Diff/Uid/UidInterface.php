<?php
namespace App\Logger\Diff\Uid;

interface UidInterface
{
    /**
     * @param object $object
     * @return string|null "entity1:id,entity2:id"
     */
    public function uid(object $object): ?string;
}
