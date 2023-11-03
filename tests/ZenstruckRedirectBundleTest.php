<?php

/*
 * This file is part of the zenstruck/redirect-bundle package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\RedirectBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Zenstruck\RedirectBundle\ZenstruckRedirectBundle;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ZenstruckRedirectBundleTest extends TestCase
{
    /**
     * @test
     */
    public function build(): void
    {
        $container = $this
            ->getMockBuilder(ContainerBuilder::class)
            ->setMethods(['addCompilerPass'])
            ->getMock()
        ;
        $container
            ->expects($this->once())
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(DoctrineOrmMappingsPass::class))
        ;

        $bundle = new ZenstruckRedirectBundle();
        $bundle->build($container);
    }
}
