<?php

namespace App\Domain\Security\Service;

interface RegistrationServiceInterface
{
    public function register(string $email, #[\SensitiveParameter] string $password): void;
}
