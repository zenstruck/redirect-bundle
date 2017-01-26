<?php

namespace Zenstruck\RedirectBundle\Tests\Service;

use Symfony\Component\HttpFoundation\Request;
use Zenstruck\RedirectBundle\Service\NotFoundManager;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class NotFoundManagerTest extends \PHPUnit_Framework_TestCase
{
    const NOT_FOUND_DUMMY_CLASS = 'Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyNotFound';

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $om;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $repository;

    /** @var NotFoundManager */
    private $notFoundManager;

    public function setUp()
    {
        $this->om = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');

        $this->notFoundManager = new NotFoundManager(self::NOT_FOUND_DUMMY_CLASS, $this->om);
    }

    public function testCreateNotFound()
    {
        $this->om->expects($this->once())
            ->method('persist');

        $this->om->expects($this->once())
            ->method('flush');

        $request = Request::create('https://example.com/foo/bar?baz=foo', 'GET', array(), array(), array(), array('HTTP_REFERER' => 'https://google.com'));

        $notFound = $this->notFoundManager->createFromRequest($request);

        $this->assertSame('/foo/bar', $notFound->getPath());
        $this->assertSame('https://example.com/foo/bar?baz=foo', $notFound->getFullUrl());
        $this->assertSame('https://google.com', $notFound->getReferer());
        $this->assertEquals(time(), $notFound->getTimestamp()->format('U'), '', 1);
    }
}
