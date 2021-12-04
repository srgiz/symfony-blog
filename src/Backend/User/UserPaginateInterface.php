<?php
namespace App\Backend\User;

use App\Dto\Request\Backend\UserPaginateRequest;
use App\Dto\Response\ResponseDtoInterface;

interface UserPaginateInterface
{
    public function paginate(UserPaginateRequest $request): ResponseDtoInterface;

    public static function getListOrderBy(): array;
}
