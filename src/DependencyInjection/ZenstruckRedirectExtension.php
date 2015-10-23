<?php

namespace Zenstruck\RedirectBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ZenstruckRedirectExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        if (null === $mergedConfig['redirect_class'] && null === $mergedConfig['not_found_class']) {
            throw new InvalidConfigurationException('A "redirect_class" or "not_found_class" must be set for "zenstruck_redirect".');
        }

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('orm.xml');

        $container->setParameter('zenstruck_redirect.model_manager_name', $mergedConfig['model_manager_name']);

        $emDefinition = $container->getDefinition('zenstruck_redirect.entity_manager');

        if (method_exists($emDefinition, 'setFactory')) {
            // Symfony 2.6+
            $emDefinition->setFactory(array(new Reference('doctrine'), 'getManager'));
        } else {
            // Symfony < 2.6
            $emDefinition->setFactoryService('doctrine');
            $emDefinition->setFactoryMethod('getManager');
        }

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
