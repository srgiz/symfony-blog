<?php
declare(strict_types=1);

namespace App\Backend\User;

use App\Dto\Request\Backend\UserPaginateRequest;
use App\Dto\Response\ResponseDto;
use App\Dto\Response\ResponseDtoInterface;
use App\Repository\User\UserRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserPaginate implements UserPaginateInterface
{
    public const DEFAULT_LIMIT = 1;

    /** @var array<string, string> public_name => column_name */
    private const ORDER_BY = [
        'id' => 'id',
        'createdAt' => 'created_at',
    ];

    public function __construct(
        private UserRepository $userRepository,
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    public function paginate(UserPaginateRequest $request): ResponseDtoInterface
    {
        $total = $this->userRepository->count([]);
        $offset = $request->offset;
        $limit = $request->limit;

        $dto = (new ResponseDto())
            ->setMetaParam('total', $total)
            ->setMetaParam('limit', $limit)
            ->setMetaParam('offset', $offset)
            ->setData($this->userRepository->findBy([], $this->createOrderBy($request->getOrderBy()), $limit, $offset))
        ;

        $prevOffset = $this->getPrevOffset($total, $offset, $limit);
        $nextOffset = $this->getNextOffset($total, $offset, $limit);

        if ($prevOffset !== null) {
            $dto->setMetaParam(
                'prev',
                $this->urlGenerator->generate('backend_dashboard', $this->createPaginateQuery($prevOffset, $request))
            );
        }

        if ($nextOffset !== null) {
            $dto->setMetaParam(
                'next',
                $this->urlGenerator->generate('backend_dashboard', $this->createPaginateQuery($nextOffset, $request))
            );
        }

        return $dto;
    }

    private function createOrderBy(?array $requestOrderBy): array
    {
        if (null === $requestOrderBy) {
            return [
                'id' => 'ASC',
            ];
        }

        $orderBy = [];

        foreach ($requestOrderBy as $order => $sort) {
            $orderBy[self::ORDER_BY[$order]] = $sort;
        }

        return $orderBy;
    }

    private function createPaginateQuery(int $offset, UserPaginateRequest $request): array
    {
        $query['offset'] = $offset;
        $order = $request->order;

        if ($order) {
            $query['order'] = $order;
            $query['sort'] = $request->sort;
        }

        return $query;
    }

    private function getPrevOffset(int $total, int $offset, int $limit): ?int
    {
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

        return $prevOffset;
    }

    private function getNextOffset(int $total, int $offset, int $limit): ?int
    {
        $nextOffset = $offset + $limit;
        return $total > $nextOffset ? $nextOffset : null;
    }

    public static function getListOrderBy(): array
    {
        return array_keys(self::ORDER_BY);
    }
}
