<?php

namespace App\DependencyInjection\Compiler;

use App\DependencyInjection\Attribute\IgnoreAutowire;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class IgnoreAutowirePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definitions = $container->getDefinitions();

        foreach ($definitions as $id => $definition) {
            if (class_exists($id)) {
                $reflection = new \ReflectionClass($id);
                $attributes = $reflection->getAttributes(IgnoreAutowire::class);

                if (!empty($attributes)) {
                    $container->removeDefinition($id);
                }
            }
        }
    }
}
