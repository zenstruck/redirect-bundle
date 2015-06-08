<?php

namespace Zenstruck\RedirectBundle\Tests\Form;

use Symfony\Component\Form\Test\TypeTestCase;
use Zenstruck\RedirectBundle\Form\Type\RedirectType;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RedirectTypeTest extends TypeTestCase
{
    public function testCreateDefault()
    {
        $form = $this->factory->create($this->createType());
        $this->assertTrue($form->has('source'));
        $this->assertTrue($form->has('destination'));
        $this->assertFalse($form->has('status_code'));
        $this->assertFalse($form->get('source')->isDisabled());
        $this->assertFalse($form->get('source')->getConfig()->getOption('read_only'));
    }

    public function testCreateWithOptions()
    {
        $form = $this->factory->create($this->createType(), null, array('status_code' => true, 'disable_source' => true));
        $this->assertTrue($form->has('status_code'));
        $this->assertTrue($form->get('source')->isDisabled());
        $this->assertTrue($form->get('source')->getConfig()->getOption('read_only'));
    }

    /**
     * @return RedirectType
     */
    private function createType()
    {
        return new RedirectType('Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect');
    }
}
