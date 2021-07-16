<?php
declare(strict_types=1);

namespace App\Logger\Diff\Factory;

use App\Entity\User\User;

class UserDiff extends AbstractDiffFactory
{
    public function generateUid(object $object): array
    {
        /** @var User $object */
        $id = $object->getId();
        return $id ? [$this->generatePartUid((string)$id)] : [];
    }
}
