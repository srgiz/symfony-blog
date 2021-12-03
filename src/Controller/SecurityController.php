<?php

namespace App\Controller;

use App\Dto\Request\Security\UserPasswordRequest;
use App\Dto\Request\Security\UserRegisterRequest;
use App\Security\Profile\UserProfileInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    public function __construct(
        private UserProfileInterface $userProfile,
    ) {}

    #[Route(path: '/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/user', name: 'user_register', methods: ['POST'])]
    public function register(UserRegisterRequest $userRequest): JsonResponse
    {
        return $this->json($this->userProfile->register($userRequest->email, $userRequest->password));
    }

    #[Route(path: '/user/password', name: 'user_password', methods: ['POST'])]
    public function password(UserPasswordRequest $passwordRequest): JsonResponse
    {
        return $this->json($this->userProfile->upgradePassword($passwordRequest->oldPassword, $passwordRequest->newPassword));
    }

    #[Route(path: '/logout', name: 'app_logout', methods: ['GET'])]
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
