<?php
declare(strict_types=1);

namespace App\DependencyInjection\Attribute;

/**
 * @see \App\DependencyInjection\Compiler\IgnoreAutowirePass
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class IgnoreAutowire
{
}
