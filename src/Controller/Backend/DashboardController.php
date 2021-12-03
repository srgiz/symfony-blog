<?php
declare(strict_types=1);

namespace App\Controller\Backend;

use App\Backend\User\UserPaginateInterface;
use App\Dto\Request\Backend\UserPaginateRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/backend', name: 'backend_')]
class DashboardController extends Controller
{
    #[Route(path: '', name: 'dashboard', methods: ['GET'])]
    public function dashboard(
        UserPaginateRequest $userRequest,
        UserPaginateInterface $userPaginate,
    ): Response
    {
        $dto = $userPaginate->users($userRequest->offset, $userRequest->limit);

        return $this->render('backend/users/index.html.twig', [
            'paginate' => $dto->getData(),
        ]);
    }
}
