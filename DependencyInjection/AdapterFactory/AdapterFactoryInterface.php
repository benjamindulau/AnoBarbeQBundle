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

/**
 * Interface for the adapater factories
 *
 * @author Benjamin Dulau <benjamin.dulau@gmail.com>
 */
interface AdapterFactoryInterface
{
    /**
     * Creates the adapter, registers it and returns its id
     *
     * @param  ContainerBuilder $container  A ContainerBuilder instance
     * @param  string           $id         The id of the service
     * @param  array            $config     An array of configuration
     */
    function create(ContainerBuilder $container, $id, array $config);

    /**
     * Returns the key for the factory configuration
     *
     * @return string
     */
    function getKey();

    /**
     * Adds configuration nodes for the factory
     *
     * @param  ArrayNodeDefinition $builder
     */
    function addConfiguration(ArrayNodeDefinition $builder);
}