<?php
declare(strict_types=1);

namespace App\Catalog\Product;

use App\Catalog\Repository\ProductRepository;
use App\Core\Dto\Response\JsonResponseDto;

readonly class ProductView
{
    public function __construct(
        private ProductRepository $productRepository,
    ) {}

    public function getById(int $id): JsonResponseDto
    {
        return new JsonResponseDto($this->productRepository->findOneBy(['id' => $id]));
    }
}
