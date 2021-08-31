<?php
namespace App\Security\Profile;

use App\Entity\User\UserToken;
use Symfony\Component\HttpFoundation\Cookie;

interface UserCookieInterface
{
    public function getName(): string;

    public function create(UserToken $token): Cookie;
}
