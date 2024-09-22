<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Blog\Entity\Collection;
use App\Domain\Blog\Entity\Id;
use App\Domain\Blog\Entity\Post;
use App\Domain\Blog\Repository\PostRepositoryInterface;
use App\Infrastructure\Doctrine\Entity\PostData;
use App\Infrastructure\Doctrine\Repository\PostDataRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

readonly class PostRepository implements PostRepositoryInterface
{
    public function __construct(
        private PostDataRepository $postDataRepository,
        private EntityManagerInterface $em,
    ) {
    }

    public function find(Id $id): ?Post
    {
        $data = $this->postDataRepository->find($id);

        return $data ? $this->transformData($data) : null;
    }

    public function findPublic(Id $id): ?Post
    {
        $data = $this->postDataRepository->findPublic($id);

        return $data ? $this->transformData($data) : null;
    }

    public function paginatePublic(int $page, int $limit): Collection
    {
        return $this->transformPaginator($this->postDataRepository->paginatePublic($this->getOffset($page, $limit), $limit));
    }

    public function searchPublic(string $q, int $page, int $limit): Collection
    {
        return $this->transformPaginator($this->postDataRepository->searchPublic($q, $this->getOffset($page, $limit), $limit));
    }

    public function paginateAll(int $page, int $limit): Collection
    {
        return $this->transformPaginator($this->postDataRepository->paginateAll($this->getOffset($page, $limit), $limit));
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
            id: $data->id,
            status: $data->status,
            title: $data->title,
            content: $data->content,
            preview: $data->preview,
        );
    }

    private function getOffset(int $page, int $limit): int
    {
        return intval(($page - 1) * $limit);
    }

    public function save(Post $post): void
    {
        $data = $this->postDataRepository->find($post->getId());

        if (!$data) {
            $this->em->persist($data = new PostData(
                id: $post->getId(), // todo
                title: $post->getTitle(),
                content: $post->getContent(),
            ));
        }

        $data->status = $post->getStatus();
        $data->title = $post->getTitle();
        $data->preview = $post->getPreview();
        $data->content = $post->getContent();

        $this->em->flush();
    }

    public function delete(Id $id): void
    {
        $data = $this->postDataRepository->find($id);

        if (!$data) {
            return;
        }

        $this->em->remove($data);
        $this->em->flush();
    }
}
