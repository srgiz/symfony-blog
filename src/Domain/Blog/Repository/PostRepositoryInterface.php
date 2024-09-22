<?php

declare(strict_types=1);

namespace App\Domain\Blog\Repository;

use App\Domain\Blog\Entity\Collection;
use App\Domain\Blog\Entity\Id;
use App\Domain\Blog\Entity\Post;

interface PostRepositoryInterface
{
    public function find(Id $id): ?Post;

    public function findPublic(Id $id): ?Post;

    public function paginatePublic(int $page, int $limit): Collection;

    public function searchPublic(string $q, int $page, int $limit): Collection;

    public function paginateAll(int $page, int $limit): Collection;

    public function save(Post $post): void;

    public function delete(Id $id): void;
}
