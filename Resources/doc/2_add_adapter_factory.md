AnoBarbeQBundle - Adding an adapter factory
===========================================

Creating adapter configuration factory
--------------------------------------

First, you need to create a factory to configure your adapter in the DIC.

```PHP
<?php

namespace My\BarbeQBundle\DependencyInjection\AdapterFactory;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Factory for BarbeQ MemcachedAdapter adapter
 */
class MemcachedAdapterFactory implements AdapterFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create(ContainerBuilder $container, $id, array $config)
    {
        $container
            ->setDefinition($id, new DefinitionDecorator('my_barbeq.adapter.memcached'))
            ->addArgument(new Reference($config['memcached_service']))
            ->addArgument($config['options'])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getKey()
    {
        return 'memcached';
    }

    /**
     * {@inheritDoc}
     */
    public function addConfiguration(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('memcached_service')->isRequired()->cannotBeEmpty()->end()
                ->arrayNode('options')
                    ->children()
                        ->scalarNode('namespace')->default('barbeq')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
```

Create, tag and load your factory and adapter service definitions
-----------------------------------------------------------------

```XML
<?xml version="1.0" encoding="UTF-8"?>

<!-- src/My/BarbeQBundle/Resources/config/apadater_factories.xml -->
<container xmlns="http://symfony.com/schema/dic/services"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">
    <services>
        <service id="my_barbeq.adapter_factory.memcached" class="My\BarbeQBundle\DependencyInjection\AdapterFactory\MemcachedAdapterFactory">
            <tag name="ano_barbeq.adapter_factory" />
        </service>

        <service id="my_barbeq.adapter.memcached" class="My\BarbeQBundle\Adapter\MemcachedAdapter">
            <argument /> <!-- Memcached service id -->
            <argument /> <!-- Options -->
        </service>
    </services>
</container>
```

Add your factories service configuration file to ano_barbe_q config
-------------------------------------------------------------------

```YAML
#app/config/config.yml

ano_barbe_q:
    factories:
        - "%kerner.root_dir%/../src/My/BarbeQBundle/Resources/config/apadater_factories.xml"
```

Now just configure your adapter like any other and you're good to go
--------------------------------------------------------------------

```YAML
#app/config/config.yml

ano_barbe_q:
    factories:
        - "%kerner.root_dir%/../src/My/BarbeQBundle/Resources/config/apadater_factories.xml"
    adapter:
        memcached:
            memcached_service: memcached
            options: { namespace: barbeq_messages }
```
