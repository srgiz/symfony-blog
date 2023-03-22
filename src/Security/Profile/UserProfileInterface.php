<?php
namespace App\Security\Profile;

interface UserProfileInterface
{
    public function register(string $email, string $password): ResponseDtoInterface;

    public function upgradePassword(string $oldPassword, string $newPassword): ResponseDtoInterface;
}
