<?php
declare(strict_types=1);

namespace App\Catalog\Product;

use App\Catalog\Dto\Product\ProductListingDto;
use App\Catalog\Dto\Request\ProductListingRequest;
use App\Catalog\Repository\ProductRepository;
use App\Core\Dto\Response\JsonResponseDto;

readonly class ProductListing
{
    public function __construct(
        private ProductRepository $productRepository,
    ) {}

    public function paginate(ProductListingRequest $request): JsonResponseDto
    {
        $dto = new ProductListingDto();
        $this->fillProducts($request, $dto);
        return new JsonResponseDto($dto);
    }

    private function fillProducts(ProductListingRequest $request, ProductListingDto $dto): void
    {
        $dto->limit = $request->limit;
        $dto->offset = $request->offset;
        $dto->total = $this->productRepository->count([]);
        $dto->items = $this->productRepository->findBy([], ['id' => 'ASC'], $request->limit, $request->offset);
    }
}
