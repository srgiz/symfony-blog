<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\User\UserInterface;
use App\Entity\User\UserToken;
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

    public function generate(UserInterface $user): UserToken
    {
        $key = $this->passwordHasher->hashPassword($user, $user->getPassword());

        $userToken = (new UserToken())
            ->setToken($key)
            ->setUser($user)
        ;

        $this->doctrine->getManager()->persist($userToken);
        $this->doctrine->getManager()->flush();

        return $userToken;
    }
}
