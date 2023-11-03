<?php

/*
 * This file is part of the zenstruck/redirect-bundle package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\RedirectBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('zenstruck_redirect');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('redirect_class')
                    ->defaultNull()
                    ->validate()
                        ->ifTrue(fn($value) => !\is_subclass_of($value, 'Zenstruck\RedirectBundle\Model\Redirect'))
                        ->thenInvalid('"redirect_class" must be an instance of "Zenstruck\RedirectBundle\Model\Redirect"')
                    ->end()
                ->end()
                ->scalarNode('not_found_class')
                    ->defaultNull()
                    ->validate()
                        ->ifTrue(fn($value) => !\is_subclass_of($value, 'Zenstruck\RedirectBundle\Model\NotFound'))
                        ->thenInvalid('"not_found_class" must be an instance of "Zenstruck\RedirectBundle\Model\NotFound"')
                    ->end()
                ->end()
                ->booleanNode('remove_not_founds')
                    ->info('When enabled, when a redirect is updated or created, the NotFound entites with a matching path are removed.')
                    ->defaultTrue()
                ->end()
                ->scalarNode('model_manager_name')->defaultNull()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
