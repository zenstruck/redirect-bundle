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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\RedirectBundle\Service\NotFoundManager;
use Zenstruck\RedirectBundle\Tests\Fixture\Entity\DummyNotFound;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class NotFoundManagerTest extends TestCase
{
    public const NOT_FOUND_DUMMY_CLASS = DummyNotFound::class;

    /** @var MockObject&ObjectManager */
    private $om;

    private NotFoundManager $notFoundManager;

    protected function setUp(): void
    {
        $this->om = $this->createMock(ObjectManager::class);

        $this->notFoundManager = new NotFoundManager(self::NOT_FOUND_DUMMY_CLASS, $this->om);
    }

    /**
     * @test
     */
    public function create_not_found(): void
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
