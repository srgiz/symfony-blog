<?php
declare(strict_types=1);

namespace App\Catalog\Controller\Admin;

use App\Catalog\Product\ProductView;
use App\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/products/view', name: 'admin_product_view', methods: ['GET'])]
class ProductViewAction extends Controller
{
    public function __construct(
        private readonly ProductView $productView,
    ) {}

    public function __invoke(Request $request): Response
    {
        $id = (int)$request->query->get('id', 0);

        //return $this->json($this->productView->getById($id));

        return $this->render('admin/products/view.html.twig', [
            'data' => $this->productView->getById($id)->getData(),
        ]);
    }
}
