<?php
declare(strict_types=1);

namespace App\Core\Entity;

use App\Core\Blog\Enum\StatusEnum;
use App\Core\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[UniqueEntity('slug')]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    public ?int $id = null;

    #[ORM\Column(type: 'enum_status', options: ['default' => StatusEnum::Draft->value])]
    #[Assert\Choice([StatusEnum::Draft->value, StatusEnum::Active->value])]
    public ?string $status = null;

    #[ORM\Column(type: 'string', length: 32)]
    #[Assert\NotBlank]
    public ?string $slug = null;

    #[ORM\Column(type: 'string', length: 120)]
    #[Assert\NotBlank]
    public ?string $title = null;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $preview = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    public ?string $content = null;
}
