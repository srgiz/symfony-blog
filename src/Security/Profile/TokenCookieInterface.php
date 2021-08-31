<?php
namespace App\Security\Profile;

use App\Entity\User\UserToken;
use Symfony\Component\HttpFoundation\Cookie;

interface TokenCookieInterface
{
    public function getName(): string;

    public function create(UserToken $token): Cookie;
}
