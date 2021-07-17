<?php
declare(strict_types=1);

namespace App\Logger\Diff\Metadata;

use App\Entity\Blog\Post;

class PostMetadata extends AbstractMetadata
{
    public function genUid(object $object): array
    {
        /** @var Post $object */
        $id = $object->getId();
        return $id ? [$this->genPartUid((string)$id)] : [];
    }
}
