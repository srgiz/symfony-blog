<?php
declare(strict_types=1);

namespace App\Blog\Entity;

use App\Blog\Enum\StatusEnum;
use App\Blog\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[Column(type: 'integer')]
    public ?int $id = null;

    #[Column(type: 'enum_status', options: ['default' => StatusEnum::Draft->value])]
    public ?string $status = null;

    #[Column(type: 'string', length: 32)]
    public ?string $slug = null;

    #[Column(type: 'string', length: 120)]
    public ?string $title = null;

    #[Column(type: 'text', nullable: true)]
    public ?string $preview = null;

    #[Column(type: 'text')]
    public ?string $content = null;
}
