<?php
declare(strict_types=1);

namespace App\Security\Controller;

use App\Controller\Controller;
use App\Security\Dto\Request\UserPasswordRequest;
use App\Security\Profile\CurrentProfile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/profile/password', name: 'upgrade_password', methods: ['POST'])]
class UpgradePasswordAction extends Controller
{
    public function __construct(
        private readonly CurrentProfile $currentProfile
    ) {}

    public function __invoke(UserPasswordRequest $passwordRequest): JsonResponse
    {
        return $this->json($this->currentProfile->upgradePassword($passwordRequest->oldPassword, $passwordRequest->newPassword));
    }
}
