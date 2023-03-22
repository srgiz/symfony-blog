<?php
declare(strict_types=1);

namespace App\Security\Profile;

use App\Entity\User\User;
use App\Exception\HttpException;
use App\Repository\User\UserRepository;
use App\Repository\User\UserTokenRepository;
use App\Response\JsonResponseDto;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserProfile implements UserProfileInterface
{
    public function __construct(
        private TokenCookieInterface $tokenCookie,
        private TokenStorageInterface $tokenStorage,
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private UserTokenRepository $tokenRepository,
    ) {}

    public function register(string $email, string $password): ResponseDtoInterface
    {
        $user = $this->userRepository->createNew($email, $password);
        $userToken = $this->tokenRepository->createNew($user);

        return (new JsonResponseDto())
            ->setData(true)
            ->setCookie($this->tokenCookie->create($userToken))
        ;
    }

    public function upgradePassword(string $oldPassword, string $newPassword): ResponseDtoInterface
    {
        $user = $this->tokenStorage?->getToken()?->getUser();

        if (!$user instanceof User)
            throw new HttpException(403);

        if (!$this->passwordHasher->isPasswordValid($user, $oldPassword))
            throw new HttpException(403, 'Wrong password');

        $this->userRepository->upgradePassword($user, $newPassword);
        $userToken = $this->tokenRepository->createNew($user);

        return (new JsonResponseDto())
            ->setData(true)
            ->setCookie($this->tokenCookie->create($userToken))
        ;
    }
}
