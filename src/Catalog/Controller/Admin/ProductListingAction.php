<?php
declare(strict_types=1);

namespace App\Catalog\Controller\Admin;

use App\Catalog\Dto\Request\ProductListingRequest;
use App\Catalog\Product\ProductListing;
use App\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/products', name: 'admin_products', methods: ['GET'])]
class ProductListingAction extends Controller
{
    public function __construct(
        private readonly ProductListing $listing,
    ) {}

    public function __invoke(ProductListingRequest $request): Response
    {
        //return $this->json($this->listing->paginate($request));

        return $this->render('admin/products/index.html.twig', [
            'data' => $this->listing->paginate($request)->getData(),
        ]);
    }
}
