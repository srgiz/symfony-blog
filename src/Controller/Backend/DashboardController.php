<?php
declare(strict_types=1);

namespace App\Controller\Backend;

use App\Backend\User\UserPaginateInterface;
use App\Dto\Request\Backend\UserPaginateRequest;
use App\Nav\SiteNavBuilderInterface;
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
        $dto = $userPaginate->paginate($userRequest);
        //return $this->json($dto);

        return $this->render('backend/users/index.html.twig', [
            'meta' => $dto->getMeta(),
            'users' => $dto->getData(),
        ]);
    }

    public function nav(SiteNavBuilderInterface $navBuilder): Response
    {
        return $this->render('backend/dashboard/_nav.html.twig', [
            'nav' => $navBuilder->backend(),
        ]);
    }
}
