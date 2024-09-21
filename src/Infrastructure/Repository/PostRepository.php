<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Blog\Entity\Collection;
use App\Domain\Blog\Entity\Id;
use App\Domain\Blog\Entity\Post;
use App\Domain\Blog\Repository\PostRepositoryInterface;
use App\Infrastructure\Doctrine\Entity\PostData;
use App\Infrastructure\Doctrine\Repository\PostDataRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

readonly class PostRepository implements PostRepositoryInterface
{
    public function __construct(
        private PostDataRepository $postDataRepository,
    ) {
    }

    public function paginatePublic(int $page, int $limit): Collection
    {
        return $this->transformPaginator($this->postDataRepository->paginatePublic(intval(($page - 1) * $limit), $limit));
    }

    private function transformPaginator(Paginator $paginator): Collection
    {
        $posts = [];

        foreach ($paginator as $data) {
            $posts[] = $this->transformData($data);
        }

        return new Collection($posts, $paginator->count());
    }

    private function transformData(PostData $data): Post
    {
        return new Post(
            id: new Id(), // todo
            status: Post\Status::from($data->status), // todo
            title: $data->title,
            content: $data->content,
            preview: $data->preview,
        );
    }
}
