<?php

/**
 * This file is part of AnoBarbeQBundle
 *
 * (c) anonymation <contact@anonymation.com>
 *
 */
namespace Ano\Bundle\BarbeQBundle\DependencyInjection;

use Ano\Bundle\BarbeQBundle\DependencyInjection\AdapterFactory\AdapterFactoryInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Extension for ano_barbeq
 *
 * @author Benjamin Dulau <benjamin.dulau@gmail.com>
 */
class AnoBarbeQExtension extends Extension
{
    protected $adapterFactories;

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();

        // Adapter factories configuration
        $factoryConfig = new AdapterFactoryConfiguration();
        $config = $processor->processConfiguration($factoryConfig, $configs);
        $adapterFactories = $this->createAdapterFactories($config, $container);

        // Main configuration
        $configuration = new Configuration($adapterFactories);
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('barbeq.xml');

        $adapterKeys = array_keys($config['adapter']);
        $adapterName = $adapterKeys[0];
        $adapterId = $this->createAdapter($adapterName, $config['adapter'][$adapterName], $container, $adapterFactories);

        $container
            ->getDefinition('ano_barbeq.barbeq')
            ->replaceArgument(0, new Reference($adapterId));
        ;
    }

    /**
     * @param string                           $name
     * @param array                            $config
     * @param ContainerBuilder                 $container
     * @param AdapterFactoryInterface[]|array  $adapterFactories
     *
     * @return string
     *
     * @throws \LogicException
     */
    private function createAdapter($name, array $config, ContainerBuilder $container, array $adapterFactories)
    {
        if (array_key_exists($name, $adapterFactories)) {
            $id = sprintf('ano_barbeq.%s_adapter', $name);
            $adapterFactories[$name]->create($container, $id, $config);

            return $id;
        }
    }

    /**
     * Creates the adapter factories
     *
     * @param  array            $config
     * @param  ContainerBuilder $container
     */
    private function createAdapterFactories($config, ContainerBuilder $container)
    {
        if (null !== $this->adapterFactories) {
            return $this->adapterFactories;
        }

        // load bundled adapter factories
        $tempContainer = new ContainerBuilder();
        $parameterBag = $container->getParameterBag();
        $loader = new XmlFileLoader($tempContainer, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('adapter_factories.xml');

        // load user-created adapter factories
        foreach ($config['factories'] as $factory) {
            $loader->load($parameterBag->resolveValue($factory));
        }

        $services  = $tempContainer->findTaggedServiceIds('ano_barbeq.adapter_factory');
        $factories = array();
        foreach (array_keys($services) as $id) {
            $factory = $tempContainer->get($id);
            $factories[str_replace('-', '_', $factory->getKey())] = $factory;
        }

        return $this->adapterFactories = $factories;
    }
}