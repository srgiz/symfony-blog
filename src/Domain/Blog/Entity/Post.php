<?php

declare(strict_types=1);

namespace App\Domain\Blog\Entity;

use App\Domain\Blog\Entity\Post\Status;

final class Post
{
    public function __construct(
        readonly private Id $id,
        private Status $status,
        private string $title,
        private string $content,
        private ?string $preview = null,
    ) {
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getPreview(): ?string
    {
        return $this->preview;
    }

    public function setPreview(?string $preview): void
    {
        $this->preview = $preview;
    }
}
