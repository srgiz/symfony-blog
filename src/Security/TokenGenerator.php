<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\User\Token;
use App\Entity\User\UserInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TokenGenerator implements TokenGeneratorInterface
{
    private UserPasswordHasherInterface $passwordHasher;

    private ManagerRegistry $doctrine;

    public function __construct(UserPasswordHasherInterface $passwordHasher, ManagerRegistry $doctrine)
    {
        $this->passwordHasher = $passwordHasher;
        $this->doctrine = $doctrine;
    }

    public function generate(UserInterface $user): Token
    {
        $key = $this->passwordHasher->hashPassword($user, $user->getPassword());

        $token = (new Token())
            ->setKey($key)
            ->setUser($user);

        $this->doctrine->getManager()->persist($token);
        $this->doctrine->getManager()->flush();

        return $token;
    }
}
