<?php

namespace Kami\ApiCoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kami_api_core');
        $rootNode->children()
            ->arrayNode('resources')->isRequired()
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('name')->isRequired()->end()
                        ->scalarNode('entity')->isRequired()->end()
                        ->scalarNode('default_sort')->defaultValue('id')->cannotBeEmpty()->end()
                        ->scalarNode('default_sort_direction')->defaultValue('asc')->cannotBeEmpty()->end()
                        ->scalarNode('request_processor')
                            ->cannotBeEmpty()
                            ->defaultValue('kami.api_core.request_processor.default')
                        ->end()
                        ->arrayNode('strategies')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('index')
                                    ->defaultValue('kami.api_core.strategy.default.index')
                                    ->end()
                                ->scalarNode('item')
                                    ->defaultValue('kami.api_core.strategy.default.item')
                                    ->end()
                                ->scalarNode('filter')
                                    ->defaultValue('kami.api_core.strategy.default.filter')
                                    ->end()
                                ->scalarNode('create')
                                    ->defaultValue('kami.api_core.strategy.default.create')
                                    ->end()
                                ->scalarNode('update')
                                    ->defaultValue('kami.api_core.strategy.default.update')
                                    ->end()
                                ->scalarNode('delete')
                                    ->defaultValue('kami.api_core.strategy.default.delete')
                                    ->end()
                                ->scalarNode('my')
                                    ->defaultValue('kami.api_core.strategy.default.my')
                                    ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('locales')->isRequired()
                ->scalarPrototype()->isRequired()->end()
            ->end()
            ->arrayNode('pagination')->addDefaultsIfNotSet()
                ->children()
                    ->integerNode('per_page')
                        ->isRequired()
                        ->defaultValue(10)
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
