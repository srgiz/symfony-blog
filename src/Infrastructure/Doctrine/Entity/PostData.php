<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use App\Domain\Blog\Entity\Id;
use App\Domain\Blog\Entity\Post\Status;
use App\Infrastructure\Doctrine\Repository\PostDataRepository;
use App\Infrastructure\Doctrine\Types\IdType;
use App\Infrastructure\Doctrine\Types\PostStatusType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostDataRepository::class)]
#[ORM\Table('post')]
class PostData
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: IdType::NAME)]
        public readonly Id $id,
        #[ORM\Column(type: 'string', length: 120)]
        public string $title,
        #[ORM\Column(type: 'text')]
        public string $content,
        #[ORM\Column(type: 'text', nullable: true)]
        public ?string $preview = null,
        #[ORM\Column(type: PostStatusType::NAME, enumType: Status::class)]
        public Status $status = Status::Draft,
    ) {
    }
}
