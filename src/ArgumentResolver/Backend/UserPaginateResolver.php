<?php
declare(strict_types=1);

namespace App\ArgumentResolver\Backend;

use App\ArgumentResolver\AbstractDtoResolver;
use App\Dto\Request\Backend\UserPaginateRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class UserPaginateResolver extends AbstractDtoResolver
{
    protected function getClassName(): string
    {
        return UserPaginateRequest::class;
    }

    protected function createRequestDto(Request $request, ArgumentMetadata $argument): object
    {
        $dto = new UserPaginateRequest();
        $offset = (int)$request->query->get('offset', 0);
        $limit = (int)$request->query->get('limit', 0);

        if ($offset >= 0) {
            $dto->offset = $offset;
        }

        if ($limit > 0) {
            $dto->limit = $limit;
        }

        return $dto;
    }
}
