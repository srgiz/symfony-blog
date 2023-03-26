<?php
declare(strict_types=1);

namespace App\Catalog\Controller;

use App\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/category', name: 'category', methods: ['GET'])]
class CategoryAction extends Controller
{
    public function __invoke(): JsonResponse
    {
        return $this->json(['id' => 0, 'name' => 'virtual test', 'products' => []]);
    }
}
