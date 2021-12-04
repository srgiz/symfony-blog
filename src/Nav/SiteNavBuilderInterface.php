<?php
declare(strict_types=1);

namespace App\Nav;

use App\Dto\Nav\NavCollection;

interface SiteNavBuilderInterface
{
    public function public(): NavCollection;

    public function backend(): NavCollection;
}
