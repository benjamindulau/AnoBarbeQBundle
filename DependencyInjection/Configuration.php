<?php

/**
 * This file is part of AnoBarbeQBundle
 *
 * (c) anonymation <contact@anonymation.com>
 *
 */
namespace Ano\Bundle\BarbeQBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration for ano_barbeq
 *
 * @author Benjamin Dulau <benjamin.dulau@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    private $adapterFactories;

    /**
     * Constructor
     *
     * @param array $adapterFactories
     */
    public function __construct(array $adapterFactories)
    {
        $this->adapterFactories = $adapterFactories;
    }

    /**
     * Generates the configuration tree builder
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ano_barbe_q');

        $this->addAdapterSection($rootNode, $this->adapterFactories);

        $rootNode
            // add a faux-entry for factories, so that no validation error is thrown
            ->fixXmlConfig('factory', 'factories')
            ->children()
                ->arrayNode('factories')->ignoreExtraKeys()->end()
            ->end()
        ;

        return $treeBuilder;
    }

    private function addAdapterSection(ArrayNodeDefinition $node, array $adapterFactories)
    {
        $adapterNodeBuilder = $node
            ->children()
                ->arrayNode('adapter')
                    ->children()
        ;

        foreach ($adapterFactories as $name => $factory) {
            $factoryNode = $adapterNodeBuilder->arrayNode($name)->canBeUnset();

            $factory->addConfiguration($factoryNode);
        }
    }
}