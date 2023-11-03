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

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class ZenstruckRedirectExtension extends ConfigurableExtension
{
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        if (null === $mergedConfig['redirect_class'] && null === $mergedConfig['not_found_class']) {
            throw new InvalidConfigurationException('A "redirect_class" or "not_found_class" must be set for "zenstruck_redirect".');
        }

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $modelManagerName = $mergedConfig['model_manager_name'] ?: 'default';

        $container->setAlias('zenstruck_redirect.entity_manager', \sprintf('doctrine.orm.%s_entity_manager', $modelManagerName));

        if (null !== $mergedConfig['redirect_class']) {
            $container->setParameter('zenstruck_redirect.redirect_class', $mergedConfig['redirect_class']);

            $loader->load('redirect.xml');
            $loader->load('form.xml');
        }

        if (null !== $mergedConfig['not_found_class']) {
            $container->setParameter('zenstruck_redirect.not_found_class', $mergedConfig['not_found_class']);

            $loader->load('not_found.xml');
        }

        if ($mergedConfig['remove_not_founds'] && null !== $mergedConfig['not_found_class'] && null !== $mergedConfig['redirect_class']) {
            $loader->load('remove_not_found_subscriber.xml');
        }
    }
}
