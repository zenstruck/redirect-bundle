<?php

namespace Zenstruck\RedirectBundle\Tests\Form;

use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Test\TypeTestCase;
use Zenstruck\RedirectBundle\Form\Type\RedirectType;
use Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RedirectTypeTest extends TypeTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addType(new RedirectType('Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect'))
            ->getFormFactory()
        ;
    }

    /**
     * @test
     */
    public function create_default()
    {
        $form = $this->factory->create(RedirectType::class);

        $this->assertTrue($form->has('source'));
        $this->assertTrue($form->has('destination'));
        $this->assertFalse($form->get('source')->isDisabled());
    }

    /**
     * @test
     */
    public function create_with_options()
    {
        $form = $this->factory->create(RedirectType::class, null, ['disable_source' => true]);
        $this->assertTrue($form->get('source')->isDisabled());
    }

    /**
     * @test
     */
    public function submit_update()
    {
        $redirect = new DummyRedirect('/baz', 'http://example.com');
        $form = $this->factory->create(RedirectType::class, $redirect);
        $formData = [
            'source' => '/foo',
            'destination' => '/bar',
        ];
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        $redirect = $form->getData();
        $this->assertInstanceOf('Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect', $redirect);
        $this->assertSame('/foo', $redirect->getSource());
        $this->assertSame('/bar', $redirect->getDestination());
    }

    /**
     * @test
     */
    public function submit_create()
    {
        $form = $this->factory->create(RedirectType::class);
        $formData = [
            'source' => '/foo',
            'destination' => '/bar',
        ];
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        $redirect = $form->getData();
        $this->assertInstanceOf('Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect', $redirect);
        $this->assertSame('/foo', $redirect->getSource());
        $this->assertSame('/bar', $redirect->getDestination());
    }
}
