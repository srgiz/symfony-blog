<?php

declare(strict_types=1);

namespace App\Domain\Blog\UseCase\CreateUser;

use App\Domain\Blog\Entity\Id;
use App\Domain\Blog\Entity\User;
use App\Domain\Blog\Repository\UserRepositoryInterface;

readonly class CreateUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(CreateUserCommand $command): void
    {
        $this->userRepository->create(new User(
            id: new Id(),
            email: $command->email
        ), $command->password);
    }
}
