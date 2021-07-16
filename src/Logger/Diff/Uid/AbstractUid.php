<?php
namespace App\Logger\Diff\Uid;

abstract class AbstractUid implements UidInterface
{
    final public function uid(object $object): ?string
    {
        $map = $this->map($object);

        if (empty($map))
            return null;

        sort($map);
        return implode(',', $map);
    }

    /**
     * @return array<string> entity:id
     */
    abstract protected function map(object $object): array;
}
