<?php
declare(strict_types=1);

namespace App\Security\Controller;

use App\Controller\Controller;
use App\Security\Dto\Request\UserRegisterRequest;
use App\Security\Profile\CurrentProfile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/profile', name: 'register', methods: ['POST'])]
class RegisterAction extends Controller
{
    public function __construct(
        private readonly CurrentProfile $currentProfile
    ) {}

    public function __invoke(UserRegisterRequest $userRequest): JsonResponse
    {
        return $this->json($this->currentProfile->register($userRequest->email, $userRequest->password));
    }
}
