<?php
declare(strict_types=1);

namespace App\Controller\Backend;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/backend', name: 'backend_')]
class DashboardController extends Controller
{
    #[Route(path: '', name: 'dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        return new Response();
    }
}
