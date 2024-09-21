<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use App\Domain\Blog\Entity\Post\Status;
use App\Infrastructure\Doctrine\Repository\PostDataRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PostDataRepository::class)]
#[ORM\Table('post')]
#[UniqueEntity('slug')]
class PostData
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    public ?int $id = null;

    #[ORM\Column(type: 'enum_status', options: ['default' => Status::Draft->value])]
    #[Assert\Choice([Status::Draft->value, Status::Active->value])]
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
