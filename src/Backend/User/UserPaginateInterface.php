<?php
namespace App\Backend\User;

use App\Response\JsonResponseDto;
use App\Security\Dto\Request\UserPaginateRequest;

interface UserPaginateInterface
{
    public function paginate(UserPaginateRequest $request): JsonResponseDto;

    public static function getListOrderBy(): array;
}
