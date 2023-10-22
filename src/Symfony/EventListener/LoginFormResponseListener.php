<?php
declare(strict_types=1);

namespace App\Symfony\EventListener;

use App\Symfony\Security\Authenticator\LoginFormAuthenticator;
use App\Symfony\Security\Authenticator\TokenAuthenticator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LoginFormResponseListener implements EventSubscriberInterface
{
    public const COOKIE_ATTR_NAME = '_security_remember_me_token';

    public function __construct(private readonly UrlGeneratorInterface $urlGenerator) {}

    public function onRememberMe(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();

        if ($request->attributes->has(self::COOKIE_ATTR_NAME)) {
            $response->headers->setCookie($request->attributes->get(self::COOKIE_ATTR_NAME));
        }
    }

    public function onLogout(LogoutEvent $event): void
    {
        $event->setResponse($response = new RedirectResponse($this->urlGenerator->generate(LoginFormAuthenticator::ROUTE)));
        $response->headers->setCookie(new Cookie(TokenAuthenticator::COOKIE_NAME));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onRememberMe',
            LogoutEvent::class => 'onLogout',
        ];
    }
}
