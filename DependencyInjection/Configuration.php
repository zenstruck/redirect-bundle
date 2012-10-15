<?php

/*
 * This file is part of the ZenstruckRedirectBundle package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
                ->scalarNode('redirect_class')->isRequired()->end()
                ->booleanNode('allow_404_query_params')->defaultFalse()->end()
                ->booleanNode('log_statistics')->defaultFalse()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
