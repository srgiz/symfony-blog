<?php
declare(strict_types=1);

namespace App\Backend\User;

use App\Dto\Response\ResponseDto;
use App\Dto\Response\ResponseDtoInterface;
use App\Repository\User\UserRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserPaginate implements UserPaginateInterface
{
    public const DEFAULT_LIMIT = 1;

    public function __construct(
        private UserRepository $userRepository,
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    public function users(int $offset = 0, int $limit = self::DEFAULT_LIMIT): ResponseDtoInterface
    {
        $total = $this->userRepository->count([]);

        $dto = (new ResponseDto())
            ->setMetaParam('total', $total)
            ->setMetaParam('limit', $limit)
            ->setMetaParam('offset', $offset)
            ->setData($this->userRepository->findBy([], null, $limit, $offset))
        ;

        $prevOffset = $this->getPrevOffset($total, $offset, $limit);
        $nextOffset = $this->getNextOffset($total, $offset, $limit);

        if ($prevOffset !== null) {
            $dto->setMetaParam('prev', $this->urlGenerator->generate('backend_dashboard', ['offset' => $prevOffset]));
        }

        if ($nextOffset !== null) {
            $dto->setMetaParam('next', $this->urlGenerator->generate('backend_dashboard', ['offset' => $nextOffset]));
        }

        return $dto;
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
}
