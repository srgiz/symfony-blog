<?php
namespace App\Security;

use App\Entity\User\Token;
use App\Entity\User\UserInterface;

interface TokenGeneratorInterface
{
    public function generate(UserInterface $user): Token;
}
