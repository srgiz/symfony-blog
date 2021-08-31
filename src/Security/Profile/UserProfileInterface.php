<?php
namespace App\Security\Profile;

use App\Dto\Response\ResponseDtoInterface;

interface UserProfileInterface
{
    public const COOKIE_NAME = 'i';

    public const COOKIE_EXPIRE = '+1 day';

    public function register(string $email, string $password): ResponseDtoInterface;
}
