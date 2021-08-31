<?php
declare(strict_types=1);

namespace App\Security\Profile;

use App\Dto\Response\ResponseDto;
use App\Dto\Response\ResponseDtoInterface;
use App\Repository\User\UserRepository;
use App\Repository\User\UserTokenRepository;
use Symfony\Component\HttpFoundation\Cookie;

class UserProfile implements UserProfileInterface
{
    private UserRepository $userRepository;

    private UserTokenRepository $tokenRepository;

    public function __construct(UserRepository $userRepository, UserTokenRepository $tokenRepository)
    {
        $this->userRepository = $userRepository;
        $this->tokenRepository = $tokenRepository;
    }

    public function register(string $email, string $password): ResponseDtoInterface
    {
        $user = $this->userRepository->createNew($email, $password);
        $userToken = $this->tokenRepository->createNew($user);
        $expiredAt = new \DateTimeImmutable(self::COOKIE_EXPIRE);

        return (new ResponseDto())
            ->setData(true)
            ->setCookie(Cookie::create(self::COOKIE_NAME, $userToken->getToken(), $expiredAt))
        ;
    }
}
