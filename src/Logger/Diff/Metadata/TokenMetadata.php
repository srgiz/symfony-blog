<?php
declare(strict_types=1);

namespace App\Logger\Diff\Metadata;

use App\Entity\User\Token;

class TokenMetadata extends AbstractMetadata
{
    public function genUid(object $object): array
    {
        /** @var Token $object */
        $id = $object->getId();
        $user = $object->getUser();

        return $id && $user ? [
            $this->genPartUid((string)$id),
            $this->genPartUid((string)$user->getId(), $user::class),
        ] : [];
    }
}
