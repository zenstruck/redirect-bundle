<?php

namespace Zenstruck\Bundle\RedirectBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('zenstruck_redirect');

        $rootNode
            ->children()
                ->booleanNode('log_statistics')->defaultFalse()->end()
                ->booleanNode('log_404_errors')->defaultFalse()->end()
            ->end()
        ;

        return $treeBuilder;
    }

}
