<?php

/*
 * This file is part of the zenstruck/redirect-bundle package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\RedirectBundle\Tests\Form;

use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Test\TypeTestCase;
use Zenstruck\RedirectBundle\Form\Type\RedirectType;
use Zenstruck\RedirectBundle\Tests\Fixture\Entity\DummyRedirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class RedirectTypeTest extends TypeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addType(new RedirectType(DummyRedirect::class))
            ->getFormFactory()
        ;
    }

    /**
     * @test
     */
    public function create_default(): void
    {
        $form = $this->factory->create(RedirectType::class);

        $this->assertTrue($form->has('source'));
        $this->assertTrue($form->has('destination'));
        $this->assertFalse($form->get('source')->isDisabled());
    }

    /**
     * @test
     */
    public function create_with_options(): void
    {
        $form = $this->factory->create(RedirectType::class, null, ['disable_source' => true]);
        $this->assertTrue($form->get('source')->isDisabled());
    }

    /**
     * @test
     */
    public function submit_update(): void
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
        $this->assertInstanceOf(DummyRedirect::class, $redirect);
        $this->assertSame('/foo', $redirect->getSource());
        $this->assertSame('/bar', $redirect->getDestination());
    }

    /**
     * @test
     */
    public function submit_create(): void
    {
        $form = $this->factory->create(RedirectType::class);
        $formData = [
            'source' => '/foo',
            'destination' => '/bar',
        ];
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        $redirect = $form->getData();
        $this->assertInstanceOf(DummyRedirect::class, $redirect);
        $this->assertSame('/foo', $redirect->getSource());
        $this->assertSame('/bar', $redirect->getDestination());
    }
}
