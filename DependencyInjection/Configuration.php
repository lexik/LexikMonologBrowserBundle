<?php

namespace Lexik\Bundle\MonologBrowserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('lexik_monolog_browser');

        $rootNode
            ->children()
                ->scalarNode('base_layout')
                    ->cannotBeEmpty()
                    ->defaultValue('LexikMonologBrowserBundle::layout.html.twig')
                ->end()
                ->scalarNode('logs_per_page')
                    ->cannotBeEmpty()
                    ->defaultValue(25)
                    ->beforeNormalization()
                    ->ifString()
                        ->then(function($v) { return (int) $v; })
                    ->end()
                ->end()
                ->arrayNode('doctrine')
                    ->children()
                        ->scalarNode('table_name')
                            ->defaultValue('monolog_entries')
                        ->end()
                        ->scalarNode('connection_name')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
