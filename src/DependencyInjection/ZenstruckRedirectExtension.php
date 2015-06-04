<?php

namespace Zenstruck\RedirectBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
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
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('listener.xml');
        $loader->load('orm.xml');

        if (class_exists('Symfony\Component\Form\Form')) {
            $loader->load('form.xml');
        }

        $container->setParameter('zenstruck_redirect.redirect_class', $mergedConfig['redirect_class']);
        $container->setParameter('zenstruck_redirect.model_manager_name', $mergedConfig['model_manager_name']);

        $definition = $container->getDefinition('zenstruck_redirect.entity_manager');

        if (method_exists($definition, 'setFactory')) {
            // Symfony 2.6+
            $definition->setFactory(array(new Reference('doctrine'), 'getManager'));
        } else {
            // Symfony < 2.6
            $definition->setFactoryService('doctrine');
            $definition->setFactoryMethod('getManager');
        }
    }
}
