<?php

declare(strict_types=1);

namespace App\Symfony\Security\Authenticator;

use App\Domain\Blog\Entity\User;
use App\Domain\Blog\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PreAuthenticatedUserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class TokenAuthenticator implements AuthenticatorInterface
{
    public const string COOKIE_NAME = 'i';

    public function __construct(
        private readonly UserProviderInterface $userProvider,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly PasswordHasherFactoryInterface $hasherFactory,
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return true;
    }

    public function authenticate(Request $request): Passport
    {
        $token = (string) $request->cookies->get(self::COOKIE_NAME);

        $user = $this->userRepository->findByToken($token);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException('No token');
        }

        $hasher = $this->hasherFactory->getPasswordHasher(User::class);

        if (!$hasher->verify($token, (string) $user->getPassword())) {
            // токен является хешем на хеш пароля
            throw new CustomUserMessageAuthenticationException('Invalid token');
        }

        $userBadge = new UserBadge($user->getEmail(), $this->userProvider->loadUserByIdentifier(...));

        return new SelfValidatingPassport($userBadge, [new PreAuthenticatedUserBadge()]);
    }

    public function createToken(Passport $passport, string $firewallName): TokenInterface
    {
        return new PreAuthenticatedToken($passport->getUser(), $firewallName, $passport->getUser()->getRoles());
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($this->tokenStorage->getToken()) {
            $this->tokenStorage->setToken(null);
        }

        return null;
    }
}
