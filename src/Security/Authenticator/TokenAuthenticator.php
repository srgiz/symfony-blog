<?php
declare(strict_types=1);

namespace App\Security\Authenticator;

use App\Repository\User\UserTokenRepository;
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
    private UserTokenRepository $userTokenRepository;

    public function __construct(UserTokenRepository $userTokenRepository)
    {
        $this->userTokenRepository = $userTokenRepository;
    }

    public function supports(Request $request): ?bool
    {
        return LoginFormAuthenticator::LOGIN_ROUTE !== $request->attributes->get('_route');
    }

    public function authenticate(Request $request): PassportInterface
    {
        $token = (string)$request->cookies->get('i', '');

        if (empty($token))
            throw new CustomUserMessageAuthenticationException('Token not passed');

        $userToken = $this->userTokenRepository->findByKey($token);

        if (!$userToken)
            throw new CustomUserMessageAuthenticationException('Invalid token');

        return new SelfValidatingPassport(new UserBadge($userToken->getUser()->getEmail()));
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
