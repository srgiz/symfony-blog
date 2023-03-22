<?php
declare(strict_types=1);

namespace App\Security\Profile;

use App\Security\Entity\UserToken;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Cookie;

readonly class TokenCookie
{
    public function __construct(
        #[Autowire('%app.security.token.cookie%')] private string $name,
        #[Autowire('%app.security.token.expire%')] private string $expire,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function create(UserToken $token): Cookie
    {
        return Cookie::create($this->getName(), $token->getToken(), strtotime($this->expire));
    }
}
