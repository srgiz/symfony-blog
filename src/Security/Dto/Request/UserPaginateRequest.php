<?php
declare(strict_types=1);

namespace App\Security\Dto\Request;

use App\Security\Admin\UserPaginate;
use Symfony\Component\Validator\Constraints as Assert;

class UserPaginateRequest
{
    /** @var array<string, mixed> */
    public array $query = [];

    #[Assert\GreaterThanOrEqual(0)]
    public int $offset = 0;

    #[Assert\GreaterThanOrEqual(1)]
    public int $limit = UserPaginate::DEFAULT_LIMIT;

    #[Assert\Choice(callback: [UserPaginate::class, 'getListOrderBy'])]
    public ?string $order = null;

    #[Assert\Choice(['ASC', 'DESC'])]
    public ?string $sort = 'ASC';

    /**
     * @return null|array<string, string>
     */
    public function getOrderBy(): ?array
    {
        if (null === $this->order) {
            return null;
        }

        return [
            $this->order => $this->sort ?? 'ASC',
        ];
    }
}
