<?php

namespace Zenstruck\RedirectBundle\Tests\Functional;

use Symfony\Component\Validator\Validator\RecursiveValidator;
use Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ValidationTest extends FunctionalTest
{
    /**
     * @test
     */
    public function validation()
    {
        /** @var RecursiveValidator $validator */
        $validator = self::getContainer()->get('validator');

        $this->assertCount(0, $validator->validate(DummyRedirect::create('/foo', '/bar')));

        $errors = $validator->validate(DummyRedirect::create('/301-redirect', '/foo'));
        $this->assertCount(1, $errors);
        $this->assertSame('The source is already used.', $errors[0]->getMessage());
        $this->assertSame('source', $errors[0]->getPropertyPath());
    }
}
