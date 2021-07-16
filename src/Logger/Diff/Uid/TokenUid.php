<?php
declare(strict_types=1);

namespace App\Logger\Diff\Uid;

use App\Entity\User\Token;

class TokenUid extends AbstractUid
{
    protected function map(object $object): array
    {
        /** @var Token $object */
        $id = $object->getId();
        $userId = $object->getUserId();

        return $id && $userId ? ['user:' . $userId, 'token:' . $id] : [];
    }
}
