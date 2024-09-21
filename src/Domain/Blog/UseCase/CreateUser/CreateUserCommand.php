<?php

declare(strict_types=1);

namespace App\Domain\Blog\UseCase\CreateUser;

readonly class CreateUserCommand
{
    public function __construct(
        public string $email,
        #[\SensitiveParameter]
        public string $password,
    ) {
    }
}
