<?php
declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

class AuthenticationSubscriber implements EventSubscriberInterface
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Принудительно удаляем авторизацию, с какого-то хера нельзя просто так взять и отключить хранение токена в сессии
     * @see \Symfony\Bundle\SecurityBundle\DependencyInjection\Compiler\RegisterTokenUsageTrackingPass::process() enableUsageTracking
     * @see \Symfony\Component\Security\Core\Authentication\Token\Storage\UsageTrackingTokenStorage
     */
    public function onFailure(LoginFailureEvent $event): void
    {
        $this->tokenStorage->setToken(null);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginFailureEvent::class => 'onFailure',
        ];

        // todo: если авторизоваться и не сработает TokenAuthenticator, то останется токен в сессии, наверное надо что-то с этим делать
    }
}
