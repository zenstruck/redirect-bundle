<?php

namespace Zenstruck\RedirectBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
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
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('listener.xml');
        $loader->load('orm.xml');

        $container->setParameter('zenstruck_redirect.redirect_class', $mergedConfig['redirect_class']);
        $container->setParameter('zenstruck_redirect.model_manager_name', $mergedConfig['model_manager_name']);
    }
}
