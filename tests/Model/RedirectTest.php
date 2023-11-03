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
use Zenstruck\RedirectBundle\Model\Redirect;
use Zenstruck\RedirectBundle\Tests\Fixture\Entity\DummyNotFound;
use Zenstruck\RedirectBundle\Tests\Fixture\Entity\DummyRedirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class RedirectTest extends TestCase
{
    /**
     * @dataProvider sourceProvider
     *
     * @test
     */
    public function set_source($source, $expected): void
    {
        $redirect = $this->createRedirect($source, '/foo');

        $this->assertSame($expected, $redirect->getSource());
    }

    /**
     * @dataProvider destinationProvider
     *
     * @test
     */
    public function set_destination($destination, $expectedDestination): void
    {
        $redirect = $this->createRedirect('/', $destination);

        $this->assertSame($expectedDestination, $redirect->getDestination());
    }

    /**
     * @test
     */
    public function get_last_accessed_at(): void
    {
        $redirect = $this->createRedirect('/', '/');
        $this->assertNull($redirect->getLastAccessed());

        $redirect->updateLastAccessed();
        $this->assertInstanceOf('DateTime', $redirect->getLastAccessed());
        $this->assertEqualsWithDelta(\time(), $redirect->getLastAccessed()->format('U'), 1);
    }

    /**
     * @test
     */
    public function increase_count(): void
    {
        $redirect = $this->createRedirect('/', '/');

        $this->assertSame(0, $redirect->getCount());

        $redirect->increaseCount();
        $this->assertSame(1, $redirect->getCount());

        $redirect->increaseCount(4);
        $this->assertSame(5, $redirect->getCount());
    }

    /**
     * @test
     */
    public function create_from_not_found(): void
    {
        $redirect = DummyRedirect::createFromNotFound(new DummyNotFound('/foo', 'https://example.com/foo'), '/baz');

        $this->assertSame('/foo', $redirect->getSource());
    }

    public function sourceProvider(): array
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

    public function destinationProvider(): array
    {
        return [
            ['/foo', '/foo'],
            ['foo', '/foo'],
            ['foo?bar=baz', '/foo?bar=baz'],
            ['http://www.example.com/foo', 'http://www.example.com/foo'],
            ['', null],
        ];
    }

    private function createRedirect(string $source, string $destination, bool $permanent = true): Redirect
    {
        return $this->getMockForAbstractClass(
            Redirect::class,
            [$source, $destination, $permanent]
        );
    }
}
