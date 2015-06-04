<?php

namespace Zenstruck\RedirectBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('zenstruck_redirect');

        $rootNode
            ->children()
                ->scalarNode('redirect_class')
                    ->isRequired()
                    ->validate()
                        ->ifTrue(function ($value) {
                            return !class_exists($value);
                        })
                        ->thenInvalid('Class "%s" does not exist')
                    ->end()
                ->end()
                ->scalarNode('model_manager_name')->defaultNull()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
