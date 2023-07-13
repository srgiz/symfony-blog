<?php
declare(strict_types=1);

namespace App\Security\Controller;

use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route('/logout', name: 'logout', methods: ['POST'])]
class LogoutController
{
    public function __invoke()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
