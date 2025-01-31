<?php

declare(strict_types=1);

namespace App\Domain\Blog\UseCase\CreateUser;

use App\Domain\Blog\Entity\Id;

readonly class CreateUserEvent
{
    public function __construct(
        public Id $id,
        public string $email,
    ) {
    }
}
