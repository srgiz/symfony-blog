<?php
declare(strict_types=1);

namespace App\Core\DependencyInjection\Attribute;

/**
 * @see \App\Core\DependencyInjection\Compiler\IgnoreAutowirePass
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class IgnoreAutowire
{
}
