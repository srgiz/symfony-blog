<?php
declare(strict_types=1);

namespace App\Logger\Diff\Uid;

use App\Entity\Blog\Post;

class PostUid extends AbstractUid
{
    protected function map(object $object): array
    {
        /** @var Post $object */
        $id = $object->getId();
        return $id ? ['post:' . $id] : [];
    }
}
