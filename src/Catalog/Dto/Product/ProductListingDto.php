<?php
declare(strict_types=1);

namespace App\Catalog\Dto\Product;

class ProductListingDto
{
    public int $total = 0;

    public int $limit = 0;

    public int $offset = 0;

    public array $items = [];
}
