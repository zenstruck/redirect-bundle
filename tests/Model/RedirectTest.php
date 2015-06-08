<?php

namespace Zenstruck\RedirectBundle\Tests\Model;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RedirectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider sourceProvider
     */
    public function testSetSource($source, $expected)
    {
        $redirect = $this->createRedirect($source);

        $this->assertSame($expected, $redirect->getSource());
    }

    /**
     * @dataProvider destinationProvider
     */
    public function testSetDestination($destination, $expectedDestination, $expectedStatusCode)
    {
        $redirect = $this->createRedirect('/', $destination);

        $this->assertSame($expectedDestination, $redirect->getDestination());
        $this->assertSame($expectedStatusCode, $redirect->getStatusCode());
    }

    /**
     * @dataProvider statusCodeProvider
     */
    public function testSetStatusCode($statusCode, $expected)
    {
        $redirect = $this->createRedirect('/', null);
        $redirect->setStatusCode($statusCode);

        $this->assertSame($expected, $redirect->getStatusCode());
    }

    public function testGetLastAccessedAt()
    {
        $redirect = $this->createRedirect('/');
        $this->assertNull($redirect->getLastAccessed());

        $redirect->updateLastAccessed();
        $this->assertInstanceOf('DateTime', $redirect->getLastAccessed());
        $this->assertEquals(time(), $redirect->getLastAccessed()->format('U'));
    }

    public function testIncreaseCount()
    {
        $redirect = $this->createRedirect('/');

        $this->assertSame(0, $redirect->getCount());

        $redirect->increaseCount();
        $this->assertSame(1, $redirect->getCount());

        $redirect->increaseCount(4);
        $this->assertSame(5, $redirect->getCount());
    }

    public function testSetDestinationUpdatesStatusCode()
    {
        $redirect = $this->createRedirect('/foo');
        $this->assertSame(404, $redirect->getStatusCode());

        $redirect->setStatusCode(302);
        $redirect->setDestination('/bar');

        $this->assertSame(302, $redirect->getStatusCode());

        $redirect->setDestination(null);

        $this->assertSame(404, $redirect->getStatusCode());

        $redirect->setDestination('/foo');

        $this->assertSame(301, $redirect->getStatusCode());
    }

    public function testEmptyConstructor()
    {
        $redirect = $this->createRedirect();

        $this->assertNull($redirect->getSource());
        $this->assertNull($redirect->getDestination());
        $this->assertSame(404, $redirect->getStatusCode());
        $this->assertSame(0, $redirect->getCount());
        $this->assertNull($redirect->getLastAccessed());
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
            array('foo/bar?baz=true', '/foo/bar?baz=true'),
            array('http://www.example.com/foo?baz=bar&foo=baz', '/foo?baz=bar&foo=baz'),
            array('http://www.example.com/foo?baz=bar&foo=baz#baz', '/foo?baz=bar&foo=baz'),
            array('', null),
            array('   ', null),
            array('/', '/'),
        );
    }

    public function destinationProvider()
    {
        return array(
            array('/foo', '/foo', 301),
            array('foo', '/foo', 301),
            array('foo?bar=baz', '/foo?bar=baz', 301),
            array(null, null, 404),
            array('', null, 404),
            array(' ', null, 404),
            array('   ', null, 404),
            array('http://www.example.com/foo', 'http://www.example.com/foo', 301),
        );
    }

    public function statusCodeProvider()
    {
        return array(
            array(200, 200),
            array('200', 200),
            array('', 0),
            array('  ', 0),
        );
    }

    /**
     * @return \Zenstruck\RedirectBundle\Model\Redirect
     */
    private function createRedirect($source = null, $destination = null, $statusCode = 404)
    {
        return $this->getMockForAbstractClass(
            'Zenstruck\RedirectBundle\Model\Redirect',
            array($source, $destination, $statusCode)
        );
    }
}
