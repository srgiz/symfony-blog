<?php
declare(strict_types=1);

namespace App\Catalog\Product;

use App\Catalog\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

readonly class ProductEdit
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function save(Request $request): int
    {
        // todo. validate or form

        $product = $this->em->getRepository(Product::class)->findOneBy(['id' => (int)$request->query->get('id')]);

        if (!$product) {
            $product = new Product();
            $this->em->persist($product);
        }

        $product->setName($request->request->get('name'));

        $this->em->flush();
        return $product->getId();
    }
}
