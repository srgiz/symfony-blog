<?php
declare(strict_types=1);

namespace App\Security\ArgumentResolver;

use App\Security\Admin\UserPaginate;
use App\Core\Controller\AbstractDtoResolver;
use App\Security\Dto\Request\UserPaginateRequest;
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
        $dto->query = $request->query->all();
        $offset = (int)$request->query->get('offset', 0);

        if ($offset >= 0) {
            $dto->offset = $offset;
        }

        $limit = (int)$request->query->get('limit', 0);

        if ($limit > 0) {
            $dto->limit = $limit;
        }

        $order = $request->query->get('order');

        if (in_array($order, UserPaginate::getListOrderBy(), true)) {
            $dto->order = $order;
            $sort = mb_strtoupper((string)$request->query->get('sort'));

            if (in_array($sort, ['ASC', 'DESC'], true)) {
                $dto->sort = $sort;
            }
        }

        return $dto;
    }
}
