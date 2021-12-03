<?php
namespace App\Backend\User;

use App\Dto\Response\ResponseDtoInterface;

interface UserPaginateInterface
{
    public function users(int $offset = 0, int $limit = 1): ResponseDtoInterface;
}
