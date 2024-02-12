<?php

declare(strict_types=1);

namespace App\Symfony\Controller\Security;

use App\Symfony\Security\Authenticator\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/login', name: 'login', methods: ['GET', 'POST'])]
class LoginController extends AbstractController
{
    public function __invoke(Request $request, TokenStorageInterface $tokenStorage): Response
    {
        return $this->render('security/login.html.twig', [
            'isAuthorized' => $tokenStorage->getToken() ? true : false,
            'username' => $request->getPayload()->getString('_username', (string) $tokenStorage->getToken()?->getUser()?->getUserIdentifier()),
            'error' => $request->attributes->get(LoginFormAuthenticator::ERROR_ATTR_NAME),
        ]);
    }
}
