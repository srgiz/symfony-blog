<?php
declare(strict_types=1);

namespace App\Logger\Diff\Factory;

use App\Entity\Blog\Post;

class PostDiff extends AbstractDiffFactory
{
    protected function generateMap(object $object): array
    {
        /** @var Post $object */
        $id = $object->getId();
        return $id ? ['post:' . $id] : [];
    }
}
