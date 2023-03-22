<?php
declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class KernelResponseHeadersSubscriber implements EventSubscriberInterface
{
    /** @var array<string, string> */
    private array $headers = [];

    /** @var array<Cookie> */
    private array $cookies = [];

    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    public function setCookie(Cookie $cookie): void
    {
        $this->cookies[] = $cookie;
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $event->getResponse()->headers->add($this->headers);

        foreach ($this->cookies as $cookie) {
            $event->getResponse()->headers->setCookie($cookie);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', 256],
        ];
    }
}
