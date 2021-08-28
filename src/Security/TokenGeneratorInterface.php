<?php
namespace App\Security;

use App\Entity\User\UserInterface;
use App\Entity\User\UserToken;

interface TokenGeneratorInterface
{
    public function generate(UserInterface $user): UserToken;
}
