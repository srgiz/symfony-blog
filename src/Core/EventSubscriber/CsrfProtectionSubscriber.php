<?php
declare(strict_types=1);

namespace App\Core\EventSubscriber;

use App\Core\Attribute\Csrf;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

readonly class CsrfProtectionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private CsrfTokenManagerInterface $csrfTokenManager,
    ) {}

    public function onKernelController(ControllerEvent $event): void
    {
        if (
            !$event->isMainRequest()
            || !$event->getRequest()->isMethod('POST')
        ) {
            return;
        }

        /** @var Csrf|null $attribute */
        $attribute = $event->getAttributes()[Csrf::class][0] ?? null;

        if (
            !$attribute
            || $this->csrfTokenManager->isTokenValid(new CsrfToken($attribute->id, (string)$event->getRequest()->request->get($attribute->field)))
        ) {
            return;
        }

        throw new HttpException(403, 'Invalid CSRF token');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController', 8],
        ];
    }
}
