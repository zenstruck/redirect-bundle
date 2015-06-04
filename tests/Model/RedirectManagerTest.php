<?php

namespace Zenstruck\RedirectBundle\Tests\Model;

use Zenstruck\RedirectBundle\Model\RedirectManager;
use Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RedirectManagerTest extends \PHPUnit_Framework_TestCase
{
    const REDIRECT_DUMMY_CLASS = 'Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect';

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $om;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $repository;

    /** @var RedirectManager */
    private $redirectManager;

    public function setUp()
    {
        $this->om = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');

        $this->om->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(self::REDIRECT_DUMMY_CLASS))
            ->will($this->returnValue($this->repository));

        $this->om->expects($this->once())
            ->method('flush');

        $this->redirectManager = new RedirectManager(self::REDIRECT_DUMMY_CLASS, $this->om);
    }

    public function testCreateRedirect()
    {
        $this->om->expects($this->once())
            ->method('persist');

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(array('source' => '/foo'))
            ->willReturn(null);

        $redirect = $this->redirectManager->updateOrCreate('/foo');

        $this->assertInstanceOf('Zenstruck\RedirectBundle\Model\Redirect', $redirect);
        $this->assertSame('/foo', $redirect->getSource());
        $this->assertNull($redirect->getDestination());
        $this->assertSame(404, $redirect->getStatusCode());
        $this->assertSame(1, $redirect->getCount());
        $this->assertEquals(time(), $redirect->getLastAccessed()->format('U'));
    }

    public function testUpdateRedirect()
    {
        $redirect = new DummyRedirect('/foo');
        $redirect->increaseCount(5);

        sleep(2);

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(array('source' => '/foo'))
            ->will($this->returnValue($redirect));

        $redirect = $this->redirectManager->updateOrCreate('/foo');

        $this->assertSame(6, $redirect->getCount());
        $this->assertEquals(time(), $redirect->getLastAccessed()->format('U'));
    }
}
