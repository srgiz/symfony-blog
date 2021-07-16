<?php
declare(strict_types=1);

namespace App\Logger\Diff\Factory;

use App\Entity\User\Token;

class TokenDiff extends AbstractDiffFactory
{
    public function generateUid(object $object): array
    {
        /** @var Token $object */
        $id = $object->getId();
        $user = $object->getUser();

        return $id && $user ? [
            $this->generatePartUid((string)$id),
            $this->generatePartUid((string)$user->getId(), $user),
        ] : [];
    }
}
