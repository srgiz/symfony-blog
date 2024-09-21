<?php

declare(strict_types=1);

namespace App\Domain\Blog\Entity;

final readonly class User
{
    public function __construct(
        private Id $id,
        private string $email,
    ) {
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
