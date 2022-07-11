<?php
declare(strict_types=1);

namespace App;

use App\DependencyInjection\Compiler\IgnoreAutowirePass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new IgnoreAutowirePass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -10);
    }
}
