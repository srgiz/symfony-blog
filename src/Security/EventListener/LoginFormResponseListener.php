<?php
declare(strict_types=1);

namespace App\Security\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LoginFormResponseListener implements EventSubscriberInterface
{
    public const COOKIE_ATTR_NAME = '_security_remember_me_token';

    public function rememberMe(ResponseEvent $event): void
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

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::RESPONSE => 'rememberMe'];
    }
}
