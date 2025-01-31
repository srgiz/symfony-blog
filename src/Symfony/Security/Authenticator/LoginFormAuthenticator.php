<?php

declare(strict_types=1);

namespace App\Symfony\Security\Authenticator;

use App\Domain\Blog\Entity\User;
use App\Domain\Blog\Repository\UserRepositoryInterface;
use App\Symfony\EventListener\LoginFormResponseListener;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class LoginFormAuthenticator extends AbstractAuthenticator
{
    public const string ROUTE = 'login';

    public const string ERROR_ATTR_NAME = '_error';

    public function __construct(
        private readonly UserProviderInterface $userProvider,
        private readonly PasswordHasherFactoryInterface $hasherFactory,
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return $request->isMethod('POST') && self::ROUTE === $request->attributes->get('_route');
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->getPayload()->getString('_username');
        $password = $request->getPayload()->getString('_password');

        $userBadge = new UserBadge($username, $this->userProvider->loadUserByIdentifier(...));

        return new Passport($userBadge, new PasswordCredentials($password));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        /** @var User $user */
        $user = $token->getUser();
        $hasher = $this->hasherFactory->getPasswordHasher(User::class);
        // токен является хешем на хеш пароля
        $tokenHash = $hasher->hash((string) $user->getPassword());

        $this->userRepository->addToken($user, $tokenHash);

        $request->attributes->set(LoginFormResponseListener::COOKIE_ATTR_NAME, new Cookie(
            TokenAuthenticator::COOKIE_NAME,
            $tokenHash,
            time() + 3600,
        ));

        return new RedirectResponse($request->getRequestUri());
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $request->attributes->set(self::ERROR_ATTR_NAME, 'Bad credentials');

        return null;
    }
}
