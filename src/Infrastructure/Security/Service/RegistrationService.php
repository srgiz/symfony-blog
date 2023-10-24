<?php
declare(strict_types=1);

namespace App\Infrastructure\Security\Service;

use App\Core\Entity\User;
use App\Core\Security\Service\RegistrationServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class RegistrationService implements RegistrationServiceInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $em,
    ) {}

    public function register(string $email, #[\SensitiveParameter] string $password): void
    {
        $user = (new User())->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $this->em->persist($user);
        $this->em->flush();
    }
}
