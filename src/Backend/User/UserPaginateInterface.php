<?php
namespace App\Backend\User;

use App\Dto\Request\Backend\UserPaginateRequest;
use App\Response\JsonResponseDto;

interface UserPaginateInterface
{
    public function paginate(UserPaginateRequest $request): JsonResponseDto;

    public static function getListOrderBy(): array;
}
