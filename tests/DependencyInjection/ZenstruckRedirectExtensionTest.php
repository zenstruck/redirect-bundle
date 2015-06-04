<?php

namespace Zenstruck\RedirectBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Zenstruck\RedirectBundle\DependencyInjection\ZenstruckRedirectExtension;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ZenstruckRedirectExtensionTest extends AbstractExtensionTestCase
{
    public function testValidConfig()
    {
        $this->load(array('redirect_class' => 'Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect'));
        $this->compile();

        $this->assertContainerBuilderHasService('zenstruck_redirect.redirect_manager');
        $this->assertContainerBuilderHasService('zenstruck_redirect.entity_manager');
        $this->assertContainerBuilderHasService('zenstruck_redirect.not_found_listener');
        $this->assertContainerBuilderHasService('zenstruck_redirect.redirect.form.type');
    }

    /**
     * @dataProvider invalidClassProvider
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testInvalidRedirectClass($class)
    {
        $this->load(array('redirect_class' => $class));
    }

    public function invalidClassProvider()
    {
        return array(
            array(null),
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
