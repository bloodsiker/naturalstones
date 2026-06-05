<?php

namespace App;

use App\DependencyInjection\Compiler\EagerSonataDoctrineMapperPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new EagerSonataDoctrineMapperPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 100);
    }
}
