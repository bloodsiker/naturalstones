<?php

namespace ShortcodeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('shortcode');
        $rootNode
            ->children()
                ->arrayNode('wrapper')
                    ->children()
                        ->scalarNode('template')->defaultNull()->end()
                    ->end()
                ->end()
                ->arrayNode('definitions')
                    ->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->children()
                            ->arrayNode('pattern')
                                ->requiresAtLeastOneElement()
                                ->prototype('scalar')->end()
                            ->end()
                            ->scalarNode('template')->isRequired()->end()
                            ->scalarNode('template_rss_google')->defaultNull()->end()
                            ->scalarNode('template_rss_facebook')->defaultNull()->end()
                            ->scalarNode('template_amp')->defaultNull()->end()
                            ->scalarNode('template_lifestyle')->defaultNull()->end()
                            ->scalarNode('template_rss')->defaultNull()->end()
                            ->arrayNode('exclude_mode')
                                ->prototype('scalar')->end()
                            ->end()
                            ->booleanNode('wrap')->defaultFalse()->end()
                            ->scalarNode('processor')->defaultNull()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
