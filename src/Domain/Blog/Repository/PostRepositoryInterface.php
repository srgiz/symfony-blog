<?php

declare(strict_types=1);

namespace App\Domain\Blog\Repository;

use App\Domain\Blog\Entity\Collection;

interface PostRepositoryInterface
{
    public function paginatePublic(int $page, int $limit): Collection;
}
