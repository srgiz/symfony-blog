<?php
declare(strict_types=1);

namespace App\Blog\Repository;

use App\Blog\Entity\Post;
use App\Blog\Enum\StatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findPublic(string $slug): ?Post
    {
        return $this->findOneBy(['slug' => $slug, 'status' => StatusEnum::Active->value]);
    }
}
