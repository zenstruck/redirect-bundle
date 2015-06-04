<?php

namespace Zenstruck\RedirectBundle\Tests;

use Zenstruck\RedirectBundle\ZenstruckRedirectBundle;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ZenstruckRedirectBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $container = $this
            ->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->setMethods(array('addCompilerPass'))
            ->getMock();
        $container
            ->expects($this->once())
            ->method('addCompilerPass')
            ->with($this->isInstanceOf('Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass'));

        $bundle = new ZenstruckRedirectBundle();
        $bundle->build($container);
    }
}
