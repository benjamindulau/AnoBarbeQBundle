<?php

/**
 * This file is part of AnoBarbeQBundle
 *
 * (c) anonymation <contact@anonymation.com>
 *
 */
namespace Ano\Bundle\BarbeQBundle\DependencyInjection\AdapterFactory;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;

/**
 * Factory for Amqp (RabbitMq) adapter
 *
 * @author Benjamin Dulau <benjamin.dulau@gmail.com>
 */
class AmqpAdapterFactory implements AdapterFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create(ContainerBuilder $container, $id, array $config)
    {
        $container
            ->setDefinition($id, new DefinitionDecorator('ano_barbeq.adapter.amqp'))
            ->addArgument($config['connection'])
            ->addArgument($config['exchange'])
            ->addArgument($config['queues'])
            ->addArgument($config['options'])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getKey()
    {
        return 'amqp';
    }

    /**
     * {@inheritDoc}
     */
    public function addConfiguration(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('connection')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('host')->defaultValue('localhost')->end()
                        ->scalarNode('port')->defaultValue(5672)->end()
                        ->scalarNode('user')->defaultValue('guest')->end()
                        ->scalarNode('password')->defaultValue('guest')->end()
                        ->scalarNode('vhost')->defaultValue('/')->end()
                    ->end()
                ->end()
                ->arrayNode('exchange')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('name')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('type')->defaultValue('direct')->end()
                        ->booleanNode('passive')->defaultFalse()->end()
                        ->booleanNode('durable')->defaultTrue()->end()
                        ->booleanNode('auto_delete')->defaultFalse()->end()
                        ->booleanNode('internal')->defaultFalse()->end()
                        ->booleanNode('nowait')->defaultFalse()->end()
                        ->variableNode('arguments')->defaultNull()->end()
                        ->scalarNode('ticket')->defaultNull()->end()
                    ->end()
                ->end()
                ->arrayNode('queues')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->beforeNormalization()
                        ->ifTrue(function($v) { return is_array($v); })
                        ->then(function($v) { $name = key($v); return array($name => array('name' => $name)); })
                    ->end()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')->isRequired()->cannotBeEmpty()->end()
                            ->booleanNode('passive')->defaultFalse()->end()
                            ->booleanNode('durable')->defaultTrue()->end()
                            ->booleanNode('exclusive')->defaultFalse()->end()
                            ->booleanNode('auto_delete')->defaultFalse()->end()
                            ->booleanNode('nowait')->defaultFalse()->end()
                            ->variableNode('arguments')->defaultNull()->end()
                            ->scalarNode('ticket')->defaultNull()->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('options')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('delivery_mode')->defaultValue(2)->end()
                        ->scalarNode('content_type')->defaultValue('text/plain')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}