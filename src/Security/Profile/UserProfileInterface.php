<?php
namespace App\Security\Profile;

use App\Dto\Response\ResponseDtoInterface;

interface UserProfileInterface
{
    public function register(string $email, string $password): ResponseDtoInterface;
}
