<?php

namespace Zenstruck\RedirectBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ZenstruckRedirectBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $this->addRegisterMappingsPass($container);
    }

    private function addRegisterMappingsPass(ContainerBuilder $container): void
    {
        $mappings = [
            \realpath(__DIR__.'/Resources/config/doctrine-mapping') => 'Zenstruck\RedirectBundle\Model',
        ];

        $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings));
    }
}
