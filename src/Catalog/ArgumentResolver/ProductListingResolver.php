<?php
declare(strict_types=1);

namespace App\Catalog\ArgumentResolver;

use App\Catalog\Dto\Request\ProductListingRequest;
use App\Core\Controller\AbstractDtoResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class ProductListingResolver extends AbstractDtoResolver
{
    protected function getClassName(): string
    {
        return ProductListingRequest::class;
    }

    protected function createRequestDto(Request $request, ArgumentMetadata $argument): object
    {
        $dto = new ProductListingRequest();
        $dto->offset = (int)$request->query->get('offset', 0);
        $dto->limit = (int)$request->query->get('limit', 1);
        return $dto;
    }
}
