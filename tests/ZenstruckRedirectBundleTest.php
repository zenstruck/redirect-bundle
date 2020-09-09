<?php

namespace Zenstruck\RedirectBundle\Tests;

use PHPUnit\Framework\TestCase;
use Zenstruck\RedirectBundle\ZenstruckRedirectBundle;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ZenstruckRedirectBundleTest extends TestCase
{
    /**
     * @test
     */
    public function build()
    {
        $container = $this
            ->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->setMethods(['addCompilerPass'])
            ->getMock()
        ;
        $container
            ->expects($this->once())
            ->method('addCompilerPass')
            ->with($this->isInstanceOf('Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass'))
        ;

        $bundle = new ZenstruckRedirectBundle();
        $bundle->build($container);
    }
}
