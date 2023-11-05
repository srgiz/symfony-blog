<?php

declare(strict_types=1);

namespace App\Core\Messenger;

use App\Core\Repository\MessageRepository;
use App\Core\Utils\PaginatorUtils;
use App\Symfony\Messenger\Manticore\Transport\ManticoreTransport;

class MessageManager
{
    private string $tableName = ManticoreTransport::DEFAULT_TABLE;

    public function __construct(
        private readonly MessageRepository $repository,
    ) {}

    public function paginate(int $page, int $limit = 4): array
    {
        $offset = PaginatorUtils::offset($limit, $page);
        $paginator = $this->repository->paginate($this->tableName, $offset, $limit);

        return [
            'page' => $page,
            'paginator' => $paginator,
            'totalPages' => PaginatorUtils::totalPages($limit, $paginator['count']),
        ];
    }
}
