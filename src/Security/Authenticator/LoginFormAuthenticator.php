<?php

namespace App\Security\Authenticator;

use App\Security\Entity\User;
use App\Security\Profile\CurrentProfile;
use App\Security\Repository\UserTokenRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'login';

    public function __construct(
        private readonly CurrentProfile $currentProfile,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly UserTokenRepository $tokenRepository,
    ) {}

    public function authenticate(Request $request): Passport\Passport
    {
        $email = (string)$request->request->get('email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport\Passport(
            new Passport\Badge\UserBadge($email),
            new Passport\Credentials\PasswordCredentials((string)$request->request->get('password', '')),
            [
                new Passport\Badge\CsrfTokenBadge('authenticate', $request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $redirectPath = $this->urlGenerator->generate('index');

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            $redirectPath = $targetPath;
        }

        /** @var User $user */
        $user = $token->getUser();
        $userToken = $this->tokenRepository->createNew($user);

        $response = new RedirectResponse($redirectPath);
        $response->headers->setCookie($this->currentProfile->createCookie($userToken));

        return $response;
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
