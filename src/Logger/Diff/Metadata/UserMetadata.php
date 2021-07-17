<?php
declare(strict_types=1);

namespace App\Logger\Diff\Metadata;

use App\Entity\User\User;

class UserMetadata extends AbstractMetadata
{
    public function genUid(object $object): array
    {
        /** @var User $object */
        $id = $object->getId();
        return $id ? [$this->genPartUid((string)$id)] : [];
    }
}
