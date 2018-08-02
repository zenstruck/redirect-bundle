<?php

namespace Zenstruck\RedirectBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Zenstruck\RedirectBundle\Service\RedirectManager;
use Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RedirectManagerTest extends TestCase
{
    const REDIRECT_DUMMY_CLASS = 'Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect';

    private $om;

    private $repository;

    /** @var RedirectManager */
    private $redirectManager;

    public function setUp()
    {
        $this->om = $this->createMock('Doctrine\Common\Persistence\ObjectManager');
        $this->repository = $this->createMock('Doctrine\Common\Persistence\ObjectRepository');

        $this->om->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(self::REDIRECT_DUMMY_CLASS))
            ->will($this->returnValue($this->repository));

        $this->redirectManager = new RedirectManager(self::REDIRECT_DUMMY_CLASS, $this->om);
    }

    public function testUpdateRedirect()
    {
        $redirect = new DummyRedirect('/foo', '/bar');
        $redirect->increaseCount(5);
        $this->assertNull($redirect->getLastAccessed());

        $this->om->expects($this->once())
            ->method('flush');

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(array('source' => '/foo'))
            ->will($this->returnValue($redirect));

        $redirect = $this->redirectManager->findAndUpdate('/foo');

        $this->assertSame(6, $redirect->getCount());
        $this->assertEquals(time(), $redirect->getLastAccessed()->format('U'), '', 1);
    }

    public function testNoRedirectFound()
    {
        $this->om->expects($this->never())
            ->method('flush');

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(array('source' => '/foo'))
            ->will($this->returnValue(null));

        $redirect = $this->redirectManager->findAndUpdate('/foo');

        $this->assertNull($redirect);
    }
}
