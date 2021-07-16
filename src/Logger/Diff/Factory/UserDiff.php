<?php
declare(strict_types=1);

namespace App\Logger\Diff\Factory;

use App\Entity\User\User;

class UserDiff extends AbstractDiffFactory
{
    protected function generateMap(object $object): array
    {
        /** @var User $object */
        $id = $object->getId();
        return $id ? ['user:' . $id] : [];
    }
}
