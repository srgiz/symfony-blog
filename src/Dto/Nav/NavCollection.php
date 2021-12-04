<?php
declare(strict_types=1);

namespace App\Dto\Nav;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @phpstan-template TKey
 * @template-extends \IteratorAggregate<TKey, NavItem>
 * @template-extends \ArrayAccess<TKey|null, NavItem>
 */
class NavCollection extends ArrayCollection
{
}
