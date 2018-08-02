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
     */
    public function testSetSource($source, $expected)
    {
        $redirect = $this->createRedirect($source, '/foo');

        $this->assertSame($expected, $redirect->getSource());
    }

    /**
     * @dataProvider destinationProvider
     */
    public function testSetDestination($destination, $expectedDestination)
    {
        $redirect = $this->createRedirect('/', $destination);

        $this->assertSame($expectedDestination, $redirect->getDestination());
    }

    public function testGetLastAccessedAt()
    {
        $redirect = $this->createRedirect('/', '/');
        $this->assertNull($redirect->getLastAccessed());

        $redirect->updateLastAccessed();
        $this->assertInstanceOf('DateTime', $redirect->getLastAccessed());
        $this->assertEquals(time(), $redirect->getLastAccessed()->format('U'), '', 1);
    }

    public function testIncreaseCount()
    {
        $redirect = $this->createRedirect('/', '/');

        $this->assertSame(0, $redirect->getCount());

        $redirect->increaseCount();
        $this->assertSame(1, $redirect->getCount());

        $redirect->increaseCount(4);
        $this->assertSame(5, $redirect->getCount());
    }

    public function testCreateFromNotFound()
    {
        $redirect = DummyRedirect::createFromNotFound(new DummyNotFound('/foo', 'https://example.com/foo'), '/baz');

        $this->assertSame('/foo', $redirect->getSource());
    }

    public function sourceProvider()
    {
        return array(
            array('foo/bar', '/foo/bar'),
            array('/foo/bar/', '/foo/bar/'),
            array('foo', '/foo'),
            array('foo/bar ', '/foo/bar'),
            array(' foo/bar/', '/foo/bar/'),
            array('   /foo', '/foo'),
            array('http://www.example.com/foo', '/foo'),
            array('http://www.example.com/', '/'),
            array('http://www.example.com', '/'),
            array('foo/bar?baz=true', '/foo/bar'),
            array('http://www.example.com/foo?baz=bar&foo=baz', '/foo'),
            array('http://www.example.com/foo?baz=bar&foo=baz#baz', '/foo'),
            array('', null),
            array('   ', null),
            array('/', '/'),
        );
    }

    public function destinationProvider()
    {
        return array(
            array('/foo', '/foo'),
            array('foo', '/foo'),
            array('foo?bar=baz', '/foo?bar=baz'),
            array(null, null),
            array('', null),
            array(' ', null),
            array('   ', null),
            array('http://www.example.com/foo', 'http://www.example.com/foo'),
        );
    }

    /**
     * @return \Zenstruck\RedirectBundle\Model\Redirect
     */
    private function createRedirect($source, $destination, $permanent = true)
    {
        return $this->getMockForAbstractClass(
            'Zenstruck\RedirectBundle\Model\Redirect',
            array($source, $destination, $permanent)
        );
    }
}
