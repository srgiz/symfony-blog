<?php

declare(strict_types=1);

namespace App\Domain\Blog\Entity;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class User implements UserInterface, PasswordAuthenticatedUserInterface // todo: del interfaces
{
    public function __construct(
        private Id $id,
        private string $email,
        private string $password,
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

    #[\Override]
    public function getPassword(): string
    {
        return $this->password;
    }

    #[\Override]
    public function getRoles(): array
    {
        return ['ROLE_ADMIN'];
    }

    #[\Override]
    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    #[\Override]
    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }
}
