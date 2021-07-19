<?php
declare(strict_types=1);

namespace App\Logger\Diff\Metadata;

use App\Entity\User\Token;
use App\Entity\User\User;

class TokenMetadata extends ObjectMetadata
{
    public function getRelatedIds(object $object): array
    {
        /** @var Token $object */
        $ids = parent::getRelatedIds($object);

        $userMetadata = $this->getRelatedMetadata(User::class);
        $ids[$userMetadata->getObjectName()] = (string)$object->getUserId();

        return $ids;
    }
}
