<?php

namespace Zenstruck\Bundle\RedirectBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;

class ZenstruckRedirectExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('redirect.xml');

        $container->getDefinition('zenstruck_redirect.manager')
                ->replaceArgument(2, $config);

        $container->getDefinition('zenstruck_redirect.listener')
                ->replaceArgument(2, $config['redirect_template'])
                ->replaceArgument(3, $config['log_statistics'])
                ->replaceArgument(4, $config['log_404_errors']);
    }

}
