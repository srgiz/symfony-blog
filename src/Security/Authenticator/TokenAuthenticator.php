<?php
declare(strict_types=1);

namespace App\Security\Authenticator;

use App\Security\Profile\TokenCookie;
use App\Security\Repository\UserTokenRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport;

class TokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly UserTokenRepository $tokenRepository,
        private readonly TokenCookie $tokenCookie,
    ) {}

    public function supports(Request $request): ?bool
    {
        return LoginFormAuthenticator::LOGIN_ROUTE !== $request->attributes->get('_route');
    }

    public function authenticate(Request $request): Passport\Passport
    {
        $token = (string)$request->cookies->get($this->tokenCookie->getName());

        if (empty($token)) {
            throw new Exception\CustomUserMessageAuthenticationException('Token not passed');
        }

        $userToken = $this->tokenRepository->findByKey($token);

        if (!$userToken) {
            throw new Exception\CustomUserMessageAuthenticationException('Invalid token');
        }

        // todo: check password hash

        return new Passport\SelfValidatingPassport(new Passport\Badge\UserBadge($userToken->getUser()->getEmail()));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, Exception\AuthenticationException $exception): ?Response
    {
        return null;
    }
}
