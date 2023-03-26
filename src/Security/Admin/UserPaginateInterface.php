<?php
namespace App\Security\Admin;

use App\Core\Dto\Response\JsonResponseDto;
use App\Security\Dto\Request\UserPaginateRequest;

interface UserPaginateInterface
{
    public function paginate(UserPaginateRequest $request): JsonResponseDto;

    /**
     * @return string[]
     */
    public static function getListOrderBy(): array;
}
