<?php

namespace App\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class EagerSonataDoctrineMapperPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('doctrine.connections') || !$container->hasDefinition('sonata.doctrine.mapper')) {
            return;
        }

        foreach (array_keys($container->getParameter('doctrine.connections')) as $connection) {
            $eventManagerId = sprintf('doctrine.dbal.%s_connection.event_manager', $connection);

            if (!$container->hasDefinition($eventManagerId)) {
                continue;
            }

            $eventManager = $container->getDefinition($eventManagerId);
            $listeners = $eventManager->getArgument(1);

            if (!is_array($listeners)) {
                continue;
            }

            foreach ($listeners as &$listener) {
                if (
                    is_array($listener)
                    && isset($listener[1])
                    && 'sonata.doctrine.mapper' === $listener[1]
                ) {
                    $listener[1] = new Reference('sonata.doctrine.mapper');
                }
            }

            $eventManager->replaceArgument(1, $listeners);
        }
    }
}
