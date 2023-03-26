<?php
declare(strict_types=1);

namespace App\Security\Controller\Admin;

use App\Security\Admin\UserPaginateInterface;
use App\Core\Controller\Controller;
use App\Security\Dto\Request\UserPaginateRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/backend', name: 'backend_dashboard')]
class DashboardAction extends Controller
{
    public function __construct(
        private readonly UserPaginateInterface $userPaginate
    ) {}

    public function __invoke(UserPaginateRequest $userRequest): Response
    {
        $dto = $this->userPaginate->paginate($userRequest);
        //return $this->json($dto);

        return $this->render('backend/users/index.html.twig', [
            //'meta' => $dto->getMeta(),
            'data' => $dto->getData(),
        ]);
    }
}
