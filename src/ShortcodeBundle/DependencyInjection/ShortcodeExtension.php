<?php

namespace ShortcodeBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * Class ShortcodeExtension
 */
class ShortcodeExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter(
            'shortcode.wrapper.template',
            array_key_exists('wrapper', $mergedConfig) ? $mergedConfig['wrapper']['template'] : null
        );

        $container->setParameter('shortcode.definitions', $mergedConfig['definitions']);
    }
}
