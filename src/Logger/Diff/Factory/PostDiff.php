<?php
declare(strict_types=1);

namespace App\Logger\Diff\Factory;

use App\Entity\Blog\Post;

class PostDiff extends AbstractDiffFactory
{
    public function generateUid(object $object): array
    {
        /** @var Post $object */
        $id = $object->getId();
        return $id ? [$this->generatePartUid((string)$id)] : [];
    }
}
