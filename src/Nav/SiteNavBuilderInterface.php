<?php
declare(strict_types=1);

namespace App\Nav;

use App\Dto\Nav\NavCollection;

/**
 * @link https://symfony.com/doc/current/templating/global_variables.html
 */
interface SiteNavBuilderInterface
{
    public function public(): NavCollection;

    public function backend(): NavCollection;
}
