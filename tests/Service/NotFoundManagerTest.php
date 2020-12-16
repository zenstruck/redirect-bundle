<?php

namespace Zenstruck\RedirectBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\RedirectBundle\Service\NotFoundManager;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class NotFoundManagerTest extends TestCase
{
    public const NOT_FOUND_DUMMY_CLASS = 'Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyNotFound';

    private $om;

    private $repository;

    /** @var NotFoundManager */
    private $notFoundManager;

    protected function setUp(): void
    {
        $this->om = $this->createMock('Doctrine\Persistence\ObjectManager');
        $this->repository = $this->createMock('Doctrine\Persistence\ObjectRepository');

        $this->notFoundManager = new NotFoundManager(self::NOT_FOUND_DUMMY_CLASS, $this->om);
    }

    /**
     * @test
     */
    public function create_not_found()
    {
        $this->om->expects($this->once())
            ->method('persist')
        ;

        $this->om->expects($this->once())
            ->method('flush')
        ;

        $request = Request::create('https://example.com/foo/bar?baz=foo', 'GET', [], [], [], ['HTTP_REFERER' => 'https://google.com']);

        $notFound = $this->notFoundManager->createFromRequest($request);

        $this->assertSame('/foo/bar', $notFound->getPath());
        $this->assertSame('https://example.com/foo/bar?baz=foo', $notFound->getFullUrl());
        $this->assertSame('https://google.com', $notFound->getReferer());
        $this->assertEqualsWithDelta(\time(), $notFound->getTimestamp()->format('U'), 1);
    }
}
