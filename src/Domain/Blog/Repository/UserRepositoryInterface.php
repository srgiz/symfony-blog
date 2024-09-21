<?php

declare(strict_types=1);

namespace App\Domain\Blog\Repository;

use App\Domain\Blog\Entity\User;

interface UserRepositoryInterface
{
    public function findByToken(string $token): ?User;

    public function create(User $user, #[\SensitiveParameter] string $plainPassword): void;
}
