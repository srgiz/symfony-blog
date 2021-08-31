<?php
declare(strict_types=1);

namespace App\Security\Profile;

use App\Entity\User\UserToken;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Cookie;

class UserCookie implements UserCookieInterface
{
    private string $name;

    private string $expire;

    public function __construct(ParameterBagInterface $params)
    {
        $this->name = $params->get('app.security.token.cookie');
        $this->expire = $params->get('app.security.token.expire');
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function create(UserToken $token): Cookie
    {
        return Cookie::create($this->name, $token->getToken(), strtotime($this->expire));
    }
}
