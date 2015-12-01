<?php

namespace Zenstruck\RedirectBundle\Tests\Form;

use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpKernel\Kernel;
use Zenstruck\RedirectBundle\Form\Type\RedirectType;
use Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RedirectTypeTest extends TypeTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addType(new RedirectType('Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect'))
            ->getFormFactory();
    }

    public function testCreateDefault()
    {
        $form = $this->factory->create($this->createType());

        $this->assertTrue($form->has('source'));
        $this->assertTrue($form->has('destination'));
        $this->assertFalse($form->get('source')->isDisabled());
    }

    public function testCreateWithOptions()
    {
        $form = $this->factory->create($this->createType(), null, array('disable_source' => true));
        $this->assertTrue($form->get('source')->isDisabled());
    }

    public function testSubmitUpdate()
    {
        $redirect = new DummyRedirect('/baz', 'http://example.com');
        $form     = $this->factory->create($this->createType(), $redirect);
        $formData = array(
            'source'      => '/foo',
            'destination' => '/bar',
        );
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        $redirect = $form->getData();
        $this->assertInstanceOf('Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect', $redirect);
        $this->assertSame('/foo', $redirect->getSource());
        $this->assertSame('/bar', $redirect->getDestination());
    }

    public function testSubmitCreate()
    {
        $form     = $this->factory->create($this->createType());
        $formData = array(
            'source'      => '/foo',
            'destination' => '/bar',
        );
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        $redirect = $form->getData();
        $this->assertInstanceOf('Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect', $redirect);
        $this->assertSame('/foo', $redirect->getSource());
        $this->assertSame('/bar', $redirect->getDestination());
    }

    /**
     * @return string
     */
    private function createType()
    {
        if (version_compare(Kernel::VERSION, '2.8.0', '<')) {
            return 'zenstruck_redirect';
        }

        return RedirectType::class;
    }
}
