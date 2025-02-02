<?php

declare(strict_types=1);

namespace App\Domain\Blog\Repository;

use App\Domain\Blog\Entity\Id;
use App\Domain\Blog\Entity\User;

interface UserRepositoryInterface
{
    public function findByToken(#[\SensitiveParameter] string $token): ?User;

    public function findByEmail(string $email): ?User;

    public function create(Id $id, string $email, #[\SensitiveParameter] string $plainPassword): void;

    public function addToken(User $user, #[\SensitiveParameter] string $token): void;
}
