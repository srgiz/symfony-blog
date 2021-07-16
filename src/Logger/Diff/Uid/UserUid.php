<?php
declare(strict_types=1);

namespace App\Logger\Diff\Uid;

use App\Entity\User\User;

class UserUid extends AbstractUid
{
    protected function map(object $object): array
    {
        /** @var User $object */
        $id = $object->getId();
        return $id ? ['user:' . $id] : [];
    }
}
