<?php
declare(strict_types=1);

namespace App\Backend\User;

use App\Dto\Paginate\PaginateOffsetDto;
use App\Dto\Response\ResponseDto;
use App\Dto\Response\ResponseDtoInterface;
use App\Repository\User\UserRepository;

class UserPaginate implements UserPaginateInterface
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function users(int $offset = 0, int $limit = 1): ResponseDtoInterface
    {
        $total = $this->userRepository->count([]);
        $prevOffset = null;

        if ($total && $offset) {
            $prevOffset = $offset - $limit;

            if ($prevOffset >= $total) {
                $prevOffset = $total - $limit;
            }

            if ($prevOffset < 0) {
                $prevOffset = 0;
            }
        }

        $nextOffset = $offset + $limit;
        $nextOffset = $total > $nextOffset ? $nextOffset : null;

        return (new ResponseDto())
            ->setData(new PaginateOffsetDto(
                $offset,
                $limit,
                $total,
                $this->userRepository->findBy([], null, $limit, $offset),
                $prevOffset,
                $nextOffset,
            ))
            ;
    }
}
