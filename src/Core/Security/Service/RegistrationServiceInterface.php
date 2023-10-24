<?php

namespace App\Core\Security\Service;

interface RegistrationServiceInterface
{
    public function register(string $email, #[\SensitiveParameter] string $password): void;
}
