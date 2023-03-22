<?php
declare(strict_types=1);

namespace App\Security\Profile;

use App\EventSubscriber\KernelResponseHeadersSubscriber;
use App\Exception\HttpException;
use App\Response\JsonResponseDto;
use App\Security\Entity\User;
use App\Security\Repository\UserTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

readonly class CurrentProfile
{
    public function __construct(
        private EntityManagerInterface $em,
        private TokenCookie $tokenCookie,
        private TokenStorageInterface $tokenStorage,
        private UserPasswordHasherInterface $passwordHasher,
        private UserTokenRepository $tokenRepository,
        private KernelResponseHeadersSubscriber $headers
    ) {}

    public function register(string $email, string $password): JsonResponseDto
    {
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->em->persist($user);
        $this->em->flush();

        $userToken = $this->tokenRepository->createNew($user);
        $this->headers->setCookie($this->tokenCookie->create($userToken));

        return new JsonResponseDto(null);
    }

    public function upgradePassword(string $oldPassword, string $newPassword): JsonResponseDto
    {
        $user = $this->tokenStorage?->getToken()?->getUser();

        if (!$user instanceof User) {
            throw new HttpException(403);
        }

        if (!$this->passwordHasher->isPasswordValid($user, $oldPassword)) {
            throw new HttpException(403, 'Wrong password');
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
        $this->em->persist($user);
        $this->em->flush();

        $userToken = $this->tokenRepository->createNew($user);
        $this->headers->setCookie($this->tokenCookie->create($userToken));

        return new JsonResponseDto(null);
    }
}
