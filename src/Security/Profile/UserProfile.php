<?php
declare(strict_types=1);

namespace App\Security\Profile;

use App\Dto\Response\ResponseDto;
use App\Dto\Response\ResponseDtoInterface;
use App\Repository\User\UserRepository;
use App\Repository\User\UserTokenRepository;

class UserProfile implements UserProfileInterface
{
    private UserRepository $userRepository;

    private UserTokenRepository $tokenRepository;

    private TokenCookieInterface $tokenCookie;

    public function __construct(UserRepository $userRepository, UserTokenRepository $tokenRepository, TokenCookieInterface $tokenCookie)
    {
        $this->userRepository = $userRepository;
        $this->tokenRepository = $tokenRepository;
        $this->tokenCookie = $tokenCookie;
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
}
