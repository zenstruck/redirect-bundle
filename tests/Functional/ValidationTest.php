<?php

namespace Zenstruck\RedirectBundle\Tests\Functional;

use Symfony\Component\Validator\Validator\RecursiveValidator;
use Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ValidationTest extends FunctionalTest
{
    public function testValidation()
    {
        /** @var RecursiveValidator $validator */
        $validator = self::$kernel->getContainer()->get('validator');

        $this->assertCount(0, $validator->validate(new DummyRedirect('/foo', '/bar')));

        $errors = $validator->validate(new DummyRedirect(null, null));
        $this->assertCount(2, $errors);
        $this->assertSame('Please enter a source.', $errors[0]->getMessage());
        $this->assertSame('source', $errors[0]->getPropertyPath());
        $this->assertSame('Please enter a destination.', $errors[1]->getMessage());
        $this->assertSame('destination', $errors[1]->getPropertyPath());

        $errors = $validator->validate(new DummyRedirect('/301-redirect', '/foo'));
        $this->assertCount(1, $errors);
        $this->assertSame('The source is already used.', $errors[0]->getMessage());
        $this->assertSame('source', $errors[0]->getPropertyPath());
    }
}
