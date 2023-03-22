<?php
declare(strict_types=1);

namespace App\Catalog\Controller;

use App\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/product', name: 'product', methods: ['GET'])]
class ProductPublicAction extends Controller
{
    public function __invoke(): JsonResponse
    {
        return $this->json(['id' => 0, 'name' => 'virtual product']);
    }

    /*
    #[Route(path: '/test', name: 'test', methods: ['GET'])]
    public function test(ProductRepository $productRepository)
    {
        $products = $productRepository->findByFilter([
            'enum' => [
                'color' => ['red'],
            ],
            'range' => [
                'size' => [0, 2],
            ],
        ]);

        return new JsonResponse([
            'products' => $products,
        ]);
    }

    #[Route(path: '/test/save', name: 'test-save', methods: ['GET'])]
    public function testSave(EntityManagerInterface $em, ProductRepository $productRepository)
    {
        $product = $productRepository->findById(1);

        $product->getAttrValues()->setValues([
            'color' => [
                'red',
            ],
            'size' => [
                rand(0, 10),
            ],
        ], false);

        $em->flush();

        return new JsonResponse([
            'id' => $product->getId(),
            'values' => $product->getAttrValues()->getValues(),
        ]);
    }

    #[Route(path: '/test-categories', name: 'test-categories', methods: ['GET'])]
    public function testCategories(CategoryRepository $categoryRepository, CategoryTreeFactory $treeFactory)
    {
        $categories = $categoryRepository->findAll();
        $tree = $treeFactory->create($categories);

        foreach ($tree as $category) {
            var_dump([
                $category->getId() => $category->getChildren()
            ]);
        }

        return new JsonResponse([
            'tree' => $tree,
        ]);
    }*/
}
