<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\Category\CategoryRepository;
use App\Repository\Product\ProductRepository;
use App\Response\Format\JsonResponse;
use App\Service\Category\Tree\CategoryTreeFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route(path: '/', name: 'index', methods: ['GET'])]
    public function index()
    {
        return $this->render('default/index.html.twig');
    }

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
    }
}
