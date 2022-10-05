<?php

namespace Zenstruck\RedirectBundle\Tests\Model;

use PHPUnit\Framework\TestCase;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class NotFoundTest extends TestCase
{
    /**
     * @dataProvider pathProvider
     *
     * @test
     */
    public function constructor($path, $expectedPath)
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
        ];
    }

    private function createNotFound(string $path, string $fullUrl, ?string $referer = null, ?\DateTime $timestamp = null): \Zenstruck\RedirectBundle\Model\NotFound
    {
        $mock = $this->getMockForAbstractClass('Zenstruck\RedirectBundle\Model\NotFound');

        return $mock::create($path, $fullUrl, $referer, $timestamp);
    }
}
