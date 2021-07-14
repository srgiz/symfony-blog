<?php
declare(strict_types=1);

namespace App\Security;

use App\Repository\User\TokenRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class TokenAuthenticator extends AbstractAuthenticator
{
    private TokenRepository $tokenRepository;

    public function __construct(TokenRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
    }

    public function supports(Request $request): ?bool
    {
        return LoginFormAuthenticator::LOGIN_ROUTE !== $request->attributes->get('_route');
    }

    public function authenticate(Request $request): PassportInterface
    {
        $cookie = (string)$request->cookies->get('i', '');

        if (empty($cookie))
            throw new CustomUserMessageAuthenticationException('Token not passed');

        $token = $this->tokenRepository->findByToken($cookie);

        if (!$token)
            throw new CustomUserMessageAuthenticationException('Invalid token');

        return new SelfValidatingPassport(new UserBadge($token->getUser()->getEmail()));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }
}
