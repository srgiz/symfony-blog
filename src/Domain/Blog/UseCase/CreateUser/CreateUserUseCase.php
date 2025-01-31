<?php

declare(strict_types=1);

namespace App\Domain\Blog\UseCase\CreateUser;

use App\Domain\Blog\Entity\Id;
use App\Domain\Blog\Repository\UserRepositoryInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

readonly class CreateUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(CreateUserCommand $command): void
    {
        $this->userRepository->create($id = new Id(), $command->email, $command->password);
        $this->eventDispatcher->dispatch(new CreateUserEvent($id, $command->email));
    }
}
