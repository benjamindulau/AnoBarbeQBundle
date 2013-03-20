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
use Symfony\Component\DependencyInjection\Reference;

/**
 * Factory for Pdo adapter
 *
 * @author Benjamin Dulau <benjamin.dulau@gmail.com>
 */
class PdoAdapterFactory implements AdapterFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create(ContainerBuilder $container, $id, array $config)
    {
        $container
            ->setDefinition($id, new DefinitionDecorator('ano_barbeq.adapter.pdo'))
            ->addArgument(new Reference($config['pdo_service']))
            ->addArgument($config['db_options'])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getKey()
    {
        return 'pdo';
    }

    /**
     * {@inheritDoc}
     */
    public function addConfiguration(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('pdo_service')->isRequired()->cannotBeEmpty()->end()
                ->arrayNode('db_options')
                    ->children()
                        ->scalarNode('table')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('id')->defaultValue('msg_id')->end()
                        ->scalarNode('body')->defaultValue('msg_body')->end()
                        ->scalarNode('queue')->defaultValue('msg_queue')->end()
                        ->scalarNode('state')->defaultValue('msg_state')->end()
                        ->scalarNode('started_at')->defaultValue('msg_started_at')->end()
                        ->scalarNode('completed_at')->defaultValue('msg_completed_at')->end()
                        ->scalarNode('priority')->defaultValue('msg_priority')->end()
                        ->scalarNode('metadata')->defaultValue('msg_metadata')->end()
                        ->scalarNode('time')->defaultValue('msg_time')->end()
                        ->scalarNode('memory')->defaultValue('msg_memory')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}