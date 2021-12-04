<?php
declare(strict_types=1);

namespace App\Dto\Request\Backend;

use App\Backend\User\UserPaginate;
use Symfony\Component\Validator\Constraints as Assert;

class UserPaginateRequest
{
    #[Assert\GreaterThanOrEqual(0)]
    public int $offset = 0;

    #[Assert\GreaterThanOrEqual(1)]
    public int $limit = UserPaginate::DEFAULT_LIMIT;
}
