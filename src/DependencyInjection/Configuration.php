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
        $treeBuilder = new TreeBuilder('zenstruck_redirect');

        // Keep compatibility with symfony/config < 4.2
        if (\method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $rootNode = $treeBuilder->root('zenstruck_redirect');
        }

        $rootNode
            ->children()
                ->scalarNode('redirect_class')
                    ->defaultNull()
                    ->validate()
                        ->ifTrue(function ($value) {
                            return !is_subclass_of($value, 'Zenstruck\RedirectBundle\Model\Redirect');
                        })
                        ->thenInvalid('"redirect_class" must be an instance of "Zenstruck\RedirectBundle\Model\Redirect"')
                    ->end()
                ->end()
                ->scalarNode('not_found_class')
                    ->defaultNull()
                    ->validate()
                        ->ifTrue(function ($value) {
                            return !is_subclass_of($value, 'Zenstruck\RedirectBundle\Model\NotFound');
                        })
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
