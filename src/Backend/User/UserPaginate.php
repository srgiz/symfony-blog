<?php
declare(strict_types=1);

namespace App\Backend\User;

use App\Dto\Paginate\PageLink;
use App\Dto\Paginate\Paginate;
use App\Dto\Paginate\SortLink;
use App\Response\JsonResponseDto;
use App\Security\Dto\Request\UserPaginateRequest;
use App\Security\Repository\UserRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserPaginate implements UserPaginateInterface
{
    public const DEFAULT_LIMIT = 1;

    private const ORDER_BY = [
        'id' => [
            'column' => 'id',
            'name' => 'id',
        ],
        'createdAt' => [
            'column' => 'created_at',
            'name' => 'Дата создания',
        ],
    ];

    private string $routeName = 'backend_dashboard';

    public function __construct(
        private UserRepository $userRepository,
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    public function paginate(UserPaginateRequest $request): JsonResponseDto
    {
        $total = $this->userRepository->count([]);
        $offset = $request->offset;
        $limit = $request->limit;
        $orderBy = $this->createOrderBy($request->getOrderBy());

        $data = new Paginate();
        $data->setItems($this->userRepository->findBy([], $orderBy, $limit, $offset));

        $dto = (new JsonResponseDto($data))
            //->setMetaParam('total', $total)
            //->setMetaParam('limit', $limit)
            //->setMetaParam('offset', $offset)
            //->setData($data)
        ;

        foreach (self::ORDER_BY as $key => $order) {
            $name = $order['name'];

            $query = $request->query;
            unset($query['offset']);

            $query['order'] = $key;
            $query['sort'] = 'ASC';

            if (isset($orderBy[$order['column']])) {
                $name .= ' ' . ($orderBy[$order['column']] === 'DESC' ? '&darr;' : '&uarr;');
                $query['sort'] = $orderBy[$order['column']] === 'DESC' ? 'ASC' : 'DESC'; // переворачиваем сортировку по ссылке
            }

            $data->addSortLink(new SortLink(
                $this->urlGenerator->generate($this->routeName, $query),
                $name,
            ));
        }

        $prevOffset = $this->getPrevOffset($total, $offset, $limit);
        $nextOffset = $this->getNextOffset($total, $offset, $limit);

        if ($prevOffset !== null) {
            $query = $request->query;
            $query['offset'] = $prevOffset;

            $data->addPageLink(new PageLink(
                $this->urlGenerator->generate($this->routeName, $query),
                '&lsaquo; Назад'
            ));
        }

        if ($nextOffset !== null) {
            $query = $request->query;
            $query['offset'] = $nextOffset;

            $data->addPageLink(new PageLink(
                $this->urlGenerator->generate($this->routeName, $query),
                'Вперед &rsaquo;'
            ));
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
            if (isset(self::ORDER_BY[$order])) {
                $orderBy[self::ORDER_BY[$order]['column']] = $sort;
            }
        }

        return $orderBy;
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
