<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Blog\Entity\User;
use App\Domain\Blog\Repository\UserRepositoryInterface;
use App\Infrastructure\Doctrine\Entity\UserData;
//use App\Infrastructure\Doctrine\Repository\UserDataRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        //private UserDataRepository $userDataRepository,
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function findByToken(string $token): ?User
    {
        // TODO: Implement findByToken() method.
        return null;
    }

    private function transformData(UserData $data): User
    {
        return new User(
            id: $data->getId(),
            email: $data->getUserIdentifier()
        );
    }

    public function create(User $user, #[\SensitiveParameter] string $plainPassword): void
    {
        $userData = new UserData(
            id: $user->getId(),
            email: $user->getEmail(),
        );

        $userData->setPassword($this->passwordHasher->hashPassword($userData, $plainPassword));

        $this->em->persist($userData);
        $this->em->flush();
    }
}
