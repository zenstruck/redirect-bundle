<?php

/*
 * This file is part of the zenstruck/redirect-bundle package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\RedirectBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\RedirectBundle\Tests\Fixture\Entity\DummyRedirect;

use function Zenstruck\Foundry\create;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ValidationTest extends KernelTestCase
{
    use Factories, ResetDatabase;

    /**
     * @test
     */
    public function validation(): void
    {
        create(DummyRedirect::class, ['source' => '/301-redirect', 'destination' => '/bar']);

        /** @var RecursiveValidator $validator */
        $validator = self::getContainer()->get('validator');

        $this->assertCount(0, $validator->validate(new DummyRedirect('/foo', '/bar')));

        $errors = $validator->validate(new DummyRedirect('/301-redirect', '/foo'));
        $this->assertCount(1, $errors);
        $this->assertSame('The source is already used.', $errors[0]->getMessage());
        $this->assertSame('source', $errors[0]->getPropertyPath());
    }
}
