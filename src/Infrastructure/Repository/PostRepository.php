<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Blog\Entity\Collection;
use App\Domain\Blog\Entity\Id;
use App\Domain\Blog\Entity\Post;
use App\Domain\Blog\Entity\Post\Status;
use App\Domain\Blog\Repository\PostRepositoryInterface;
use App\Infrastructure\Doctrine\Paginator;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

readonly class PostRepository implements PostRepositoryInterface
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function find(Id $id): ?Post
    {
        $queryBuilder = $this->connection->createQueryBuilder()->from('post', 'p')
            ->select('p.*')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id);

        $data = $queryBuilder->executeQuery()->fetchAssociative();

        return $data ? $this->transformData($data) : null;
    }

    public function findPublic(Id $id): ?Post
    {
        $queryBuilder = $this->connection->createQueryBuilder()->from('post', 'p')
            ->select('p.*')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id);

        $this->wherePublic($queryBuilder);
        $data = $queryBuilder->executeQuery()->fetchAssociative();

        return $data ? $this->transformData($data) : null;
    }

    private function wherePublic(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->andWhere('p.status = :status')->setParameter('status', Status::Active->value);
    }

    public function paginatePublic(int $page, int $limit): Collection
    {
        $queryBuilder = $this->connection->createQueryBuilder()->from('post', 'p')
            ->select('p.*')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($this->getOffset($page, $limit));

        $this->wherePublic($queryBuilder);
        $paginator = new Paginator($this->connection, $queryBuilder);

        return $this->transformPaginator($paginator);
    }

    public function searchPublic(string $q, int $page, int $limit): Collection
    {
        $queryBuilder = $this->connection->createQueryBuilder()->from('post', 'p')
            ->select('p.*')
            ->orderBy('p.id', 'DESC') // todo: order weight
            ->andWhere("(to_tsvector('russian', p.title || p.content || COALESCE(p.preview, '')) @@ plainto_tsquery('russian', :q))")
            ->setParameter('q', $q) // todo: test search
            ->setMaxResults($limit)
            ->setFirstResult($this->getOffset($page, $limit));

        $this->wherePublic($queryBuilder);
        $paginator = new Paginator($this->connection, $queryBuilder);

        return $this->transformPaginator($paginator);
    }

    public function paginateAll(int $page, int $limit): Collection
    {
        $queryBuilder = $this->connection->createQueryBuilder()->from('post', 'p')
            ->select('p.*')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($this->getOffset($page, $limit));

        $paginator = new Paginator($this->connection, $queryBuilder);

        return $this->transformPaginator($paginator);
    }

    private function transformPaginator(Paginator $paginator): Collection
    {
        $posts = [];

        foreach ($paginator->get() as $data) {
            $posts[] = $this->transformData($data);
        }

        return new Collection($posts, $paginator->total());
    }

    private function transformData(array $data): Post
    {
        return new Post(
            id: new Id($data['id']),
            status: Status::from($data['status']),
            title: $data['title'],
            content: $data['content'],
            preview: $data['preview'],
        );
    }

    private function getOffset(int $page, int $limit): int
    {
        return intval(($page - 1) * $limit);
    }

    public function save(Post $post): void
    {
        $sql = 'INSERT INTO post (id, status, title, preview, content)
        VALUES (:id, :status, :title, :preview, :content)
        ON CONFLICT (id) DO UPDATE SET 
            status = EXCLUDED.status, title = EXCLUDED.title, preview = EXCLUDED.preview, content = EXCLUDED.content';

        $this->connection->executeStatement($sql, [
            'id' => $post->getId(),
            'status' => $post->getStatus()->value,
            'title' => $post->getTitle(),
            'preview' => $post->getPreview(),
            'content' => $post->getContent(),
        ]);
    }

    public function delete(Id $id): void
    {
        $this->connection->createQueryBuilder()->delete('post')->where('id = :id')->setParameter('id', $id)->executeStatement();
    }
}
