<?php

/*
 * This file is part of the zenstruck/redirect-bundle package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\RedirectBundle\Tests\Service;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Zenstruck\RedirectBundle\Service\RedirectManager;
use Zenstruck\RedirectBundle\Tests\Fixture\Entity\DummyRedirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class RedirectManagerTest extends TestCase
{
    public const REDIRECT_DUMMY_CLASS = DummyRedirect::class;

    /** @var MockObject&ObjectManager */
    private $om;

    /** @var MockObject&ObjectRepository */
    private $repository;

    private RedirectManager $redirectManager;

    protected function setUp(): void
    {
        $this->om = $this->createMock(ObjectManager::class);
        $this->repository = $this->createMock(ObjectRepository::class);

        $this->om->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(self::REDIRECT_DUMMY_CLASS))
            ->willReturn($this->repository)
        ;

        $this->redirectManager = new RedirectManager(self::REDIRECT_DUMMY_CLASS, $this->om);
    }

    /**
     * @test
     */
    public function update_redirect(): void
    {
        $redirect = new DummyRedirect('/foo', '/bar');
        $redirect->increaseCount(5);
        $this->assertNull($redirect->getLastAccessed());

        $this->om->expects($this->once())
            ->method('flush')
        ;

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(['source' => '/foo'])
            ->willReturn($redirect)
        ;

        $redirect = $this->redirectManager->findAndUpdate('/foo');

        $this->assertSame(6, $redirect->getCount());
        $this->assertEqualsWithDelta(\time(), $redirect->getLastAccessed()->format('U'), 1);
    }

    /**
     * @test
     */
    public function no_redirect_found(): void
    {
        $this->om->expects($this->never())
            ->method('flush')
        ;

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(['source' => '/foo'])
            ->willReturn(null)
        ;

        $redirect = $this->redirectManager->findAndUpdate('/foo');

        $this->assertNull($redirect);
    }
}
