<?php
declare(strict_types=1);

namespace App\Security\Profile;

use App\Dto\Response\ResponseDto;
use App\Dto\Response\ResponseDtoInterface;
use App\Entity\User\User;
use App\Exception\HttpException;
use App\Repository\User\UserRepository;
use App\Repository\User\UserTokenRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserProfile implements UserProfileInterface
{
    private TokenCookieInterface $tokenCookie;

    private TokenStorageInterface $tokenStorage;

    private UserPasswordHasherInterface $passwordHasher;

    private UserRepository $userRepository;

    private UserTokenRepository $tokenRepository;

    public function __construct(
        TokenCookieInterface $tokenCookie,
        TokenStorageInterface $tokenStorage,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository,
        UserTokenRepository $tokenRepository,
    ) {
        $this->tokenCookie = $tokenCookie;
        $this->tokenStorage = $tokenStorage;
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
        $this->tokenRepository = $tokenRepository;
    }

    public function register(string $email, string $password): ResponseDtoInterface
    {
        $user = $this->userRepository->createNew($email, $password);
        $userToken = $this->tokenRepository->createNew($user);

        return (new ResponseDto())
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

        return (new ResponseDto())
            ->setData(true)
            ->setCookie($this->tokenCookie->create($userToken))
        ;
    }
}
