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

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\RedirectBundle\Tests\Fixture\Entity\DummyNotFound;
use Zenstruck\RedirectBundle\Tests\Fixture\Entity\DummyRedirect;
use function Zenstruck\Foundry\Persistence\persist;
use function Zenstruck\Foundry\Persistence\persist_proxy;
use function Zenstruck\Foundry\Persistence\repository;


/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class RemoveNotFoundSubscriberTest extends KernelTestCase
{
    use Factories, ResetDatabase;

    /**
     * @test
     */
    public function delete_not_found_on_create_redirect(): void
    {
        $notFoundRepo = repository(DummyNotFound::class);

        persist(DummyNotFound::class, ['path' => '/foo', 'fullUrl' => '/foo']);

        $notFoundRepo->assert()->count(1);

        $em = self::getContainer()->get(EntityManagerInterface::class);
        $em->persist(new DummyRedirect('/foo', '/bar'));
        $em->flush();

        $notFoundRepo->assert()->empty();
    }

    /**
     * @test
     */
    public function delete_not_found_on_update_redirect(): void
    {
        $notFoundRepo = repository(DummyNotFound::class);

        persist(DummyNotFound::class, ['path' => '/foo', 'fullUrl' => '/foo']);

        $redirect = persist_proxy(DummyRedirect::class, ['source' => '/baz', 'destination' => '/bar']);

        $notFoundRepo->assert()->count(1);

        $redirect->setSource('/foo');
        $redirect->_save();

        $notFoundRepo->assert()->empty();
    }
}
