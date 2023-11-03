<?php

declare(strict_types=1);

namespace App\Core\Utils;

class PaginatorUtils
{
    private function __construct() {}

    public static function page(int|string $page): int
    {
        return max((int)$page, 1);
    }

    public static function offset(int $limit, int $page): int
    {
        return intval(($page - 1) * $limit);
    }

    public static function totalPages(int $limit, int $total): int
    {
        return (int)ceil($total / $limit);
    }
}
