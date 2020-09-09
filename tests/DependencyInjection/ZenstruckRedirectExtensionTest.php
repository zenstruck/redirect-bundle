<?php

namespace Zenstruck\RedirectBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Zenstruck\RedirectBundle\DependencyInjection\ZenstruckRedirectExtension;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ZenstruckRedirectExtensionTest extends AbstractExtensionTestCase
{
    public function testNoClassesSet()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('A "redirect_class" or "not_found_class" must be set for "zenstruck_redirect".');

        $this->load(array());
        $this->compile();
    }

    public function testRedirectClass()
    {
        $this->load(array('redirect_class' => 'Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect'));
        $this->compile();

        $this->assertContainerBuilderHasService('zenstruck_redirect.redirect_manager');
        $this->assertContainerBuilderHasAlias('zenstruck_redirect.entity_manager', 'doctrine.orm.default_entity_manager');
        $this->assertContainerBuilderHasService('zenstruck_redirect.redirect_listener');
        $this->assertContainerBuilderHasService('zenstruck_redirect.redirect.form.type');
    }

    public function testCustomModelManagerName()
    {
        $this->load(array(
            'redirect_class' => 'Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect',
            'model_manager_name' => 'foo',
        ));
        $this->compile();

        $this->assertContainerBuilderHasAlias('zenstruck_redirect.entity_manager', 'doctrine.orm.foo_entity_manager');
    }

    public function testNotFoundClass()
    {
        $this->load(array('not_found_class' => 'Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyNotFound'));
        $this->compile();

        $this->assertContainerBuilderHasService('zenstruck_redirect.not_found_manager');
        $this->assertContainerBuilderHasAlias('zenstruck_redirect.entity_manager', 'doctrine.orm.default_entity_manager');
        $this->assertContainerBuilderHasService('zenstruck_redirect.not_found_listener');
    }

    public function testRemoveNotFoundSubscriber()
    {
        $this->load(array(
            'not_found_class' => 'Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyNotFound',
            'redirect_class' => 'Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect',
        ));
        $this->compile();

        $this->assertContainerBuilderHasService('zenstruck_redirect.remove_not_found_subscriber');
    }

    public function testDisableRemoveNotFoundSubscriber()
    {
        $this->load(array(
            'not_found_class' => 'Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyNotFound',
            'redirect_class' => 'Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect',
            'remove_not_founds' => false,
        ));
        $this->compile();

        $this->assertContainerBuilderNotHasService('zenstruck_redirect.remove_not_found_subscriber');
    }

    /**
     * @dataProvider invalidClassProvider
     */
    public function testInvalidRedirectClass($class)
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->load(array('redirect_class' => $class));
    }

    /**
     * @dataProvider invalidClassProvider
     */
    public function testInvalidNotFoundClass($class)
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->load(array('not_found_class' => $class));
    }

    public function invalidClassProvider()
    {
        return array(
            array('Foo\Bar'),
            array(get_class($this)),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions(): array
    {
        return array(new ZenstruckRedirectExtension());
    }
}
