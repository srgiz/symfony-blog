<?php
declare(strict_types=1);

namespace App\Logger\Diff\Factory;

use App\Entity\User\Token;

class TokenDiff extends AbstractDiffFactory
{
    protected function generateMap(object $object): array
    {
        /** @var Token $object */
        $id = $object->getId();
        $userId = $object->getUserId();

        return $id && $userId ? ['user:' . $userId, 'token:' . $id] : [];
    }
}
