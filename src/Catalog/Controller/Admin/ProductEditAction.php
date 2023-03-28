<?php
declare(strict_types=1);

namespace App\Catalog\Controller\Admin;

use App\Catalog\Product\ProductEdit;
use App\Core\Attribute\Csrf;
use App\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/products/edit', name: 'admin_product_edit', methods: ['POST'])]
#[Csrf('product_edit')]
class ProductEditAction extends Controller
{
    public function __construct(
        private readonly ProductEdit $productEdit,
    ) {}

    public function __invoke(Request $request): Response
    {
        return $this->redirect($this->generateUrl('admin_product_view', ['id' => $this->productEdit->save($request)]));
    }
}
