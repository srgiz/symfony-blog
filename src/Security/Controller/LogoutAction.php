<?php
declare(strict_types=1);

namespace App\Security\Controller;

use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/logout', name: 'logout', methods: ['GET'])]
class LogoutAction
{
    public function __invoke()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
