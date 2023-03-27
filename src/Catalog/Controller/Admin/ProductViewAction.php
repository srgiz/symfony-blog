<?php
declare(strict_types=1);

namespace App\Catalog\Controller\Admin;

use App\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/products/view', name: 'admin_product_view', methods: ['GET'])]
class ProductViewAction extends Controller
{
    public function __invoke(): Response
    {
        return $this->json(['todo: product view']);
    }
}
