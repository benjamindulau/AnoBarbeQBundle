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
 * Factory for Runtime adapter
 *
 * @author Benjamin Dulau <benjamin.dulau@gmail.com>
 */
class RuntimeAdapterFactory implements AdapterFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create(ContainerBuilder $container, $id, array $config)
    {
        $container
            ->setDefinition($id, new DefinitionDecorator('ano_barbeq.adapter.runtime'))
            ->addMethodCall('setBarbeQ', array(new Reference('ano_barbeq.barbeq')))
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getKey()
    {
        return 'runtime';
    }

    /**
     * {@inheritDoc}
     */
    public function addConfiguration(ArrayNodeDefinition $node)
    {
        // no configuration
        $node
            ->children()
            ->end()
        ;
    }
}