<?php

namespace Api\SdkBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 *
 * @codeCoverageIgnore
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('api_sdk');

        $rootNode
            ->children()
                ->arrayNode('observer')
                    ->children()
                        ->scalarNode('root_dir')
                            ->isRequired()
                        ->end()

                        ->scalarNode('web_path')
                            ->defaultValue('/web')
                        ->end()

                        ->scalarNode('symfony_web_path')
                            ->defaultValue('/data/symfony/web')
                        ->end()

                        ->scalarNode('environment')
                            ->defaultValue('legacy_bridge')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end()
        ;

        return $treeBuilder;
    }
}
