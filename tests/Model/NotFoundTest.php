<?php

/*
 * This file is part of the zenstruck/redirect-bundle package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\RedirectBundle\Tests\Model;

use PHPUnit\Framework\TestCase;
use Zenstruck\RedirectBundle\Model\NotFound;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class NotFoundTest extends TestCase
{
    /**
     * @dataProvider pathProvider
     *
     * @test
     */
    public function constructor($path, $expectedPath): void
    {
        $notFound = $this->createNotFound($path, 'http://foobar.com/baz');

        $this->assertSame($expectedPath, $notFound->getPath());
        $this->assertNull($notFound->getReferer());
        $this->assertSame('http://foobar.com/baz', $notFound->getFullUrl());
        $this->assertEqualsWithDelta(\time(), $notFound->getTimestamp()->format('U'), 1);
    }

    public function pathProvider(): array
    {
        return [
            ['foo/bar', '/foo/bar'],
            ['/foo/bar/', '/foo/bar/'],
            ['foo', '/foo'],
            ['foo/bar ', '/foo/bar'],
            [' foo/bar/', '/foo/bar/'],
            ['   /foo', '/foo'],
            ['http://www.example.com/foo', '/foo'],
            ['http://www.example.com/', '/'],
            ['http://www.example.com', '/'],
            ['foo/bar?baz=true', '/foo/bar'],
            ['http://www.example.com/foo?baz=bar&foo=baz', '/foo'],
            ['http://www.example.com/foo?baz=bar&foo=baz#baz', '/foo'],
            ['/', '/'],
            ['', null],
        ];
    }

    private function createNotFound(string $path, string $fullUrl, ?string $referer = null, ?\DateTime $timestamp = null): NotFound
    {
        return $this->getMockForAbstractClass(
            NotFound::class,
            [$path, $fullUrl, $referer, $timestamp]
        );
    }
}
