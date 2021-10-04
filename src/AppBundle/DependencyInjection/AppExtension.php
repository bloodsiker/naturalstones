<?php

namespace AppBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AppExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $bundles = $container->getParameter('kernel.bundles');

        $loader->load('services.yml');
        $loader->load('blocks.yml');
        $loader->load('twig.yml');

        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load('admin.yml');
        }
    }
}
