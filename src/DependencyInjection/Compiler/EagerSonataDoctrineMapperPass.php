<?php

namespace App\DependencyInjection\Compiler;

use Doctrine\Common\EventManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class EagerSonataDoctrineMapperPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition('lexik_translation.translator')) {
            $translator = $container->getDefinition('lexik_translation.translator');
            $translator->setMethodCalls(array_filter(
                $translator->getMethodCalls(),
                static fn (array $call): bool => 'addDatabaseResources' !== $call[0],
            ));
        }

        if (!$container->hasParameter('doctrine.connections')) {
            return;
        }

        foreach (array_keys($container->getParameter('doctrine.connections')) as $connection) {
            $eventManagerId = sprintf('doctrine.dbal.%s_connection.event_manager', $connection);

            if (!$container->hasDefinition($eventManagerId)) {
                continue;
            }

            $container->getDefinition($eventManagerId)
                ->setClass(EventManager::class)
                ->setArguments([]);
        }
    }
}
