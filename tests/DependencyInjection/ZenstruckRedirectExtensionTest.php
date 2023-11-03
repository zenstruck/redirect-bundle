<?php

/*
 * This file is part of the zenstruck/redirect-bundle package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\RedirectBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Zenstruck\RedirectBundle\DependencyInjection\ZenstruckRedirectExtension;
use Zenstruck\RedirectBundle\Tests\Fixture\Entity\DummyNotFound;
use Zenstruck\RedirectBundle\Tests\Fixture\Entity\DummyRedirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ZenstruckRedirectExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function no_classes_set(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('A "redirect_class" or "not_found_class" must be set for "zenstruck_redirect".');

        $this->load([]);
        $this->compile();
    }

    /**
     * @test
     */
    public function redirect_class(): void
    {
        $this->load(['redirect_class' => DummyRedirect::class]);
        $this->compile();

        $this->assertContainerBuilderHasService('zenstruck_redirect.redirect_manager');
        $this->assertContainerBuilderHasAlias('zenstruck_redirect.entity_manager', 'doctrine.orm.default_entity_manager');
        $this->assertContainerBuilderHasService('zenstruck_redirect.redirect_listener');
        $this->assertContainerBuilderHasService('zenstruck_redirect.redirect.form.type');
    }

    /**
     * @test
     */
    public function custom_model_manager_name(): void
    {
        $this->load([
            'redirect_class' => DummyRedirect::class,
            'model_manager_name' => 'foo',
        ]);
        $this->compile();

        $this->assertContainerBuilderHasAlias('zenstruck_redirect.entity_manager', 'doctrine.orm.foo_entity_manager');
    }

    /**
     * @test
     */
    public function not_found_class(): void
    {
        $this->load(['not_found_class' => DummyNotFound::class]);
        $this->compile();

        $this->assertContainerBuilderHasService('zenstruck_redirect.not_found_manager');
        $this->assertContainerBuilderHasAlias('zenstruck_redirect.entity_manager', 'doctrine.orm.default_entity_manager');
        $this->assertContainerBuilderHasService('zenstruck_redirect.not_found_listener');
    }

    /**
     * @test
     */
    public function remove_not_found_subscriber(): void
    {
        $this->load([
            'not_found_class' => DummyNotFound::class,
            'redirect_class' => DummyRedirect::class,
        ]);
        $this->compile();

        $this->assertContainerBuilderHasService('zenstruck_redirect.remove_not_found_subscriber');
    }

    /**
     * @test
     */
    public function disable_remove_not_found_subscriber(): void
    {
        $this->load([
            'not_found_class' => DummyNotFound::class,
            'redirect_class' => DummyRedirect::class,
            'remove_not_founds' => false,
        ]);
        $this->compile();

        $this->assertContainerBuilderNotHasService('zenstruck_redirect.remove_not_found_subscriber');
    }

    /**
     * @dataProvider invalidClassProvider
     *
     * @test
     */
    public function invalid_redirect_class($class): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->load(['redirect_class' => $class]);
    }

    /**
     * @dataProvider invalidClassProvider
     *
     * @test
     */
    public function invalid_not_found_class($class): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->load(['not_found_class' => $class]);
    }

    public function invalidClassProvider(): array
    {
        return [
            ['Foo\Bar'],
            [static::class],
        ];
    }

    protected function getContainerExtensions(): array
    {
        return [new ZenstruckRedirectExtension()];
    }
}
