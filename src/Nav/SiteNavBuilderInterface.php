<?php
declare(strict_types=1);

namespace App\Nav;

use App\Nav\Dto\NavItem;

/**
 * @link https://symfony.com/doc/current/templating/global_variables.html
 */
interface SiteNavBuilderInterface
{
    /**
     * @return array<NavItem>
     */
    public function public(): array;

    /**
     * @return array<NavItem>
     */
    public function backend(): array;
}
