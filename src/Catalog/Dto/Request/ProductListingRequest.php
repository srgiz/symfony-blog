<?php
declare(strict_types=1);

namespace App\Catalog\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

class ProductListingRequest
{
    #[Assert\GreaterThanOrEqual(0)]
    public int $offset = 0;

    #[Assert\GreaterThanOrEqual(1)]
    public int $limit = 1;
}
