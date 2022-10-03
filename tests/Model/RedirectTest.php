<?php

namespace Zenstruck\RedirectBundle\Tests\Model;

use PHPUnit\Framework\TestCase;
use Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyNotFound;
use Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RedirectTest extends TestCase
{
    /**
     * @dataProvider sourceProvider
     *
     * @test
     */
    public function set_source($source, $expected)
    {
        $redirect = $this->createRedirect($source, '/foo');

        $this->assertSame($expected, $redirect->getSource());
    }

    /**
     * @dataProvider destinationProvider
     *
     * @test
     */
    public function set_destination($destination, $expectedDestination)
    {
        $redirect = $this->createRedirect('/', $destination);

        $this->assertSame($expectedDestination, $redirect->getDestination());
    }

    /**
     * @test
     */
    public function get_last_accessed_at()
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
    public function increase_count()
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
    public function create_from_not_found()
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
        ];
    }

    public function destinationProvider(): array
    {
        return [
            ['/foo', '/foo'],
            ['foo', '/foo'],
            ['foo?bar=baz', '/foo?bar=baz'],
            ['http://www.example.com/foo', 'http://www.example.com/foo'],
        ];
    }

    private function createRedirect(string $source, string $destination, bool $permanent = true): \Zenstruck\RedirectBundle\Model\Redirect
    {
        return $this->getMockForAbstractClass(
            'Zenstruck\RedirectBundle\Model\Redirect',
            [$source, $destination, $permanent]
        );
    }
}
