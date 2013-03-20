<?php

/**
 * This file is part of AnoBarbeQBundle
 *
 * (c) anonymation <contact@anonymation.com>
 *
 */
namespace Ano\Bundle\BarbeQBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConsumerCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ano_barbeq.message_dispatcher')) {
            return;
        }

        $definition = $container->getDefinition('ano_barbeq.message_dispatcher');
        foreach ($container->findTaggedServiceIds('ano_barbeq.consumer') as $id => $consumers) {
            foreach ($consumers as $consumer) {
                $priority = isset($consumer['priority']) ? $consumer['priority'] : 0;

                if (!isset($consumer['queue'])) {
                    throw new \InvalidArgumentException(sprintf('A "queue" attribute must be set for the consumer service with id "%s".', $id));
                }

                $definition->addMethodCall('addListenerService', array($consumer['queue'], array($id, 'consume'), $priority));
            }
        }
    }
}