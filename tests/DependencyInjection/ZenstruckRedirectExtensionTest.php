<?php

namespace Zenstruck\RedirectBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Zenstruck\RedirectBundle\DependencyInjection\ZenstruckRedirectExtension;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ZenstruckRedirectExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage A "redirect_class" or "not_found_class" must be set for "zenstruck_redirect".
     */
    public function testNoClassesSet()
    {
        $this->load(array());
        $this->compile();
    }

    public function testRedirectClass()
    {
        $this->load(array('redirect_class' => 'Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect'));
        $this->compile();

        $this->assertContainerBuilderHasService('zenstruck_redirect.redirect_manager');
        $this->assertContainerBuilderHasService('zenstruck_redirect.entity_manager');
        $this->assertContainerBuilderHasService('zenstruck_redirect.redirect_listener');
        $this->assertContainerBuilderHasService('zenstruck_redirect.redirect.form.type');
    }

    public function testNotFoundClass()
    {
        $this->load(array('not_found_class' => 'Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyNotFound'));
        $this->compile();

        $this->assertContainerBuilderHasService('zenstruck_redirect.not_found_manager');
        $this->assertContainerBuilderHasService('zenstruck_redirect.entity_manager');
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
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testInvalidRedirectClass($class)
    {
        $this->load(array('redirect_class' => $class));
    }

    /**
     * @dataProvider invalidClassProvider
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testInvalidNotFoundClass($class)
    {
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
    protected function getContainerExtensions()
    {
        return array(new ZenstruckRedirectExtension());
    }
}
